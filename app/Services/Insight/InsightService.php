<?php

namespace App\Services\Insight;

use App\Models\pinjaman;
use Carbon\CarbonImmutable;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/**
 * Angka untuk Dasbor utama & dasbor per produk.
 *
 * PENYALURAN, NOA PENYALURAN & PENDAPATAN mengikuti definisi catatan kantor (pivot "Tanggal Realisasi"):
 * - TomiDana aplikasi = pinjamen status approve/done (tidak dihapus); tanggal = pinjamen.created_at (pengajuan);
 *   nilai = pinjamen.nominal (kotor); perusahaan = perusahaan saat pinjaman diajukan (pinjamen.id_perusahaan,
 *   bukan perusahaan karyawan sekarang: karyawan yang pindah perusahaan tetap tercatat di perusahaan lamanya
 *   untuk pinjaman lama); NOA = 1 per pinjaman.
 * - TomiDana upload manual = invoice_generate_manuals (menu Buat Tagihan): tanggal = created_at; nilai = total_pinjaman;
 *   NOA = jumlah_peminjam; pemberi dana lewat company_id → list_perusahaans.id_lender (kosong = tidak ikut saat
 *   filter pemberi dana dipilih).
 * - TomiGaji = salary_financings status uploaded_transfer/uploaded_bayar/lunas; tanggal = created_at;
 *   nilai = loan_amount; NOA = total_borrowers (per karyawan).
 * - Tomi Invoice = per termin pembayaran ke pelanggan: tanggal = finance_vendor_payment_proof_date_n;
 *   nilai = approved_financing_amount × % termin (vendor_payment_terms, lewat vendor_payment_term_id atau vendor_id);
 *   NOA = 1 per termin. Kolom finance_vendor_payment_proof_amount_n tidak dipakai (isinya total batch).
 * - TomiGawai = gadget_installments status approved/paid (waktu: created_at); belum ada di catatan kantor.
 * - Pendapatan = bagian TOMI saja: TomiDana 1% × nominal + biaya_layanan; upload manual = total_fee;
 *   TomiGaji 1,5% × loan_amount; Tomi Invoice 1% × nilai termin. Persentase belum tersimpan di DB (lihat konstanta FEE_*).
 *
 * POSISI & PEMBAYARAN (belum ada di catatan kantor, definisi lama):
 * - Pemberi dana TomiDana = pinjamen.id_lender (pinjaman) / list_perusahaans.id_lender (pembayaran tagihan).
 *   Kolom disbursements.lender_id & repayments.lender_id (atribusi lender saat cair/bayar) belum ada di skema ini.
 * - TomiDana "pembayaran" = repayments.amount_total (waktu: repayments.tanggal_pembayaran).
 * - TomiDana "sisa pinjaman" pada akhir periode = cicilan belum lunas × total_angsuran_perbulan
 *   (cicilan yang lunas setelah akhir periode dihitung belum lunas, pakai jatuh_tempos.updated_at).
 *   Upload manual tidak punya data cicilan, jadi tidak ikut sisa / NOA outstanding / tunggakan.
 * - Funnel TomiDana dihitung dari pengajuan yang tersimpan. Pengajuan yang gagal OTP dihapus permanen
 *   oleh cron hapuspinjaman, jadi tahap awal funnel lebih kecil dari kenyataan.
 * - TomiGaji sisa = pengajuan cair yang belum lunas (waktu cair: transfer_date, lalu created_at);
 *   dibayar = status lunas (waktu: payment_date, lalu updated_at).
 * - Tomi Invoice sisa = pembiayaan disetujui (approved_at) − termin yang sudah ditagihkan ke mitra.
 *
 * NOA outstanding dihitung per akun pinjaman:
 * - NOA outstanding = jumlah akun yang masih berjalan pada akhir periode, definisinya sama dengan nominal "sisa":
 *   TomiDana pinjaman yang masih punya cicilan belum lunas; TomiGaji pengajuan cair yang belum lunas;
 *   Tomi Invoice tagihan disetujui yang terminnya belum ditagihkan penuh; TomiGawai cicilan berstatus approved.
 */
class InsightService
{
    public const PRODUCTS = [
        'tomidana' => ['name' => 'TomiDana', 'desc' => 'Pinjaman payroll karyawan', 'slot' => 1],
        'tomigaji' => ['name' => 'TomiGaji', 'desc' => 'Pembiayaan gaji perusahaan', 'slot' => 2],
        'tomiinvoice' => ['name' => 'Tomi Invoice', 'desc' => 'Pembiayaan tagihan mitra', 'slot' => 3],
        'tomigawai' => ['name' => 'TomiGawai', 'desc' => 'Cicilan gadget karyawan', 'slot' => 4],
    ];

    /** Fee bagian TOMI (catatan kantor). Belum tersimpan di DB: tenor_pinjamen.fee_tomi = 0, salary_financings.total_fee = 5%. */
    public const FEE_TOMIDANA = 0.01;

    public const FEE_TOMIGAJI = 0.015;

    public const FEE_TOMIINVOICE = 0.01;

    /** Pinjaman TomiDana yang dihitung sebagai penyaluran. */
    public const DANA_STATUS = ['approve', 'done'];

    /**
     * Perusahaan pinjaman TomiDana = perusahaan saat pinjaman diajukan (disimpan PinjamanController saat pengajuan).
     * Jangan pakai list_karyawans.id_list_perusahaan: itu perusahaan karyawan SEKARANG dan berubah kalau karyawan pindah.
     */
    public const DANA_COMPANY = 'p.id_perusahaan';

    public const DANA_FEE_SQL = 'COALESCE(p.nominal,0) * '.self::FEE_TOMIDANA;

    public const GAJI_FEE_SQL = 'COALESCE(s.loan_amount,0) * '.self::FEE_TOMIGAJI;

    /** Nilai satu termin Tomi Invoice (alias it = invoiceTerms()). */
    public const INVOICE_TERM_SQL = 'COALESCE(it.approved_financing_amount,0) * it.pct / 100';

    public const INVOICE_FEE_SQL = '(COALESCE(it.approved_financing_amount,0) * it.pct / 100) * '.self::FEE_TOMIINVOICE;

    public const GAJI_DISBURSED = ['uploaded_transfer', 'uploaded_bayar', 'lunas'];

    public function earliest(): CarbonImmutable
    {
        $dates = array_filter([
            DB::table('pinjamen')->whereNull('deleted_at')->min('created_at'),
            DB::table('invoice_generate_manuals')->min('created_at'),
            DB::table('salary_financings')->min('created_at'),
            DB::table('financing_request_invoices')->whereNull('deleted_at')->min('created_at'),
            DB::table('gadget_installments')->min('created_at'),
        ]);

        return $dates ? CarbonImmutable::parse(min($dates))->startOfDay() : CarbonImmutable::now()->startOfYear();
    }

    /* ===================================================================== */
    /* Dashboard utama */
    /* ===================================================================== */

