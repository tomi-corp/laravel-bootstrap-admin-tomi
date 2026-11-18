<?php

namespace App\Services\Insight;

use App\Models\pinjaman;
use Carbon\CarbonImmutable;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/**
 * Angka untuk Dasbor utama & dasbor per produk.
 *
 * DEFINISI SEMENTARA (belum disepakati tim bisnis):
 * - TomiDana "dana dicairkan" = disbursements.dana_yang_dicairkan (waktu: disbursements.created_at).
 * - Pemberi dana TomiDana = pinjamen.id_lender (pencairan) / list_perusahaans.id_lender (pembayaran tagihan).
 *   Kolom disbursements.lender_id & repayments.lender_id (atribusi lender saat cair/bayar) belum ada di skema ini.
 * - TomiDana "pembayaran" = repayments.amount_total (waktu: repayments.tanggal_pembayaran).
 * - TomiDana "sisa pinjaman" pada akhir periode = cicilan belum lunas × total_angsuran_perbulan
 *   (cicilan yang lunas setelah akhir periode dihitung belum lunas, pakai jatuh_tempos.updated_at).
 * - Pendapatan TomiDana (diakui saat pencairan) = pendapatan_bunga + total_biaya_admin + biaya_admin_pandu
 *   + biaya_layanan + jumlah_asuransi + jumlah_provisi + jumlah_profesional_1/2. Pendapatan TomiGaji = total_fee.
 * - Funnel TomiDana dihitung dari pengajuan yang tersimpan. Pengajuan yang gagal OTP dihapus permanen
 *   oleh cron hapuspinjaman, jadi tahap awal funnel lebih kecil dari kenyataan.
 * - TomiGaji dicairkan = status uploaded_transfer/uploaded_bayar/lunas (waktu: transfer_date, lalu created_at);
 *   dibayar = status lunas (waktu: payment_date, lalu updated_at).
 * - Tomi Invoice dicairkan = approved_financing_amount yang disetujui (waktu: approved_at).
 * - TomiGawai = gadget_installments status approved/paid (waktu: created_at).
 *
 * NOA (Number of Account) — dihitung per akun pinjaman, bukan per orang:
 * - NOA penyaluran = jumlah akun yang cair dalam periode: TomiDana per pinjaman dicairkan, TomiGaji per pengajuan
 *   dicairkan, Tomi Invoice per tagihan disetujui, TomiGawai per cicilan disetujui.
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

    /** Komponen pendapatan TomiDana (kolom pinjamen) — urutan = urutan tampil. */
    private const DANA_REVENUE = [
        'bunga' => ['Bunga', 'p.pendapatan_bunga'],
        'admin' => ['Biaya admin', 'p.total_biaya_admin'],
        'admin_pandu' => ['Biaya admin Pandu', 'p.biaya_admin_pandu'],
        'layanan' => ['Biaya layanan', 'p.biaya_layanan'],
        'asuransi' => ['Asuransi', 'p.jumlah_asuransi'],
        'provisi' => ['Provisi', 'p.jumlah_provisi'],
        'profesional' => ['Biaya profesional', 'COALESCE(p.jumlah_profesional_1,0) + COALESCE(p.jumlah_profesional_2,0)'],
    ];

    public const GAJI_DISBURSED = ['uploaded_transfer', 'uploaded_bayar', 'lunas'];

    public function earliest(): CarbonImmutable
    {
        $dates = array_filter([
            DB::table('pinjamen')->whereNull('deleted_at')->min('created_at'),
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
                $this->kpi('noa', 'NOA penyaluran', $totals['count'], 'num', true, $sumSeries('noa'), 'Akun cair pada periode ini'),
                $this->kpi('noa_out', 'NOA outstanding', $totals['noa_out'], 'num', true, $sumSeries('noa_out'), 'Akun belum lunas per '.InsightPeriod::date($period->to)),
                $this->kpi('outstanding', 'Sisa pinjaman berjalan', $totals['outstanding'], 'rp', true, $sumSeries('outstanding'), 'Per '.InsightPeriod::date($period->to)),
                $this->kpi('repaid', 'Pembayaran diterima', $totals['repaid'], 'rp', true, $sumSeries('repaid')),
                $this->kpi('revenue', 'Pendapatan (gabungan)', $totals['revenue'], 'rp', true, $sumSeries('revenue'), 'Definisi sementara'),
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
            'lenders' => $this->lenderSplit($period, $lenderId),
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
            $this->kpi('noa', 'NOA penyaluran', $t('count'), 'num', true, $cur['series']['noa'], '1 akun = 1 '.$cur['count_label'].' cair'),
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

    public function danaDisbursements(InsightPeriod $period, ?int $lenderId): Builder
    {
        return DB::table('disbursements as d')
            ->join('pinjamen as p', 'p.id', '=', 'd.pinjaman_id')
            ->whereBetween('d.created_at', [$period->start(), $period->end()])
            ->when($lenderId, fn ($q) => $q->where('p.id_lender', $lenderId));
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
        $revenueSql = implode(' + ', array_map(fn ($c) => 'COALESCE('.$c[1].',0)', self::DANA_REVENUE));
        $row = $this->danaDisbursements($period, $lenderId)
            ->selectRaw("COUNT(*) c, COALESCE(SUM(d.dana_yang_dicairkan),0) amt, COALESCE(SUM($revenueSql),0) rev")
            ->first();
        $apps = $this->danaApplications($period, $lenderId)->count();
        $approved = $this->danaApplications($period, $lenderId)
            ->whereExists(fn ($q) => $q->from('disbursements as dx')->whereColumn('dx.pinjaman_id', 'p.id'))
            ->count();

        $revenue = [];
        foreach (self::DANA_REVENUE as $rk => [$label, $col]) {
            $revenue[$rk] = ['label' => $label, 'product' => 'tomidana', 'total' => (float) $this->danaDisbursements($period, $lenderId)->sum(DB::raw("COALESCE($col,0)")),
                'series' => $withSeries ? $this->series($this->danaDisbursements($period, $lenderId), 'd.created_at', "COALESCE($col,0)", $period) : []];
        }

        return [
            'labels' => ['disbursed' => 'Dana dicairkan', 'repaid' => 'Pembayaran diterima'],
            'count_label' => 'pinjaman',
            'totals' => [
                'disbursed' => (float) $row->amt,
                'count' => (int) $row->c,
                'noa_out' => $this->danaNoaOutstanding($period->to, $lenderId),
                'repaid' => (float) $this->danaRepayments($period, $lenderId)->sum('r.amount_total'),
                'outstanding' => $this->danaOutstanding($period->to, $lenderId),
                'revenue' => (float) $row->rev,
                'applications' => $apps,
                'approved' => $approved,
            ],
            'series' => $withSeries ? [
                'disbursed' => $this->series($this->danaDisbursements($period, $lenderId), 'd.created_at', 'd.dana_yang_dicairkan', $period),
                'noa' => $this->series($this->danaDisbursements($period, $lenderId), 'd.created_at', '1', $period),
                'noa_out' => $this->noaOutSeries($period, fn ($at) => $this->danaNoaOutstanding($at, $lenderId)),
                'outstanding' => $this->positionSeries($period, fn ($at) => $this->danaOutstanding($at, $lenderId)),
                'repaid' => $this->series($this->danaRepayments($period, $lenderId), 'r.tanggal_pembayaran', 'r.amount_total', $period),
                'applications' => $this->series($this->danaApplications($period, $lenderId), 'p.created_at', '1', $period),
                'revenue' => $this->series($this->danaDisbursements($period, $lenderId), 'd.created_at', $revenueSql, $period),
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

    private function gajiTotals(InsightPeriod $period, ?int $lenderId, bool $withSeries): array
    {
        $dateDisb = 'COALESCE(s.transfer_date, s.created_at)';
        $datePaid = 'COALESCE(s.payment_date, s.updated_at)';
        $disb = fn () => $this->gajiQuery($lenderId)->whereIn('s.status', self::GAJI_DISBURSED)
            ->whereRaw("$dateDisb BETWEEN ? AND ?", [$period->start(), $period->end()]);
        $paid = fn () => $this->gajiQuery($lenderId)->where('s.status', 'lunas')
            ->whereRaw("$datePaid BETWEEN ? AND ?", [$period->start(), $period->end()]);
        $apps = fn () => $this->gajiQuery($lenderId)->whereBetween('s.created_at', [$period->start(), $period->end()]);
        $openAt = fn (string $end) => $this->gajiOpenAt($end, $lenderId);
        $outstanding = (float) $openAt($period->end())->sum('s.total_dibayarkan');

        return [
            'labels' => ['disbursed' => 'Dana dicairkan', 'repaid' => 'Pelunasan diterima'],
            'count_label' => 'pengajuan',
            'totals' => [
                'disbursed' => (float) $disb()->sum('s.loan_amount'),
                'count' => $disb()->count(),
                'noa_out' => $openAt($period->end())->count(),
                'repaid' => (float) $paid()->sum('s.total_dibayarkan'),
                'outstanding' => $outstanding,
                'revenue' => (float) $disb()->sum('s.total_fee'),
                'applications' => $apps()->count(),
                'approved' => $apps()->whereIn('s.status', self::GAJI_DISBURSED)->count(),
                'borrowers' => (int) $disb()->sum('s.total_borrowers'),
            ],
            'series' => $withSeries ? [
                'disbursed' => $this->series($disb(), $dateDisb, 's.loan_amount', $period),
                'noa' => $this->series($disb(), $dateDisb, '1', $period),
                'noa_out' => $this->noaOutSeries($period, fn ($at) => $openAt($at->endOfDay()->toDateTimeString())->count()),
                'outstanding' => $this->positionSeries($period, fn ($at) => (float) $openAt($at->endOfDay()->toDateTimeString())->sum('s.total_dibayarkan')),
                'repaid' => $this->series($paid(), $datePaid, 's.total_dibayarkan', $period),
                'applications' => $this->series($apps(), 's.created_at', '1', $period),
                'revenue' => $this->series($disb(), $dateDisb, 's.total_fee', $period),
            ] : [],
            'revenue' => ['fee_gaji' => ['label' => 'Biaya jasa TomiGaji', 'product' => 'tomigaji', 'total' => (float) $disb()->sum('s.total_fee'),
                'series' => $withSeries ? $this->series($disb(), $dateDisb, 's.total_fee', $period) : []]],
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

    private function invoiceTotals(InsightPeriod $period, ?int $lenderId, bool $withSeries): array
    {
        $approved = fn () => $this->invoiceQuery($lenderId)->where('f.analyst_decision', 'approved')
            ->whereBetween('f.approved_at', [$period->start(), $period->end()]);
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
            'labels' => ['disbursed' => 'Pembiayaan disetujui', 'repaid' => 'Termin ditagihkan'],
            'count_label' => 'tagihan',
            'totals' => [
                'disbursed' => (float) $approved()->sum('f.approved_financing_amount'),
                'count' => $approved()->count(),
                'noa_out' => $openAt($end)->count(),
                'repaid' => (float) $terms()->sum('t.amt'),
                'outstanding' => $outstandingAt($end),
                'revenue' => 0.0,
                'applications' => $apps()->count(),
                'approved' => $apps()->where('f.analyst_decision', 'approved')->count(),
            ],
            'series' => $withSeries ? [
                'disbursed' => $this->series($approved(), 'f.approved_at', 'f.approved_financing_amount', $period),
                'noa' => $this->series($approved(), 'f.approved_at', '1', $period),
                'noa_out' => $this->noaOutSeries($period, fn ($at) => $openAt($at->endOfDay()->toDateTimeString())->count()),
                'outstanding' => $this->positionSeries($period, fn ($at) => $outstandingAt($at->endOfDay()->toDateTimeString())),
                'repaid' => $this->series($terms(), 't.dt', 't.amt', $period),
                'applications' => $this->series($apps(), 'f.created_at', '1', $period),
                'revenue' => array_fill(0, count($period->buckets()), 0.0),
            ] : [],
            'revenue' => [],
            'revenue_note' => 'Pendapatan Tomi Invoice belum punya definisi (biaya per termin belum tersimpan sebagai angka).',
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

        $companies = $this->danaDisbursements($period, $lenderId)
            ->leftJoin('list_perusahaans as lp', 'lp.id', '=', 'p.id_perusahaan')
            ->leftJoin('lenders as l', 'l.id', '=', 'p.id_lender')
            ->groupBy('lp.id', 'lp.nama', 'l.nama')
            ->orderByDesc(DB::raw('SUM(d.dana_yang_dicairkan)'))
            ->limit(10)
            ->get([DB::raw('lp.nama as name'), DB::raw('l.nama as lender'), DB::raw('COUNT(*) as c'), DB::raw('SUM(d.dana_yang_dicairkan) as v')]);

        $tenor = $this->danaDisbursements($period, $lenderId)->groupBy('p.tenor')->orderBy('p.tenor')
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
            'breakdown' => ['title' => 'Per perusahaan', 'sub' => 'Dana dicairkan pada periode ini', 'rows' => $companies->map(fn ($r) => ['name' => $r->name ?? '-', 'sub' => ($r->lender ?? '-').' · '.$r->c.' pinjaman', 'value' => (float) $r->v,
                'filters' => ['company' => $r->name ?? '-', 'lender' => $r->lender ?? '-']])->all()],
            'health' => $health,
            'columns' => ['title' => 'Sebaran tenor', 'sub' => 'Jumlah pinjaman dicairkan per tenor', 'categories' => $tenor->map(fn ($r) => $r->t.' bln')->all(), 'data' => $tenor->map(fn ($r) => (int) $r->c)->all(), 'unit' => 'pinjaman'],
            'tables' => [
                ['title' => 'Antrean pencairan', 'sub' => 'Sudah tanda tangan, menunggu ditransfer pemberi dana', 'badge' => count($queue).' menunggu', 'link' => self::link('admin.ksp.index'),
                    'columns' => ['Pinjaman', 'Karyawan', 'Nominal', 'Prioritas'], 'num' => [2], 'rows' => $queue, 'empty' => 'Tidak ada antrean pencairan'],
                $this->danaInvoiceTable($lenderId),
            ],
        ];
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
        $kpis[] = $this->kpi('borrowers', 'Karyawan dibiayai', ['value' => $cur['totals']['borrowers'], 'prev' => null], 'num', true, null, 'Total karyawan dalam pengajuan yang cair');
        $stages = $this->stageCounts('tomigaji', $period, $lenderId);
        $companies = $this->gajiQuery($lenderId)->whereIn('s.status', self::GAJI_DISBURSED)
            ->whereRaw('COALESCE(s.transfer_date, s.created_at) BETWEEN ? AND ?', [$period->start(), $period->end()])
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
            'breakdown' => ['title' => 'Per perusahaan', 'sub' => 'Dana dicairkan pada periode ini', 'rows' => $companies->map(fn ($r) => ['name' => $r->name ?? '-', 'sub' => $r->c.' pengajuan · '.$r->b.' karyawan', 'value' => (float) $r->v,
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
        $vendors = $this->invoiceQuery($lenderId)->where('f.analyst_decision', 'approved')
            ->whereBetween('f.approved_at', [$period->start(), $period->end()])
            ->leftJoin('vendors as v', 'v.id', '=', 'f.vendor_id')
            ->groupBy('v.id', 'v.name')->orderByDesc(DB::raw('SUM(f.approved_financing_amount)'))->limit(10)
            ->get([DB::raw('v.name as name'), DB::raw('COUNT(*) c'), DB::raw('SUM(f.approved_financing_amount) v')]);
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
            'breakdown' => ['title' => 'Per pelanggan', 'sub' => 'Pembiayaan disetujui pada periode ini', 'rows' => $vendors->map(fn ($r) => ['name' => $r->name ?? '-', 'sub' => $r->c.' tagihan', 'value' => (float) $r->v,
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

    private function lenderSplit(InsightPeriod $period, ?int $lenderId): array
    {
        $rows = [];
        foreach (DB::table('lenders')->when($lenderId, fn ($q) => $q->where('id', $lenderId))->orderBy('id')->get(['id', 'nama']) as $l) {
            $sum = $this->danaTotals($period, $l->id, false)['totals']['disbursed']
                + $this->gajiTotals($period, $l->id, false)['totals']['disbursed']
                + $this->invoiceTotals($period, $l->id, false)['totals']['disbursed']
                + $this->gawaiTotals($period, $l->id, false)['totals']['disbursed'];
            $rows[] = ['name' => $l->nama, 'value' => $sum];
        }

        return $rows;
    }

    private function topCompanies(InsightPeriod $period, ?int $lenderId): array
    {
        $dana = $this->danaDisbursements($period, $lenderId)
            ->groupBy('p.id_perusahaan')
            ->get([DB::raw('p.id_perusahaan as id'), DB::raw('SUM(d.dana_yang_dicairkan) v'), DB::raw('COUNT(*) c')])->keyBy('id');
        $gaji = $this->gajiQuery($lenderId)->whereIn('s.status', self::GAJI_DISBURSED)
            ->whereRaw('COALESCE(s.transfer_date, s.created_at) BETWEEN ? AND ?', [$period->start(), $period->end()])
            ->whereNotNull('s.id_perusahaan')->groupBy('s.id_perusahaan')
            ->get([DB::raw('s.id_perusahaan as id'), DB::raw('SUM(s.loan_amount) v')])->keyBy('id');
        $ids = $dana->keys()->merge($gaji->keys())->unique()->filter();
        $names = DB::table('list_perusahaans as lp')->leftJoin('lenders as l', 'l.id', '=', 'lp.id_lender')
            ->whereIn('lp.id', $ids)->get(['lp.id', 'lp.nama', DB::raw('l.nama as lender')])->keyBy('id');

        return $ids->map(fn ($id) => [
            'name' => $names[$id]->nama ?? '-',
            'lender' => $names[$id]->lender ?? '-',
            'dana' => (float) ($dana[$id]->v ?? 0),
            'dana_count' => (int) ($dana[$id]->c ?? 0),
            'gaji' => (float) ($gaji[$id]->v ?? 0),
        ])->sortByDesc(fn ($r) => $r['dana'] + $r['gaji'])->take(8)->values()->all();
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

    /** SUM(expr) dikelompokkan per bucket periode, urut sesuai InsightPeriod::buckets(). */
    private function series(Builder $query, string $dateExpr, string $valueExpr, InsightPeriod $period): array
    {
        $fmt = $period->sqlFormat();
        $rows = (clone $query)->selectRaw("DATE_FORMAT($dateExpr, '$fmt') as k, SUM($valueExpr) as v")
            ->groupBy('k')->pluck('v', 'k');

        return array_map(fn ($b) => (float) ($rows[$b['key']] ?? 0), $period->buckets());
    }

    private function rp($value): string
    {
        $v = (float) $value;
        if ($v >= 1e9) {
            return 'Rp '.number_format($v / 1e9, 1, ',', '.').' M';
        }
        if ($v >= 1e6) {
            return 'Rp '.number_format($v / 1e6, 1, ',', '.').' jt';
        }

        return 'Rp '.number_format($v, 0, ',', '.');
    }
}