    public function overview(InsightPeriod $period, ?int $lenderId): array
    {
        $cur = $this->allTotals($period, $lenderId, true);
        $prevPeriod = $period->previous();
        $prev = $prevPeriod ? $this->allTotals($prevPeriod, $lenderId, false) : null;

        $sum = fn (array $set, string $key) => array_sum(array_map(fn ($p) => $p['totals'][$key] ?? 0, $set));
        $buckets = $period->buckets();
        $sumSeries = function (string $key) use ($cur, $buckets) {
            $out = array_fill(0, count($buckets), 0.0);
            foreach ($cur as $p) {
                if (! array_key_exists($key, $p['series']) || $p['series'][$key] === null) {
                    return null; // deret tidak tersedia (mis. NOA outstanding per jam)
                }
                foreach ($p['series'][$key] as $i => $v) {
                    $out[$i] += $v;
                }
            }

            return $out;
        };

        $totals = [];
        foreach (['disbursed', 'count', 'noa_out', 'repaid', 'outstanding', 'revenue'] as $k) {
            $totals[$k] = ['value' => $sum($cur, $k), 'prev' => $prev ? $sum($prev, $k) : null];
        }

        $disbursedSeries = $sumSeries('disbursed');
        $danaHealth = $this->danaHealth($period, $lenderId);

        return [
            'hero' => [
                'value' => $totals['disbursed']['value'],
                'prev' => $totals['disbursed']['prev'],
                'count' => $totals['count']['value'],
                'avg' => $totals['count']['value'] > 0 ? $totals['disbursed']['value'] / $totals['count']['value'] : null,
                'series' => $disbursedSeries,
                'summary' => $this->summarySentence($cur, $prev, $period),
            ],
            'kpis' => [
                $this->kpi('noa', 'NOA penyaluran', $totals['count'], 'num', true, $sumSeries('noa'), 'TomiDana per peminjam, TomiGaji per karyawan, Tomi Invoice per termin'),
                $this->kpi('noa_out', 'NOA outstanding', $totals['noa_out'], 'num', true, $sumSeries('noa_out'), 'Akun belum lunas per '.InsightPeriod::date($period->to)),
                $this->kpi('outstanding', 'Sisa pinjaman berjalan', $totals['outstanding'], 'rp', true, $sumSeries('outstanding'), 'Per '.InsightPeriod::date($period->to)),
                $this->kpi('repaid', 'Pembayaran diterima', $totals['repaid'], 'rp', true, $sumSeries('repaid')),
                $this->kpi('revenue', 'Pendapatan TOMI', $totals['revenue'], 'rp', true, $sumSeries('revenue'), 'Bagian TOMI, tanpa bagian pemberi dana'),
                $this->kpi('late', 'Tunggakan TomiDana', ['value' => $danaHealth['late'], 'prev' => null], 'rp', false, null, 'Cicilan lewat jatuh tempo'),
            ],
            'trend' => [
                'categories' => array_column($buckets, 'label'),
                'series' => array_values(array_map(fn ($key, $p) => [
                    'key' => $key, 'name' => self::PRODUCTS[$key]['name'], 'slot' => self::PRODUCTS[$key]['slot'],
                    'data' => array_map(fn ($v) => round($v), $p['series']['disbursed']),
                ], array_keys($cur), $cur)),
                'noa' => array_map('intval', $sumSeries('noa')),
            ],
            'products' => array_values(array_map(fn ($key, $p) => [
                'key' => $key,
                'name' => self::PRODUCTS[$key]['name'],
                'desc' => self::PRODUCTS[$key]['desc'],
                'slot' => self::PRODUCTS[$key]['slot'],
                'disbursed' => $p['totals']['disbursed'],
                'prev' => $prev[$key]['totals']['disbursed'] ?? null,
                'count' => $p['totals']['count'],
                'count_label' => $p['count_label'],
                'noa_out' => $p['totals']['noa_out'],
                'outstanding' => $p['totals']['outstanding'],
                'series' => $p['series']['disbursed'],
                'url' => route('admin.insight.product', $key),
            ], array_keys($cur), $cur)),
            'lenders' => $this->lenderSplit($period, $lenderId, $totals['disbursed']['value']),
            'revenue' => $this->revenueBlock($cur, $prev, $buckets),
            'companies' => $this->topCompanies($period, $lenderId),
            'health' => $danaHealth,
            'feed' => $this->activityFeed($lenderId),
        ];
    }

    /* ===================================================================== */
    /* Dashboard per produk */
    /* ===================================================================== */

    public function product(string $key, InsightPeriod $period, ?int $lenderId): array
    {
        $cur = $this->productTotals($key, $period, $lenderId, true);
        $prevPeriod = $period->previous();
        $prev = $prevPeriod ? $this->productTotals($key, $prevPeriod, $lenderId, false) : null;
        $t = fn (string $k) => ['value' => $cur['totals'][$k] ?? 0, 'prev' => $prev ? ($prev['totals'][$k] ?? 0) : null];
        $buckets = $period->buckets();

        $approval = fn ($set) => ($set['totals']['applications'] ?? 0) > 0 ? $set['totals']['approved'] / $set['totals']['applications'] : null;
        $base = [
            'product' => ['key' => $key] + self::PRODUCTS[$key],
            'trend' => [
                'categories' => array_column($buckets, 'label'),
                'series' => [
                    ['name' => $cur['labels']['disbursed'], 'slot' => 1, 'data' => array_map('round', $cur['series']['disbursed'])],
                    ['name' => $cur['labels']['repaid'], 'slot' => 2, 'data' => array_map('round', $cur['series']['repaid'])],
                ],
                'noa' => array_map('intval', $cur['series']['noa']),
                'noa_out' => $cur['series']['noa_out'] === null ? null : array_map('intval', $cur['series']['noa_out']),
                'outstanding' => $cur['series']['outstanding'] === null ? null : array_map('round', $cur['series']['outstanding']),
            ],
            'revenue' => $this->revenueBlock([$key => $cur], $prev ? [$key => $prev] : null, $buckets),
        ];

        $kpis = [
            $this->kpi('disbursed', $cur['labels']['disbursed'], $t('disbursed'), 'rp', true, $cur['series']['disbursed']),
            $this->kpi('noa', 'NOA penyaluran', $t('count'), 'num', true, $cur['series']['noa'], $cur['noa_hint']),
            $this->kpi('outstanding', 'Sisa pembiayaan berjalan', $t('outstanding'), 'rp', true, $cur['series']['outstanding'], 'Per '.InsightPeriod::date($period->to)),
            $this->kpi('noa_out', 'NOA outstanding', $t('noa_out'), 'num', true, $cur['series']['noa_out'], ucfirst($cur['count_label']).' belum lunas per '.InsightPeriod::date($period->to)),
            $this->kpi('applications', 'Pengajuan masuk', $t('applications'), 'num', true, $cur['series']['applications'] ?? null),
            $this->kpi('approval', 'Tingkat persetujuan', ['value' => $approval($cur), 'prev' => $prev ? $approval($prev) : null], 'pct', true, null),
            $this->kpi('repaid', $cur['labels']['repaid'], $t('repaid'), 'rp', true, $cur['series']['repaid']),
        ];

        return $base + match ($key) {
            'tomidana' => $this->danaDetail($period, $lenderId, $kpis),
            'tomigaji' => $this->gajiDetail($period, $lenderId, $kpis, $cur),
            'tomiinvoice' => $this->invoiceDetail($period, $lenderId, $kpis, $cur),
            default => $this->gawaiDetail($period, $lenderId, $kpis, $cur),
        };
    }

    /* ===================================================================== */
    /* Total & deret per produk */
    /* ===================================================================== */

    private function allTotals(InsightPeriod $period, ?int $lenderId, bool $withSeries): array
    {
        $out = [];
        foreach (array_keys(self::PRODUCTS) as $key) {
            $out[$key] = $this->productTotals($key, $period, $lenderId, $withSeries);
        }

        return $out;
    }

    private function productTotals(string $key, InsightPeriod $period, ?int $lenderId, bool $withSeries): array
    {
        return match ($key) {
            'tomidana' => $this->danaTotals($period, $lenderId, $withSeries),
            'tomigaji' => $this->gajiTotals($period, $lenderId, $withSeries),
            'tomiinvoice' => $this->invoiceTotals($period, $lenderId, $withSeries),
            default => $this->gawaiTotals($period, $lenderId, $withSeries),
        };
    }

    /**
     * Posisi per akhir tiap bucket (NOA outstanding, sisa berjalan). Null bila granularitas jam
     * atau bucket terlalu banyak (mis. "Semua waktu" bertahun-tahun per hari).
     */
    private function noaOutSeries(InsightPeriod $period, callable $countAt): ?array
    {
        $ends = $period->bucketEnds();
        if ($ends === null || count($ends) > 36) {
            return null;
        }

        return array_map(fn (CarbonImmutable $at) => (float) $countAt($at), $ends);
    }

    private function positionSeries(InsightPeriod $period, callable $valueAt): ?array
    {
        return $this->noaOutSeries($period, $valueAt);
    }

    /** Penyaluran TomiDana dari aplikasi (alias p = pinjamen, k = list_karyawans). */
    public function danaLoans(InsightPeriod $period, ?int $lenderId): Builder
    {
        return DB::table('pinjamen as p')
            ->leftJoin('list_karyawans as k', 'k.id', '=', 'p.id_karyawan')
            ->whereNull('p.deleted_at')
            ->whereIn('p.status', self::DANA_STATUS)
            ->whereBetween('p.created_at', [$period->start(), $period->end()])
            ->when($lenderId, fn ($q) => $q->where('p.id_lender', $lenderId));
    }

    /** Penyaluran TomiDana dari upload manual menu Buat Tagihan (alias m, lpm = perusahaan bila company_id terisi). */
    public function manualUploads(InsightPeriod $period, ?int $lenderId): Builder
    {
        return DB::table('invoice_generate_manuals as m')
            ->leftJoin('list_perusahaans as lpm', 'lpm.id', '=', 'm.company_id')
            ->whereBetween('m.created_at', [$period->start(), $period->end()])
            ->when($lenderId, fn ($q) => $q->where('lpm.id_lender', $lenderId));
    }

    public function danaRepayments(InsightPeriod $period, ?int $lenderId): Builder
    {
        return DB::table('repayments as r')
            ->leftJoin('invoicing_pinjamen as i', 'i.id', '=', 'r.id_invoicing')
            ->leftJoin('list_perusahaans as lp', 'lp.id', '=', 'i.id_list_perusahaan')
            ->whereBetween('r.tanggal_pembayaran', [$period->start(), $period->end()])
            ->when($lenderId, fn ($q) => $q->where('lp.id_lender', $lenderId));
    }

    public function danaApplications(InsightPeriod $period, ?int $lenderId): Builder
    {
        return DB::table('pinjamen as p')
            ->whereNull('p.deleted_at')
            ->whereBetween('p.created_at', [$period->start(), $period->end()])
            ->when($lenderId, fn ($q) => $q->where('p.id_lender', $lenderId));
    }

    private function danaTotals(InsightPeriod $period, ?int $lenderId, bool $withSeries): array
    {
        $loans = fn () => $this->danaLoans($period, $lenderId);
        $manual = fn () => $this->manualUploads($period, $lenderId);
        $row = $loans()->selectRaw('COUNT(*) c, COALESCE(SUM(p.nominal),0) amt, COALESCE(SUM('.self::DANA_FEE_SQL.'),0) fee, COALESCE(SUM(p.biaya_layanan),0) lay')->first();
        $m = $manual()->selectRaw('COALESCE(SUM(m.jumlah_peminjam),0) c, COALESCE(SUM(m.total_pinjaman),0) amt, COALESCE(SUM(m.total_fee),0) fee')->first();
        $apps = $this->danaApplications($period, $lenderId)->count();
        $approved = $this->danaApplications($period, $lenderId)
            ->whereExists(fn ($q) => $q->from('disbursements as dx')->whereColumn('dx.pinjaman_id', 'p.id'))
            ->count();

        $parts = [
            'fee_dana' => ['Fee TOMI 1% TomiDana', $loans, 'p.created_at', self::DANA_FEE_SQL, (float) $row->fee],
            'layanan' => ['Biaya layanan TomiDana', $loans, 'p.created_at', 'COALESCE(p.biaya_layanan,0)', (float) $row->lay],
            'fee_manual' => ['Fee upload manual (Buat Tagihan)', $manual, 'm.created_at', 'COALESCE(m.total_fee,0)', (float) $m->fee],
        ];
        $revenue = [];
        foreach ($parts as $rk => [$label, $q, $date, $expr, $total]) {
            $revenue[$rk] = ['label' => $label, 'product' => 'tomidana', 'total' => $total,
                'series' => $withSeries ? $this->series($q(), $date, $expr, $period) : []];
        }

        return [
            'labels' => ['disbursed' => 'Penyaluran', 'repaid' => 'Pembayaran diterima'],
            'count_label' => 'pinjaman',
            'noa_hint' => 'Per pinjaman aplikasi + jumlah peminjam upload manual',
            'totals' => [
                'disbursed' => (float) $row->amt + (float) $m->amt,
                'count' => (int) $row->c + (int) $m->c,
                'noa_out' => $this->danaNoaOutstanding($period->to, $lenderId),
                'repaid' => (float) $this->danaRepayments($period, $lenderId)->sum('r.amount_total'),
                'outstanding' => $this->danaOutstanding($period->to, $lenderId),
                'revenue' => array_sum(array_column($revenue, 'total')),
                'applications' => $apps,
                'approved' => $approved,
            ],
            'series' => $withSeries ? [
                'disbursed' => $this->addSeries($this->series($loans(), 'p.created_at', 'p.nominal', $period), $this->series($manual(), 'm.created_at', 'm.total_pinjaman', $period)),
                'noa' => $this->addSeries($this->series($loans(), 'p.created_at', '1', $period), $this->series($manual(), 'm.created_at', 'm.jumlah_peminjam', $period)),
                'noa_out' => $this->noaOutSeries($period, fn ($at) => $this->danaNoaOutstanding($at, $lenderId)),
                'outstanding' => $this->positionSeries($period, fn ($at) => $this->danaOutstanding($at, $lenderId)),
                'repaid' => $this->series($this->danaRepayments($period, $lenderId), 'r.tanggal_pembayaran', 'r.amount_total', $period),
                'applications' => $this->series($this->danaApplications($period, $lenderId), 'p.created_at', '1', $period),
                'revenue' => $this->addSeries(...array_column($revenue, 'series')),
            ] : [],
            'revenue' => $revenue,
        ];
    }

    /** Cicilan TomiDana yang belum lunas pada akhir hari $at (lunas sesudahnya dihitung belum). */
    public function danaInstallmentsOpenAt(CarbonImmutable $at, ?int $lenderId): Builder
    {
        $end = $at->endOfDay()->toDateTimeString();

        return DB::table('jatuh_tempos as jt')
            ->join('pinjamen as p', 'p.id', '=', 'jt.id_pinjaman')
            ->join('disbursements as d', 'd.pinjaman_id', '=', 'p.id')
            ->whereNull('p.deleted_at')
            ->where('d.created_at', '<=', $end)
            ->where(fn ($q) => $q->where('jt.status', '!=', 'lunas')->orWhere('jt.updated_at', '>', $end))
            ->when($lenderId, fn ($q) => $q->where('p.id_lender', $lenderId));
    }

    private function danaOutstanding(CarbonImmutable $at, ?int $lenderId): float
    {
        return (float) $this->danaInstallmentsOpenAt($at, $lenderId)->sum('p.total_angsuran_perbulan');
    }

    /** NOA outstanding TomiDana: pinjaman yang masih punya cicilan belum lunas pada $at. */
    private function danaNoaOutstanding(CarbonImmutable $at, ?int $lenderId): int
    {
        return (int) $this->danaInstallmentsOpenAt($at, $lenderId)->distinct()->count('p.id');
    }

    private function danaHealth(InsightPeriod $period, ?int $lenderId): array
    {
        $at = $period->to;
        $base = fn () => $this->danaInstallmentsOpenAt($at, $lenderId);
        $total = (float) $base()->sum('p.total_angsuran_perbulan');
        $late = (float) $base()->where('jt.jatuh_tempo', '<', $at->toDateString())->sum('p.total_angsuran_perbulan');
        $due = (float) $base()->whereBetween('jt.jatuh_tempo', [$at->toDateString(), $at->addDays(7)->toDateString()])->sum('p.total_angsuran_perbulan');

        return [
            'total' => $total,
            'late' => $late,
            'as_of' => InsightPeriod::date($at),
            'parts' => [
                ['tone' => 'good', 'label' => 'Lancar', 'value' => max(0, $total - $late - $due)],
                ['tone' => 'warn', 'label' => 'Jatuh tempo ≤ 7 hari', 'value' => $due],
                ['tone' => 'crit', 'label' => 'Terlambat', 'value' => $late],
            ],
        ];
    }

    public function gajiQuery(?int $lenderId): Builder
    {
        return DB::table('salary_financings as s')->when($lenderId, fn ($q) => $q->where('s.lender_id', $lenderId));
    }

    /** Penyaluran TomiGaji: pengajuan yang sudah ditransfer, menurut tanggal pengajuan (catatan kantor). */
    public function gajiDisbursed(InsightPeriod $period, ?int $lenderId): Builder
    {
        return $this->gajiQuery($lenderId)->whereIn('s.status', self::GAJI_DISBURSED)
            ->whereBetween('s.created_at', [$period->start(), $period->end()]);
    }

    private function gajiTotals(InsightPeriod $period, ?int $lenderId, bool $withSeries): array
    {
        $dateDisb = 's.created_at';
        $datePaid = 'COALESCE(s.payment_date, s.updated_at)';
        $disb = fn () => $this->gajiDisbursed($period, $lenderId);
        $paid = fn () => $this->gajiQuery($lenderId)->where('s.status', 'lunas')
            ->whereRaw("$datePaid BETWEEN ? AND ?", [$period->start(), $period->end()]);
        $apps = fn () => $this->gajiQuery($lenderId)->whereBetween('s.created_at', [$period->start(), $period->end()]);
        $openAt = fn (string $end) => $this->gajiOpenAt($end, $lenderId);
        $outstanding = (float) $openAt($period->end())->sum('s.total_dibayarkan');

        return [
            'labels' => ['disbursed' => 'Penyaluran', 'repaid' => 'Pelunasan diterima'],
            'count_label' => 'pengajuan',
            'noa_hint' => 'Per karyawan dibiayai',
            'totals' => [
                'disbursed' => (float) $disb()->sum('s.loan_amount'),
                'count' => (int) $disb()->sum('s.total_borrowers'),
                'noa_out' => $openAt($period->end())->count(),
                'repaid' => (float) $paid()->sum('s.total_dibayarkan'),
                'outstanding' => $outstanding,
                'revenue' => (float) $disb()->sum(DB::raw(self::GAJI_FEE_SQL)),
                'applications' => $apps()->count(),
                'approved' => $apps()->whereIn('s.status', self::GAJI_DISBURSED)->count(),
            ],
            'series' => $withSeries ? [
                'disbursed' => $this->series($disb(), $dateDisb, 's.loan_amount', $period),
                'noa' => $this->series($disb(), $dateDisb, 's.total_borrowers', $period),
                'noa_out' => $this->noaOutSeries($period, fn ($at) => $openAt($at->endOfDay()->toDateTimeString())->count()),
                'outstanding' => $this->positionSeries($period, fn ($at) => (float) $openAt($at->endOfDay()->toDateTimeString())->sum('s.total_dibayarkan')),
                'repaid' => $this->series($paid(), $datePaid, 's.total_dibayarkan', $period),
                'applications' => $this->series($apps(), 's.created_at', '1', $period),
                'revenue' => $this->series($disb(), $dateDisb, self::GAJI_FEE_SQL, $period),
            ] : [],
            'revenue' => ['fee_gaji' => ['label' => 'Fee TOMI 1,5% TomiGaji', 'product' => 'tomigaji', 'total' => (float) $disb()->sum(DB::raw(self::GAJI_FEE_SQL)),
                'series' => $withSeries ? $this->series($disb(), $dateDisb, self::GAJI_FEE_SQL, $period) : []]],
        ];
    }

    /** Pengajuan TomiGaji yang sudah cair dan belum lunas pada $end (datetime). */
    public function gajiOpenAt(string $end, ?int $lenderId): Builder
    {
        return $this->gajiQuery($lenderId)->whereIn('s.status', self::GAJI_DISBURSED)
            ->whereRaw('COALESCE(s.transfer_date, s.created_at) <= ?', [$end])
            ->where(fn ($q) => $q->where('s.status', '!=', 'lunas')->orWhereRaw('COALESCE(s.payment_date, s.updated_at) > ?', [$end]));
    }

    /** SQL termin Tomi Invoice yang sudah ditagihkan s.d. tanggal (butuh 3 binding tanggal yang sama). */
    public const INVOICE_BILLED_SQL = '(CASE WHEN f.finance_invoice_term_date_1 IS NOT NULL AND f.finance_invoice_term_date_1 <= ? THEN COALESCE(f.finance_invoice_term_amount_1, 0) ELSE 0 END'
        .' + CASE WHEN f.finance_invoice_term_date_2 IS NOT NULL AND f.finance_invoice_term_date_2 <= ? THEN COALESCE(f.finance_invoice_term_amount_2, 0) ELSE 0 END'
        .' + CASE WHEN f.finance_invoice_term_date_3 IS NOT NULL AND f.finance_invoice_term_date_3 <= ? THEN COALESCE(f.finance_invoice_term_amount_3, 0) ELSE 0 END)';

    /** Tagihan Tomi Invoice disetujui s.d. $at yang terminnya belum ditagihkan penuh. */
    public function invoiceOpenAt(string $at, ?int $lenderId): Builder
    {
        return $this->invoiceQuery($lenderId)->where('f.analyst_decision', 'approved')->where('f.approved_at', '<=', $at)
            ->whereRaw('COALESCE(f.approved_financing_amount, 0) > '.self::INVOICE_BILLED_SQL, [$at, $at, $at]);
    }

    /** Cicilan TomiGawai yang masih berjalan (status approved) dan dibuat s.d. $at. */
    public function gawaiOpenAt(string $at, ?int $lenderId): Builder
    {
        return $this->gawaiQuery($lenderId)->where('g.status', 'approved')->where('g.created_at', '<=', $at);
    }

    public function invoiceQuery(?int $lenderId): Builder
    {
        return DB::table('financing_request_invoices as f')->whereNull('f.deleted_at')
            ->when($lenderId, fn ($q) => $q->where('f.lender_id', $lenderId));
    }

    /**
     * Penyaluran Tomi Invoice per termin pembayaran ke pelanggan (alias it): satu baris per termin yang tanggal bayarnya terisi.
     * Kolom: id, lender_id, vendor_id, client_id, purchase_order_number, approved_financing_amount, term_no, pct, dt.
     * % termin dari vendor_payment_term_id, lalu termin aktif vendor; tanpa data termin → termin 1 = 100%.
     */
    public function invoiceTerms(InsightPeriod $period, ?int $lenderId): Builder
    {
        $union = null;
        foreach ([1, 2, 3] as $n) {
            $pct = "COALESCE(vt.term_{$n}_percentage, (SELECT vv.term_{$n}_percentage FROM vendor_payment_terms vv"
                .' WHERE vv.vendor_id = f.vendor_id AND vv.deleted_at IS NULL ORDER BY vv.id DESC LIMIT 1), '.($n === 1 ? 100 : 0).')';
            $q = DB::table('financing_request_invoices as f')
                ->leftJoin('vendor_payment_terms as vt', 'vt.id', '=', 'f.vendor_payment_term_id')
                ->whereNull('f.deleted_at')
                ->where('f.analyst_decision', 'approved')
                ->whereNotNull("f.finance_vendor_payment_proof_date_$n")
                ->selectRaw("f.id, f.lender_id, f.vendor_id, f.client_id, f.purchase_order_number, f.approved_financing_amount, $n as term_no, $pct as pct, f.finance_vendor_payment_proof_date_$n as dt");
            $union = $union ? $union->unionAll($q) : $q;
        }

        return DB::query()->fromSub($union, 'it')
            ->where('it.pct', '>', 0)
            ->whereBetween('it.dt', [$period->start(), $period->end()])
            ->when($lenderId, fn ($q) => $q->where('it.lender_id', $lenderId));
    }

    private function invoiceTotals(InsightPeriod $period, ?int $lenderId, bool $withSeries): array
    {
        $paidOut = fn () => $this->invoiceTerms($period, $lenderId);
        $apps = fn () => $this->invoiceQuery($lenderId)->whereBetween('f.created_at', [$period->start(), $period->end()]);
        $termSql = [];
        $termBindings = [];
        foreach ([1, 2, 3] as $n) {
            $termSql[] = "SELECT f.lender_id, f.finance_invoice_term_date_$n dt, f.finance_invoice_term_amount_$n amt FROM financing_request_invoices f WHERE f.deleted_at IS NULL AND f.finance_invoice_term_date_$n IS NOT NULL";
        }
        $terms = fn () => DB::query()->fromSub(implode(' UNION ALL ', $termSql), 't')
            ->whereBetween('t.dt', [$period->start(), $period->end()])
            ->when($lenderId, fn ($q) => $q->where('t.lender_id', $lenderId));
        $end = $period->end();
        // NOA outstanding: tagihan disetujui yang termin tertagih (s.d. $at) masih kurang dari nilai pembiayaan
        $openAt = fn (string $at) => $this->invoiceOpenAt($at, $lenderId);
        // Sisa berjalan pada $at = pembiayaan disetujui s.d. $at − termin yang sudah ditagihkan s.d. $at
        $outstandingAt = function (string $at) use ($lenderId, $termSql) {
            $openApproved = (float) $this->invoiceQuery($lenderId)->where('f.analyst_decision', 'approved')->where('f.approved_at', '<=', $at)->sum('f.approved_financing_amount');
            $billedBefore = (float) DB::query()->fromSub(implode(' UNION ALL ', $termSql), 't')->where('t.dt', '<=', $at)
                ->when($lenderId, fn ($q) => $q->where('t.lender_id', $lenderId))->sum('t.amt');

            return max(0, $openApproved - $billedBefore);
        };

        return [
            'labels' => ['disbursed' => 'Dibayar ke pelanggan', 'repaid' => 'Termin ditagihkan'],
            'count_label' => 'tagihan',
            'noa_hint' => 'Per termin pembayaran ke pelanggan',
            'totals' => [
                'disbursed' => (float) $paidOut()->sum(DB::raw(self::INVOICE_TERM_SQL)),
                'count' => $paidOut()->count(),
                'noa_out' => $openAt($end)->count(),
                'repaid' => (float) $terms()->sum('t.amt'),
                'outstanding' => $outstandingAt($end),
                'revenue' => (float) $paidOut()->sum(DB::raw(self::INVOICE_FEE_SQL)),
                'applications' => $apps()->count(),
                'approved' => $apps()->where('f.analyst_decision', 'approved')->count(),
            ],
            'series' => $withSeries ? [
                'disbursed' => $this->series($paidOut(), 'it.dt', self::INVOICE_TERM_SQL, $period),
                'noa' => $this->series($paidOut(), 'it.dt', '1', $period),
                'noa_out' => $this->noaOutSeries($period, fn ($at) => $openAt($at->endOfDay()->toDateTimeString())->count()),
                'outstanding' => $this->positionSeries($period, fn ($at) => $outstandingAt($at->endOfDay()->toDateTimeString())),
                'repaid' => $this->series($terms(), 't.dt', 't.amt', $period),
                'applications' => $this->series($apps(), 'f.created_at', '1', $period),
                'revenue' => $this->series($paidOut(), 'it.dt', self::INVOICE_FEE_SQL, $period),
            ] : [],
            'revenue' => ['fee_invoice' => ['label' => 'Fee TOMI 1% Tomi Invoice', 'product' => 'tomiinvoice', 'total' => (float) $paidOut()->sum(DB::raw(self::INVOICE_FEE_SQL)),
                'series' => $withSeries ? $this->series($paidOut(), 'it.dt', self::INVOICE_FEE_SQL, $period) : []]],
        ];
    }

    public function gawaiQuery(?int $lenderId): Builder
    {
        return DB::table('gadget_installments as g')->when($lenderId, fn ($q) => $q->where('g.lender_id', $lenderId));
    }

    private function gawaiTotals(InsightPeriod $period, ?int $lenderId, bool $withSeries): array
    {
        $ok = fn () => $this->gawaiQuery($lenderId)->whereIn('g.status', ['approved', 'paid'])->whereBetween('g.created_at', [$period->start(), $period->end()]);
        $openAt = fn (string $at) => $this->gawaiOpenAt($at, $lenderId);
        $apps = fn () => $this->gawaiQuery($lenderId)->whereBetween('g.created_at', [$period->start(), $period->end()]);

        return [
            'labels' => ['disbursed' => 'Cicilan disetujui', 'repaid' => 'Cicilan lunas'],
            'count_label' => 'cicilan',
            'noa_hint' => '1 akun = 1 cicilan disetujui',
            'totals' => [
                'disbursed' => (float) $ok()->sum('g.total_amount'),
                'count' => $ok()->count(),
                'noa_out' => $openAt($period->end())->count(),
                'repaid' => (float) $this->gawaiQuery($lenderId)->where('g.status', 'paid')->whereBetween('g.updated_at', [$period->start(), $period->end()])->sum('g.total_amount'),
                'outstanding' => (float) $openAt($period->end())->sum('g.total_amount'),
                'revenue' => 0.0,
                'applications' => $apps()->count(),
                'approved' => $apps()->whereIn('g.status', ['approved', 'paid'])->count(),
            ],
            'series' => $withSeries ? [
                'disbursed' => $this->series($ok(), 'g.created_at', 'g.total_amount', $period),
                'noa' => $this->series($ok(), 'g.created_at', '1', $period),
                'noa_out' => $this->noaOutSeries($period, fn ($at) => $openAt($at->endOfDay()->toDateTimeString())->count()),
                'outstanding' => $this->positionSeries($period, fn ($at) => (float) $openAt($at->endOfDay()->toDateTimeString())->sum('g.total_amount')),
                'repaid' => $this->series($this->gawaiQuery($lenderId)->where('g.status', 'paid')->whereBetween('g.updated_at', [$period->start(), $period->end()]), 'g.updated_at', 'g.total_amount', $period),
                'applications' => $this->series($apps(), 'g.created_at', '1', $period),
                'revenue' => array_fill(0, count($period->buckets()), 0.0),
            ] : [],
            'revenue' => [],
            'revenue_note' => 'Pendapatan TomiGawai belum punya definisi.',
        ];
    }

    /* ===================================================================== */
    /* Alur pengajuan (dipakai grafik alur & panel rincian) */
    /* ===================================================================== */

    /** Pengajuan suatu produk yang masuk pada periode (titik awal alur). */
    public function applicationsQuery(string $product, InsightPeriod $period, ?int $lenderId): Builder
    {
        return match ($product) {
            'tomidana' => $this->danaApplications($period, $lenderId),
            'tomigaji' => $this->gajiQuery($lenderId)->whereBetween('s.created_at', [$period->start(), $period->end()]),
            'tomiinvoice' => $this->invoiceQuery($lenderId)->whereBetween('f.created_at', [$period->start(), $period->end()]),
            default => $this->gawaiQuery($lenderId)->whereBetween('g.created_at', [$period->start(), $period->end()]),
        };
    }

    /**
     * Tahap alur pengajuan per produk: [nama, keterangan, syarat tambahan pada query pengajuan].
     * Tiap tahap dihitung sendiri (bukan bertingkat) dari pengajuan yang masuk pada periode.
     */
    public function stageDefinitions(string $product): array
    {
        $none = fn ($q) => $q;

        return match ($product) {
            'tomidana' => [
                ['Pengajuan tersimpan', 'Dari aplikasi TomiHub', $none],
                ['OTP terverifikasi', 'WhatsApp', fn ($q) => $q->where('p.status_otp', 'success')],
                ['Tanda tangan Privy', 'Perjanjian kredit', fn ($q) => $q->where(fn ($w) => $w->where('p.document_privy', 'y')->orWhereIn('p.status', ['approve', 'done']))],
                ['Dana dicairkan', 'Masuk rekening karyawan', fn ($q) => $q->whereExists(fn ($e) => $e->from('disbursements as dx')->whereColumn('dx.pinjaman_id', 'p.id'))],
                ['Lunas', 'Semua cicilan dibayar', fn ($q) => $q->where('p.status', 'done')],
            ],
            'tomigaji' => [
                ['Pengajuan masuk', 'Berkas gaji diunggah', $none],
                ['Dana ditransfer', 'Bukti transfer diunggah', fn ($q) => $q->whereIn('s.status', self::GAJI_DISBURSED)],
                ['Bukti bayar diunggah', 'Perusahaan membayar', fn ($q) => $q->whereIn('s.status', ['uploaded_bayar', 'lunas'])],
                ['Lunas', '', fn ($q) => $q->where('s.status', 'lunas')],
            ],
            'tomiinvoice' => [
                ['Tagihan diajukan', 'Pesanan pembelian masuk', $none],
                ['Disetujui analis', '', fn ($q) => $q->where('f.analyst_decision', 'approved')],
                ['Dibayar ke pelanggan', 'Bukti bayar diunggah', fn ($q) => $q->whereNotNull('f.finance_vendor_payment_proof_amount_1')],
                ['Termin ditagihkan', 'Tagihan termin ke mitra', fn ($q) => $q->whereNotNull('f.finance_invoice_term_amount_1')],
            ],
            default => [
                ['Pengajuan cicilan', '', $none],
                ['Disetujui', '', fn ($q) => $q->whereIn('g.status', ['approved', 'paid'])],
                ['Lunas', '', fn ($q) => $q->where('g.status', 'paid')],
            ],
        };
    }

    private function stageCounts(string $product, InsightPeriod $period, ?int $lenderId): array
    {
        return array_map(fn ($st) => ['name' => $st[0], 'sub' => $st[1], 'value' => $st[2]($this->applicationsQuery($product, $period, $lenderId))->count()],
            $this->stageDefinitions($product));
    }

    /* ===================================================================== */
    /* Rincian per produk */
    /* ===================================================================== */

    private function danaDetail(InsightPeriod $period, ?int $lenderId, array $kpis): array
    {
        $health = $this->danaHealth($period, $lenderId);
        $kpis[] = $this->kpi('late', 'Tunggakan', ['value' => $health['late'], 'prev' => null], 'rp', false, null, 'Cicilan lewat jatuh tempo per '.$health['as_of']);

        $stages = $this->stageCounts('tomidana', $period, $lenderId);

        $companies = $this->danaByCompany($period, $lenderId)->sortByDesc('v')->take(10)->values();

        $tenor = $this->danaLoans($period, $lenderId)->groupBy('p.tenor')->orderBy('p.tenor')
            ->get([DB::raw('p.tenor t'), DB::raw('COUNT(*) c')]);

        $queue = $this->pendingDisbursements($lenderId)->with(['data_karyawan.perusahaan'])->orderBy('created_at')->limit(8)->get()
            ->map(fn ($p) => [
                ['text' => $p->id_pinjaman, 'sub' => $p->data_karyawan?->perusahaan?->nama ?? '-', 'strong' => true],
                ['text' => $p->data_karyawan?->nama_karyawan ?? '-'],
                ['money' => (float) $p->jumlah_yang_diterima],
                $this->priorityBadge($p->created_at),
            ])->all();

        return [
            'kpis' => $kpis,
            'funnel' => ['title' => 'Alur pengajuan', 'note' => 'Pengajuan yang gagal OTP dihapus otomatis oleh sistem, jadi tahap pertama hanya menghitung pengajuan yang masih tersimpan.', 'stages' => $stages],
            'breakdown' => ['title' => 'Per perusahaan', 'sub' => 'Penyaluran pada periode ini (perusahaan saat pengajuan)', 'rows' => $companies->map(fn ($r) => ['name' => $r->name, 'sub' => $r->lender.' · '.$r->c.($r->manual ? ' peminjam (upload manual)' : ' pinjaman'), 'value' => $r->v,
                'filters' => ['company' => $r->name, 'lender' => $r->lender]])->all()],
            'health' => $health,
            'columns' => ['title' => 'Sebaran tenor', 'sub' => 'Jumlah pinjaman aplikasi per tenor (upload manual tidak punya tenor)', 'categories' => $tenor->map(fn ($r) => $r->t.' bln')->all(), 'data' => $tenor->map(fn ($r) => (int) $r->c)->all(), 'unit' => 'pinjaman'],
            'tables' => [
                ['title' => 'Antrean pencairan', 'sub' => 'Sudah tanda tangan, menunggu ditransfer pemberi dana', 'badge' => count($queue).' menunggu', 'link' => self::link('admin.ksp.index'),
                    'columns' => ['Pinjaman', 'Karyawan', 'Nominal', 'Prioritas'], 'num' => [2], 'rows' => $queue, 'empty' => 'Tidak ada antrean pencairan'],
                $this->danaInvoiceTable($lenderId),
            ],
        ];
    }

    /**
     * Penyaluran TomiDana per perusahaan & pemberi dana (aplikasi + upload manual), nama/pemberi dana kosong = "-".
     *
     * @return Collection<int, object{name:string,lender:string,c:int,v:float,manual:bool}>
     */
    private function danaByCompany(InsightPeriod $period, ?int $lenderId)
    {
        $loans = $this->danaLoans($period, $lenderId)
            ->leftJoin('list_perusahaans as lp', 'lp.id', '=', DB::raw(self::DANA_COMPANY))
            ->leftJoin('lenders as l', 'l.id', '=', 'p.id_lender')
            ->groupBy('lp.id', 'lp.nama', 'l.nama')
            ->get([DB::raw('lp.nama as name'), DB::raw('l.nama as lender'), DB::raw('COUNT(*) as c'), DB::raw('SUM(p.nominal) as v'), DB::raw('0 as manual')]);
        $manual = $this->manualUploads($period, $lenderId)
            ->leftJoin('lenders as l', 'l.id', '=', 'lpm.id_lender')
            ->groupBy(DB::raw('COALESCE(lpm.nama, m.nama_perusahaan)'), 'l.nama')
            ->get([DB::raw('COALESCE(lpm.nama, m.nama_perusahaan) as name'), DB::raw('l.nama as lender'), DB::raw('SUM(m.jumlah_peminjam) as c'), DB::raw('SUM(m.total_pinjaman) as v'), DB::raw('1 as manual')]);

        return $loans->concat($manual)->map(fn ($r) => (object) [
            'name' => ($r->name ?? '') === '' ? '-' : $r->name,
            'lender' => ($r->lender ?? '') === '' ? '-' : $r->lender,
            'c' => (int) $r->c,
            'v' => (float) $r->v,
            'manual' => (bool) $r->manual,
        ]);
    }

    private function danaInvoiceTable(?int $lenderId): array
    {
        $today = CarbonImmutable::today();
        $rows = DB::table('invoicing_pinjamen as i')
            ->join('list_perusahaans as lp', 'lp.id', '=', 'i.id_list_perusahaan')
            ->where('i.status', 'belum_lunas')
            ->when($lenderId, fn ($q) => $q->where('lp.id_lender', $lenderId))
            ->orderBy('i.period')
            ->limit(6)
            ->get(['i.id', 'i.period', 'i.id_list_perusahaan', 'lp.nama', 'lp.id_lender'])
            ->map(function ($i) use ($today) {
                $period = CarbonImmutable::parse($i->period);
                $amount = (float) DB::table('pinjamen as p')
                    ->where('p.id_perusahaan', $i->id_list_perusahaan)->where('p.id_lender', $i->id_lender)->where('p.status', 'approve')
                    ->whereExists(fn ($q) => $q->from('jatuh_tempos as jt')->whereColumn('jt.id_pinjaman', 'p.id')
                        ->whereYear('jt.jatuh_tempo', $period->year)->whereMonth('jt.jatuh_tempo', $period->month)->where('jt.status', '!=', 'lunas'))
                    ->sum('p.total_angsuran_perbulan');
                $days = $today->diffInDays($period->startOfDay(), false);

                return [
                    ['text' => $i->nama, 'strong' => true],
                    ['text' => InsightPeriod::date($period)],
                    ['money' => $amount],
                    $days < 0 ? ['badge' => ['tone' => 'crit', 'icon' => 'alert', 'label' => 'Lewat '.(int) abs($days).' hari']]
                        : ($days <= 7 ? ['badge' => ['tone' => 'warn', 'icon' => 'clock', 'label' => $days == 0 ? 'Hari ini' : (int) $days.' hari lagi']]
                            : ['badge' => ['tone' => 'good', 'icon' => 'check', 'label' => (int) $days.' hari lagi']]),
                ];
            })->all();

        return ['title' => 'Tagihan perusahaan belum lunas', 'sub' => 'Tagihan TomiDana, jatuh tempo terdekat', 'link' => self::link('admin.ksp.invoicing.index'),
            'columns' => ['Perusahaan', 'Jatuh tempo', 'Tagihan', 'Status'], 'num' => [2], 'rows' => $rows, 'empty' => 'Semua tagihan sudah lunas'];
    }

    private function gajiDetail(InsightPeriod $period, ?int $lenderId, array $kpis, array $cur): array
    {
        $stages = $this->stageCounts('tomigaji', $period, $lenderId);
        $companies = $this->gajiDisbursed($period, $lenderId)
            ->leftJoin('list_perusahaans as lp', 'lp.id', '=', 's.id_perusahaan')
            ->groupBy('lp.nama', 's.company_name')->orderByDesc(DB::raw('SUM(s.loan_amount)'))
            ->get([DB::raw('COALESCE(lp.nama, s.company_name) as name'), DB::raw('COUNT(*) c'), DB::raw('SUM(s.loan_amount) v'), DB::raw('SUM(s.total_borrowers) b')]);
        $statusLabel = ['pending' => ['warn', 'clock', 'Menunggu'], 'uploaded_transfer' => ['info', 'clock', 'Sudah ditransfer'], 'uploaded_bayar' => ['info', 'clock', 'Bukti bayar masuk'], 'lunas' => ['good', 'check', 'Lunas']];
        $recent = $this->gajiQuery($lenderId)->leftJoin('list_perusahaans as lp', 'lp.id', '=', 's.id_perusahaan')
            ->orderByDesc('s.created_at')->limit(6)
            ->get(['s.created_at', 's.loan_amount', 's.total_borrowers', 's.status', DB::raw('COALESCE(lp.nama, s.company_name) as name')])
            ->map(fn ($r) => [
                ['text' => $r->name ?? '-', 'sub' => InsightPeriod::date(CarbonImmutable::parse($r->created_at)), 'strong' => true],
                ['text' => $r->total_borrowers.' karyawan'],
                ['money' => (float) $r->loan_amount],
                ['badge' => ['tone' => $statusLabel[$r->status][0] ?? 'mute', 'icon' => $statusLabel[$r->status][1] ?? 'clock', 'label' => $statusLabel[$r->status][2] ?? $r->status]],
            ])->all();

        return [
            'kpis' => $kpis,
            'funnel' => ['title' => 'Alur pengajuan gaji', 'note' => null, 'stages' => $stages],
            'breakdown' => ['title' => 'Per perusahaan', 'sub' => 'Penyaluran pada periode ini', 'rows' => $companies->map(fn ($r) => ['name' => $r->name ?? '-', 'sub' => $r->c.' pengajuan · '.$r->b.' karyawan', 'value' => (float) $r->v,
                'filters' => ['company' => $r->name ?? '-']])->all()],
            'health' => null,
            'columns' => null,
            'tables' => [['title' => 'Pengajuan terbaru', 'sub' => 'Pengajuan TomiGaji terakhir', 'link' => self::link('admin.salary-financing.index'),
                'columns' => ['Perusahaan', 'Peminjam', 'Nilai', 'Status'], 'num' => [2], 'rows' => $recent, 'empty' => 'Belum ada pengajuan']],
        ];
    }

    private function invoiceDetail(InsightPeriod $period, ?int $lenderId, array $kpis, array $cur): array
    {
        $kpis[] = $this->kpi('po_amount', 'Nilai pesanan diajukan', ['value' => (float) $this->invoiceQuery($lenderId)->whereBetween('f.created_at', [$period->start(), $period->end()])->sum('f.purchase_order_amount'), 'prev' => null], 'rp', true, null);
        $stages = $this->stageCounts('tomiinvoice', $period, $lenderId);
        $vendors = $this->invoiceTerms($period, $lenderId)
            ->leftJoin('vendors as v', 'v.id', '=', 'it.vendor_id')
            ->groupBy('v.id', 'v.name')->orderByDesc(DB::raw('SUM('.self::INVOICE_TERM_SQL.')'))->limit(10)
            ->get([DB::raw('v.name as name'), DB::raw('COUNT(*) c'), DB::raw('SUM('.self::INVOICE_TERM_SQL.') v')]);
        $recent = $this->invoiceQuery($lenderId)->leftJoin('vendors as v', 'v.id', '=', 'f.vendor_id')->leftJoin('clients as c', 'c.id', '=', 'f.client_id')
            ->orderByDesc('f.created_at')->limit(6)
            ->get(['f.purchase_order_number', 'f.created_at', 'f.approved_financing_amount', 'f.purchase_order_amount', 'f.analyst_decision', DB::raw('v.name as vendor'), DB::raw('c.name as client')])
            ->map(fn ($r) => [
                ['text' => $r->purchase_order_number ?: '-', 'sub' => InsightPeriod::date(CarbonImmutable::parse($r->created_at)), 'strong' => true],
                ['text' => $r->vendor ?? '-', 'sub' => $r->client ?? null],
                ['money' => (float) ($r->approved_financing_amount ?: $r->purchase_order_amount)],
                $r->analyst_decision === 'approved' ? ['badge' => ['tone' => 'good', 'icon' => 'check', 'label' => 'Disetujui']]
                    : ($r->analyst_decision === 'rejected' ? ['badge' => ['tone' => 'crit', 'icon' => 'alert', 'label' => 'Ditolak']] : ['badge' => ['tone' => 'warn', 'icon' => 'clock', 'label' => 'Menunggu analis']]),
            ])->all();

        return [
            'kpis' => $kpis,
            'funnel' => ['title' => 'Alur tagihan', 'note' => null, 'stages' => $stages],
            'breakdown' => ['title' => 'Per pelanggan', 'sub' => 'Dibayar ke pelanggan pada periode ini', 'rows' => $vendors->map(fn ($r) => ['name' => $r->name ?? '-', 'sub' => $r->c.' termin', 'value' => (float) $r->v,
                'filters' => ['name' => $r->name ?? '-']])->all()],
            'health' => null,
            'columns' => null,
            'tables' => [['title' => 'Tagihan terbaru', 'sub' => 'Pengajuan Tomi Invoice terakhir', 'link' => self::link('admin.invoice-financing.index'),
                'columns' => ['Nomor pesanan', 'Pelanggan', 'Nilai', 'Status'], 'num' => [2], 'rows' => $recent, 'empty' => 'Belum ada tagihan']],
        ];
    }

    private function gawaiDetail(InsightPeriod $period, ?int $lenderId, array $kpis, array $cur): array
    {
        $stages = $this->stageCounts('tomigawai', $period, $lenderId);

        return [
            'kpis' => $kpis,
            'funnel' => ['title' => 'Alur cicilan', 'note' => null, 'stages' => $stages],
            'breakdown' => ['title' => 'Per toko', 'sub' => 'Cicilan disetujui pada periode ini', 'rows' => []],
            'health' => null,
            'columns' => null,
            'tables' => [],
        ];
    }

    /* ===================================================================== */
    /* Blok bersama */
    /* ===================================================================== */

    private function revenueBlock(array $cur, ?array $prev, array $buckets): array
    {
        $components = [];
        foreach ($cur as $key => $p) {
            foreach ($p['revenue'] ?? [] as $rk => $c) {
                $prevTotal = $prev[$key]['revenue'][$rk]['total'] ?? null;
                if ($c['total'] <= 0 && ! $prevTotal) {
                    continue; // komponen yang selalu nol tidak ditampilkan
                }
                $components[] = [
                    'key' => $rk, 'label' => $c['label'], 'product' => self::PRODUCTS[$c['product']]['name'],
                    'total' => $c['total'], 'prev' => $prev ? (float) ($prevTotal ?? 0) : null,
                    'data' => array_map(fn ($v) => round($v), $c['series']),
                ];
            }
        }
        $notes = array_values(array_filter(array_map(fn ($p) => $p['revenue_note'] ?? null, $cur)));

        return [
            'components' => $components,
            'total' => array_sum(array_column($components, 'total')),
            'prev' => $prev ? array_sum(array_column($components, 'prev')) : null,
            'categories' => array_column($buckets, 'label'),
            'notes' => $notes,
        ];
    }

    /** Penyaluran per pemberi dana. Penyaluran tanpa pemberi dana (mis. upload manual) jadi baris "Belum tercatat". */
    private function lenderSplit(InsightPeriod $period, ?int $lenderId, float $total): array
    {
        $rows = [];
        foreach (DB::table('lenders')->when($lenderId, fn ($q) => $q->where('id', $lenderId))->orderBy('id')->get(['id', 'nama']) as $l) {
            $sum = $this->danaTotals($period, $l->id, false)['totals']['disbursed']
                + $this->gajiTotals($period, $l->id, false)['totals']['disbursed']
                + $this->invoiceTotals($period, $l->id, false)['totals']['disbursed']
                + $this->gawaiTotals($period, $l->id, false)['totals']['disbursed'];
            $rows[] = ['name' => $l->nama, 'value' => $sum];
        }
        $rest = $total - array_sum(array_column($rows, 'value'));
        if (! $lenderId && $rest >= 1) {
            $rows[] = ['name' => 'Belum tercatat', 'value' => $rest];
        }

        return $rows;
    }

    /** Perusahaan mitra teratas (TomiDana + TomiGaji), dikelompokkan per nama yang sama dengan kolom perusahaan di panel rincian. */
    private function topCompanies(InsightPeriod $period, ?int $lenderId): array
    {
        $out = [];
        $add = function (string $name, string $key, float $value, int $count = 0) use (&$out) {
            $out[$name] ??= ['name' => $name, 'dana' => 0.0, 'dana_count' => 0, 'gaji' => 0.0];
            $out[$name][$key] += $value;
            $out[$name]['dana_count'] += $count;
        };
        foreach ($this->danaByCompany($period, $lenderId) as $r) {
            $add($r->name, 'dana', $r->v, $r->c);
        }
        $this->gajiDisbursed($period, $lenderId)->leftJoin('list_perusahaans as lp', 'lp.id', '=', 's.id_perusahaan')
            ->groupBy(DB::raw('COALESCE(lp.nama, s.company_name)'))
            ->get([DB::raw('COALESCE(lp.nama, s.company_name) as name'), DB::raw('SUM(s.loan_amount) v')])
            ->each(fn ($r) => $add(($r->name ?? '') === '' ? '-' : $r->name, 'gaji', (float) $r->v));
        $lenders = DB::table('list_perusahaans as lp')->join('lenders as l', 'l.id', '=', 'lp.id_lender')
            ->whereIn('lp.nama', array_keys($out))->get(['lp.nama as company', 'l.nama as lender'])->pluck('lender', 'company');

        return collect($out)->map(fn ($r) => $r + ['lender' => $lenders[$r['name']] ?? '-'])
            ->sortByDesc(fn ($r) => $r['dana'] + $r['gaji'])->take(8)->values()->all();
    }

    private function activityFeed(?int $lenderId): array
    {
        $events = collect();
        DB::table('disbursements as d')->join('pinjamen as p', 'p.id', '=', 'd.pinjaman_id')
            ->leftJoin('list_perusahaans as lp', 'lp.id', '=', 'p.id_perusahaan')
            ->when($lenderId, fn ($q) => $q->where('p.id_lender', $lenderId))
            ->orderByDesc('d.created_at')->limit(5)->get(['d.created_at', 'd.dana_yang_dicairkan', 'lp.nama'])
            ->each(fn ($r) => $events->push(['at' => $r->created_at, 'tone' => 'info', 'icon' => 'arrow', 'product' => 'TomiDana', 'title' => 'Pencairan '.$this->rp($r->dana_yang_dicairkan), 'sub' => $r->nama]));
        DB::table('repayments as r')->leftJoin('invoicing_pinjamen as i', 'i.id', '=', 'r.id_invoicing')
            ->leftJoin('list_perusahaans as lp', 'lp.id', '=', 'i.id_list_perusahaan')
            ->when($lenderId, fn ($q) => $q->where('lp.id_lender', $lenderId))
            ->orderByDesc('r.tanggal_pembayaran')->limit(5)->get(['r.tanggal_pembayaran', 'r.amount_total', 'lp.nama'])
            ->each(fn ($r) => $events->push(['at' => $r->tanggal_pembayaran, 'tone' => 'good', 'icon' => 'check', 'product' => 'TomiDana', 'title' => 'Tagihan dibayar '.$this->rp($r->amount_total), 'sub' => $r->nama]));
        $this->gajiQuery($lenderId)->leftJoin('list_perusahaans as lp', 'lp.id', '=', 's.id_perusahaan')
            ->orderByDesc('s.updated_at')->limit(4)->get(['s.updated_at', 's.loan_amount', 's.status', DB::raw('COALESCE(lp.nama, s.company_name) n')])
            ->each(fn ($r) => $events->push(['at' => $r->updated_at, 'tone' => $r->status === 'lunas' ? 'good' : 'info', 'icon' => $r->status === 'lunas' ? 'check' : 'arrow', 'product' => 'TomiGaji',
                'title' => ($r->status === 'lunas' ? 'Pengajuan lunas ' : 'Pengajuan diperbarui ').$this->rp($r->loan_amount), 'sub' => $r->n]));
        $this->invoiceQuery($lenderId)->leftJoin('vendors as v', 'v.id', '=', 'f.vendor_id')
            ->orderByDesc('f.updated_at')->limit(4)->get(['f.updated_at', 'f.approved_financing_amount', 'f.purchase_order_amount', DB::raw('v.name n')])
            ->each(fn ($r) => $events->push(['at' => $r->updated_at, 'tone' => 'info', 'icon' => 'arrow', 'product' => 'Tomi Invoice', 'title' => 'Tagihan '.$this->rp($r->approved_financing_amount ?: $r->purchase_order_amount), 'sub' => $r->n]));

        return $events->filter(fn ($e) => $e['at'])->sortByDesc('at')->take(7)->values()
            ->map(fn ($e) => $e + ['when' => CarbonImmutable::parse($e['at'])->locale('id')->diffForHumans()])->all();
    }

    private function summarySentence(array $cur, ?array $prev, InsightPeriod $period): string
    {
        $total = array_sum(array_map(fn ($p) => $p['totals']['disbursed'], $cur));
        if ($total <= 0) {
            return 'Belum ada dana tersalurkan pada periode ini.';
        }
        $byProduct = array_map(fn ($p) => $p['totals']['disbursed'], $cur);
        arsort($byProduct);
        $top = array_key_first($byProduct);
        $share = $byProduct[$top] / $total;
        $text = self::PRODUCTS[$top]['name'].' menyumbang '.round($share * 100).'% dari total penyaluran.';
        $prevTotal = $prev ? array_sum(array_map(fn ($p) => $p['totals']['disbursed'], $prev)) : 0;
        if ($prevTotal > 0) {
            $change = ($total - $prevTotal) / $prevTotal;
            $text = 'Penyaluran '.($change >= 0 ? 'naik ' : 'turun ').number_format(abs($change) * 100, 1, ',', '.').'% dibanding '.$period->prevLabel.'. '.$text;
        }

        return $text;
    }

    /** Pinjaman TomiDana yang sudah tanda tangan Privy dan menunggu dana ditransfer pemberi dana. */
    private function pendingDisbursements(?int $lenderId)
    {
        return pinjaman::query()
            ->when($lenderId !== null, fn ($q) => $q->where('id_lender', $lenderId))
            ->where('status', 'pending')
            ->where('document_privy', 'y');
    }

    /** URL halaman operasional bila route-nya ada di aplikasi ini. */
    private static function link(string $route): ?string
    {
        return Route::has($route) ? route($route) : null;
    }

    /** $metric = kunci rincian (panel detail) — lihat InsightDetail. */
    private function kpi(string $metric, string $label, array $t, string $format, bool $upIsGood, ?array $spark, ?string $hint = null): array
    {
        return ['metric' => $metric, 'label' => $label, 'value' => $t['value'], 'prev' => $t['prev'], 'format' => $format, 'up_is_good' => $upIsGood, 'spark' => $spark, 'hint' => $hint];
    }

    private function priorityBadge($createdAt): array
    {
        $hours = CarbonImmutable::parse($createdAt)->diffInHours(CarbonImmutable::now());

        return match (true) {
            $hours >= 24 => ['badge' => ['tone' => 'crit', 'icon' => 'alert', 'label' => 'Lewat 24 jam']],
            $hours >= 4 => ['badge' => ['tone' => 'warn', 'icon' => 'clock', 'label' => 'Lewat 4 jam']],
            $hours >= 1 => ['badge' => ['tone' => 'info', 'icon' => 'clock', 'label' => 'Biasa']],
            default => ['badge' => ['tone' => 'mute', 'icon' => 'clock', 'label' => 'Baru masuk']],
        };
    }

    /** Jumlahkan beberapa deret dengan bucket yang sama. */
    private function addSeries(array ...$series): array
    {
        return array_map(fn (...$v) => array_sum($v), ...$series);
    }

    /** SUM(expr) dikelompokkan per bucket periode, urut sesuai InsightPeriod::buckets(). */
    private function series(Builder $query, string $dateExpr, string $valueExpr, InsightPeriod $period): array
    {
        $fmt = $period->sqlFormat();
        $rows = (clone $query)->selectRaw("DATE_FORMAT($dateExpr, '$fmt') as k, SUM($valueExpr) as v")
            ->groupBy('k')->pluck('v', 'k');

        return array_map(fn ($b) => (float) ($rows[$b['key']] ?? 0), $period->buckets());
    }

    /** Rupiah lengkap tanpa singkatan & tanpa desimal, mis. "Rp 5.417.295.922". */
    private function rp($value): string
    {
        return 'Rp '.number_format(round((float) $value), 0, ',', '.');
    }
}
