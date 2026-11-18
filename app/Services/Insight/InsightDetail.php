<?php

namespace App\Services\Insight;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/**
 * Rincian satu kartu KPI dasbor: daftar akun/transaksi yang membentuk angkanya + pecahannya.
 *
 * Semua daftar memakai query yang sama dengan angka KPI di InsightService (filter periode & pemberi dana sama),
 * jadi jumlah baris / total nominal di sini selalu cocok dengan angka di kartunya.
 */
class InsightDetail
{
    public const PAGE_SIZE = 20;

    /** metrik => [jenis nilai, label kolom tanggal, label kolom nilai] */
    private const METRICS = [
        'disbursed' => ['money', 'Tanggal realisasi', 'Nominal'],
        'noa' => ['count', 'Tanggal realisasi', 'Nominal'],
        'outstanding' => ['money', 'Tanggal cair', 'Sisa'],
        'noa_out' => ['count', 'Tanggal cair', 'Sisa'],
        'repaid' => ['money', 'Tanggal', 'Nominal'],
        'revenue' => ['money', 'Tanggal realisasi', 'Pendapatan TOMI'],
        'late' => ['money', 'Jatuh tempo', 'Cicilan'],
        'applications' => ['count', 'Tanggal pengajuan', 'Nominal'],
        'approval' => ['rate', 'Tanggal pengajuan', 'Nominal'],
        'po_amount' => ['money', 'Tanggal pengajuan', 'Nilai pesanan'],
        'funnel' => ['count', 'Tanggal pengajuan', 'Nominal'],
    ];

    /** Kolom baris yang boleh dipakai menyaring (klik pecahan / baris perusahaan). */
    public const FILTER_KEYS = ['company', 'name', 'lender', 'product', 'status'];

    /** Metrik yang tersedia per cakupan. */
    private const AVAILABLE = [
        'overview' => ['disbursed', 'noa', 'noa_out', 'outstanding', 'repaid', 'revenue', 'late'],
        'tomidana' => ['disbursed', 'noa', 'outstanding', 'noa_out', 'applications', 'approval', 'repaid', 'revenue', 'late', 'funnel'],
        'tomigaji' => ['disbursed', 'noa', 'outstanding', 'noa_out', 'applications', 'approval', 'repaid', 'revenue', 'funnel'],
        'tomiinvoice' => ['disbursed', 'noa', 'outstanding', 'noa_out', 'applications', 'approval', 'repaid', 'revenue', 'po_amount', 'funnel'],
        'tomigawai' => ['disbursed', 'noa', 'outstanding', 'noa_out', 'applications', 'approval', 'repaid', 'funnel'],
    ];

    private const GAJI_STATUS = ['pending' => ['warn', 'Menunggu'], 'uploaded_transfer' => ['info', 'Sudah ditransfer'], 'uploaded_bayar' => ['info', 'Bukti bayar masuk'], 'lunas' => ['good', 'Lunas']];

    /** Syarat tambahan untuk query pengajuan (tahap alur), diisi saat metrik = funnel. */
    private $stageConstraint = null;

    public function __construct(private readonly InsightService $insight) {}

    /** Query pengajuan produk pada periode + syarat tahap alur (bila ada). */
    private function apps(string $product, InsightPeriod $period, ?int $lenderId)
    {
        $q = $this->insight->applicationsQuery($product, $period, $lenderId);

        return $this->stageConstraint ? ($this->stageConstraint)($q) : $q;
    }

    public static function supports(string $scope, string $metric): bool
    {
        return in_array($metric, self::AVAILABLE[$scope] ?? [], true);
    }

    /**
     * @param  array{q?:string,sort?:string,dir?:string,page?:int,stage?:int,filters?:array<string,string>,only?:array}  $opts
     */
    public function build(string $scope, string $metric, InsightPeriod $period, ?int $lenderId, array $opts = [], bool $all = false): array
    {
        $stage = null;
        if ($metric === 'funnel') {
            $stages = $this->insight->stageDefinitions($scope);
            $stage = min(max(0, (int) ($opts['stage'] ?? 0)), count($stages) - 1);
            $this->stageConstraint = $stages[$stage][2];
        }

        $products = $scope === 'overview' ? array_keys(InsightService::PRODUCTS) : [$scope];
        if (! empty($opts['only'])) {
            $products = array_values(array_intersect($products, $opts['only']));
        }
        $rows = [];
        foreach ($products as $p) {
            if ($scope === 'overview' && ! self::supports($p, $metric)) {
                continue;
            }
            foreach ($this->rows($p, $metric === 'funnel' ? 'applications' : $metric, $period, $lenderId) as $r) {
                $rows[] = $r + ['product' => InsightService::PRODUCTS[$p]['name'], 'slot' => InsightService::PRODUCTS[$p]['slot']];
            }
        }
        $this->stageConstraint = null;

        // saring per nilai kolom (klik baris perusahaan / item pecahan); beberapa kolom = semuanya harus cocok
        $filters = array_filter((array) ($opts['filters'] ?? []), fn ($v, $k) => in_array($k, self::FILTER_KEYS, true) && is_string($v) && $v !== '', ARRAY_FILTER_USE_BOTH);
        if ($filters) {
            $rows = array_values(array_filter($rows, function ($r) use ($filters) {
                foreach ($filters as $k => $v) {
                    if ($this->fieldValue($r, $k) !== $v) {
                        return false;
                    }
                }

                return true;
            }));
        }

        [$kind, $dateLabel, $valueLabel] = self::METRICS[$metric];
        $summary = $this->summarize($rows, $kind);
        $breakdowns = $this->breakdowns($rows, $kind, $scope === 'overview');

        // cari
        $q = mb_strtolower(trim((string) ($opts['q'] ?? '')));
        $filtered = $q === '' ? $rows : array_values(array_filter($rows, function ($r) use ($q) {
            foreach (['ref', 'name', 'company', 'lender', 'product', 'extra'] as $k) {
                if (($r[$k] ?? null) !== null && str_contains(mb_strtolower((string) $r[$k]), $q)) {
                    return true;
                }
            }

            return isset($r['status']) && str_contains(mb_strtolower($r['status']['label']), $q);
        }));

        // urutkan
        $sort = in_array($opts['sort'] ?? '', ['date', 'amount'], true) ? $opts['sort'] : 'date';
        $dir = ($opts['dir'] ?? ($metric === 'late' ? 'asc' : 'desc')) === 'asc' ? 1 : -1;
        usort($filtered, fn ($a, $b) => $dir * (($a[$sort] ?? '') <=> ($b[$sort] ?? '')));

        $total = count($filtered);
        $pages = max(1, (int) ceil($total / self::PAGE_SIZE));
        $page = min(max(1, (int) ($opts['page'] ?? 1)), $pages);
        $list = $all ? $filtered : array_slice($filtered, ($page - 1) * self::PAGE_SIZE, self::PAGE_SIZE);

        return [
            'metric' => $metric,
            'kind' => $kind,
            'stage' => $stage,
            'filters' => (object) $filters,
            'definition' => $metric === 'funnel'
                ? 'Pengajuan yang masuk pada periode ini dan sudah sampai tahap “'.$this->insight->stageDefinitions($scope)[$stage][0].'”'
                    .(($sub = $this->insight->stageDefinitions($scope)[$stage][1]) ? ' ('.$sub.')' : '').'. Tiap tahap dihitung sendiri dari pengajuan yang masuk pada periode.'
                : $this->definition($scope, $metric).(! empty($opts['only']) && $scope === 'overview'
                    ? ' Hanya '.implode(' & ', array_map(fn ($k) => InsightService::PRODUCTS[$k]['name'], $products)).'.' : ''),
            'as_of' => in_array($metric, ['outstanding', 'noa_out', 'late'], true) ? InsightPeriod::date($period->to) : null,
            'summary' => $summary,
            'breakdowns' => $breakdowns,
            'columns' => $this->columns($rows, $scope === 'overview', $dateLabel, $valueLabel, $metric, $scope),
            'rows' => $list,
            'pagination' => ['page' => $page, 'pages' => $pages, 'total' => $total, 'all' => count($rows), 'size' => self::PAGE_SIZE],
            'sort' => ['key' => $sort, 'dir' => $dir === 1 ? 'asc' : 'desc'],
            'links' => $this->links($scope, $metric, $products),
        ];
    }

    /* ===================================================================== */
    /* Baris per produk */
    /* ===================================================================== */

    private function rows(string $product, string $metric, InsightPeriod $period, ?int $lenderId): array
    {
        return match ($product) {
            'tomidana' => $this->danaRows($metric, $period, $lenderId),
            'tomigaji' => $this->gajiRows($metric, $period, $lenderId),
            'tomiinvoice' => $this->invoiceRows($metric, $period, $lenderId),
            default => $this->gawaiRows($metric, $period, $lenderId),
        };
    }

    private function danaRows(string $metric, InsightPeriod $period, ?int $lenderId): array
    {
        $at = $period->to;
        $atEnd = $at->endOfDay()->toDateTimeString();
        $people = function ($q) {
            return $q->leftJoin('list_karyawans as k', 'k.id', '=', 'p.id_karyawan')
                ->leftJoin('list_perusahaans as lp', 'lp.id', '=', 'p.id_perusahaan');
        };

        switch ($metric) {
            case 'disbursed':
            case 'noa':
            case 'revenue':
                $rev = $metric === 'revenue';
                $loans = $this->insight->danaLoans($period, $lenderId)
                    ->leftJoin('list_perusahaans as lp', 'lp.id', '=', DB::raw(InsightService::DANA_COMPANY))
                    ->leftJoin('lenders as l', 'l.id', '=', 'p.id_lender')
                    ->get(['p.created_at', 'p.id_pinjaman', 'k.nama_karyawan', 'lp.nama as company', 'l.nama as lender', 'p.nominal', 'p.tenor', 'p.status', 'p.biaya_layanan',
                        DB::raw('('.InsightService::DANA_FEE_SQL.') as fee')])
                    ->map(fn ($r) => $this->row($r->created_at, $r->id_pinjaman, $r->nama_karyawan, $r->company, $r->lender,
                        $rev ? (float) $r->fee + (float) $r->biaya_layanan : (float) $r->nominal,
                        $r->status === 'done' ? ['good', 'Lunas'] : ['info', 'Berjalan'],
                        $rev ? 'Fee 1% '.$this->rp($r->fee).' · layanan '.$this->rp($r->biaya_layanan).' · pinjaman '.$this->rp($r->nominal)
                            : 'Tenor '.$r->tenor.' bln'));
                $manual = $this->insight->manualUploads($period, $lenderId)
                    ->leftJoin('lenders as l', 'l.id', '=', 'lpm.id_lender')
                    ->get(['m.id', 'm.created_at', 'm.filename', 'm.jumlah_peminjam', 'm.total_pinjaman', 'm.total_fee', 'l.nama as lender', DB::raw('COALESCE(lpm.nama, m.nama_perusahaan) as company')])
                    ->map(fn ($r) => $this->row($r->created_at, 'Upload manual #'.$r->id, $r->jumlah_peminjam.' peminjam', $r->company, $r->lender,
                        $rev ? (float) $r->total_fee : (float) $r->total_pinjaman, ['mute', 'Upload manual'],
                        ($rev ? 'Fee dari file · pinjaman '.$this->rp($r->total_pinjaman).' · ' : 'Buat Tagihan · ').$r->filename, (int) $r->jumlah_peminjam));

                return $loans->concat($manual)->all();

            case 'outstanding':
            case 'noa_out':
                return $people($this->insight->danaInstallmentsOpenAt($at, $lenderId))
                    ->leftJoin('lenders as l', 'l.id', '=', 'p.id_lender')
                    ->groupBy('p.id')
                    ->get([DB::raw('MAX(d.created_at) as created_at'), DB::raw('MAX(p.id_pinjaman) as ref'), DB::raw('MAX(k.nama_karyawan) as name'),
                        DB::raw('MAX(lp.nama) as company'), DB::raw('MAX(l.nama) as lender'), DB::raw('SUM(p.total_angsuran_perbulan) as sisa'),
                        DB::raw('COUNT(*) as open_count'), DB::raw('MIN(jt.jatuh_tempo) as next_due')])
                    ->map(function ($r) use ($at) {
                        $due = CarbonImmutable::parse($r->next_due);
                        $status = $due->lt($at) ? ['crit', 'Menunggak'] : ($due->lte($at->addDays(7)) ? ['warn', 'Jatuh tempo ≤ 7 hari'] : ['good', 'Lancar']);

                        return $this->row($r->created_at, $r->ref, $r->name, $r->company, $r->lender, (float) $r->sisa, $status,
                            $r->open_count.' cicilan tersisa · berikutnya '.InsightPeriod::date($due));
                    })->all();

            case 'late':
                return $people($this->insight->danaInstallmentsOpenAt($at, $lenderId))
                    ->leftJoin('lenders as l', 'l.id', '=', 'p.id_lender')
                    ->where('jt.jatuh_tempo', '<', $at->toDateString())
                    ->get(['jt.jatuh_tempo', 'p.id_pinjaman', 'k.nama_karyawan', 'lp.nama as company', 'l.nama as lender', 'p.total_angsuran_perbulan'])
                    ->map(fn ($r) => $this->row($r->jatuh_tempo, $r->id_pinjaman, $r->nama_karyawan, $r->company, $r->lender, (float) $r->total_angsuran_perbulan,
                        ['crit', 'Lewat '.(int) CarbonImmutable::parse($r->jatuh_tempo)->diffInDays($at).' hari'], null))->all();

            case 'repaid':
                return $this->insight->danaRepayments($period, $lenderId)
                    ->leftJoin('lenders as l', 'l.id', '=', 'lp.id_lender')
                    ->get(['r.tanggal_pembayaran', 'i.no_invoice', 'i.period', 'lp.nama as company', 'l.nama as lender', 'r.amount_total'])
                    ->map(fn ($r) => $this->row($r->tanggal_pembayaran, $r->no_invoice ?: '-', null, $r->company, $r->lender, (float) $r->amount_total, ['good', 'Dibayar'],
                        $r->period ? 'Tagihan periode '.InsightPeriod::date(CarbonImmutable::parse($r->period)) : null))->all();

            case 'applications':
            case 'approval':
                return $people($this->apps('tomidana', $period, $lenderId))
                    ->leftJoin('lenders as l', 'l.id', '=', 'p.id_lender')
                    ->get(['p.created_at', 'p.id_pinjaman', 'k.nama_karyawan', 'lp.nama as company', 'l.nama as lender', 'p.nominal', 'p.status', 'p.status_otp',
                        DB::raw('EXISTS(SELECT 1 FROM disbursements dx WHERE dx.pinjaman_id = p.id) as cair')])
                    ->map(fn ($r) => $this->row($r->created_at, $r->id_pinjaman, $r->nama_karyawan, $r->company, $r->lender, (float) $r->nominal,
                        $r->cair ? ($r->status === 'done' ? ['good', 'Lunas'] : ['good', 'Dicairkan'])
                            : ($r->status_otp !== 'success' ? ['mute', 'Belum OTP'] : ['warn', 'Menunggu pencairan']), null))->all();
        }

        return [];
    }

    private function gajiRows(string $metric, InsightPeriod $period, ?int $lenderId): array
    {
        $dateDisb = 'COALESCE(s.transfer_date, s.created_at)';
        $datePaid = 'COALESCE(s.payment_date, s.updated_at)';
        $base = fn ($q) => $q->leftJoin('list_perusahaans as lp', 'lp.id', '=', 's.id_perusahaan')->leftJoin('lenders as l', 'l.id', '=', 's.lender_id');
        $cols = ['s.id', 's.agreement_number', 's.status', 's.loan_amount', 's.total_dibayarkan', 's.total_fee', 's.total_borrowers', 'l.nama as lender', DB::raw('COALESCE(lp.nama, s.company_name) as company')];
        // NOA penyaluran TomiGaji = per karyawan, jadi satu pengajuan bernilai total_borrowers
        $perEmployee = in_array($metric, ['disbursed', 'noa', 'revenue'], true);
        $map = fn (string $dateCol, callable $amount) => fn ($r) => $this->row($r->{$dateCol}, $r->agreement_number ?: 'TomiGaji #'.$r->id, null, $r->company, $r->lender,
            $amount($r), self::GAJI_STATUS[$r->status] ?? ['mute', $r->status], $r->total_borrowers.' karyawan', $perEmployee ? (int) $r->total_borrowers : 1);

        $q = match ($metric) {
            'disbursed', 'noa', 'revenue' => $this->insight->gajiDisbursed($period, $lenderId),
            'outstanding', 'noa_out' => $this->insight->gajiOpenAt($period->end(), $lenderId),
            'repaid' => $this->insight->gajiQuery($lenderId)->where('s.status', 'lunas')->whereRaw("$datePaid BETWEEN ? AND ?", [$period->start(), $period->end()]),
            'applications', 'approval' => $this->apps('tomigaji', $period, $lenderId),
            default => null,
        };
        if (! $q) {
            return [];
        }
        $dateSql = match ($metric) {
            'repaid' => $datePaid, 'applications', 'approval', 'disbursed', 'noa', 'revenue' => 's.created_at', default => $dateDisb
        };
        $amount = match ($metric) {
            'revenue' => fn ($r) => (float) $r->loan_amount * InsightService::FEE_TOMIGAJI,
            'outstanding', 'noa_out', 'repaid' => fn ($r) => (float) $r->total_dibayarkan,
            default => fn ($r) => (float) $r->loan_amount,
        };

        return $base($q)->get(array_merge($cols, [DB::raw("$dateSql as d_date")]))->map($map('d_date', $amount))->all();
    }

    private function invoiceRows(string $metric, InsightPeriod $period, ?int $lenderId): array
    {
        $base = fn ($q) => $q->leftJoin('vendors as v', 'v.id', '=', 'f.vendor_id')->leftJoin('clients as c', 'c.id', '=', 'f.client_id')
            ->leftJoin('lenders as l', 'l.id', '=', 'f.lender_id');
        $cols = ['f.purchase_order_number', 'f.analyst_decision', 'f.approved_financing_amount', 'f.purchase_order_amount', 'v.name as vendor', 'c.name as client', 'l.nama as lender'];
        $decision = fn ($d) => $d === 'approved' ? ['good', 'Disetujui'] : ($d === 'rejected' ? ['crit', 'Ditolak'] : ['warn', 'Menunggu analis']);

        if (in_array($metric, ['disbursed', 'noa', 'revenue'], true)) {
            return $this->insight->invoiceTerms($period, $lenderId)
                ->leftJoin('vendors as v', 'v.id', '=', 'it.vendor_id')->leftJoin('clients as c', 'c.id', '=', 'it.client_id')
                ->leftJoin('lenders as l', 'l.id', '=', 'it.lender_id')
                ->get(['it.dt', 'it.term_no', 'it.pct', 'it.purchase_order_number', 'it.approved_financing_amount', 'v.name as vendor', 'c.name as client', 'l.nama as lender',
                    DB::raw('('.InsightService::INVOICE_TERM_SQL.') as amt')])
                ->map(fn ($r) => $this->row($r->dt, $r->purchase_order_number ?: '-', $r->vendor, $r->client, $r->lender,
                    $metric === 'revenue' ? (float) $r->amt * InsightService::FEE_TOMIINVOICE : (float) $r->amt,
                    ['info', 'Termin '.$r->term_no.' · '.rtrim(rtrim(number_format((float) $r->pct, 2, ',', '.'), '0'), ',').'%'],
                    ($metric === 'revenue' ? 'Fee 1% dari termin '.$this->rp($r->amt).' · ' : '').'Pembiayaan disetujui '.$this->rp($r->approved_financing_amount)))->all();
        }

        if ($metric === 'repaid') {
            $out = [];
            foreach ([1, 2, 3] as $n) {
                $base($this->insight->invoiceQuery($lenderId))->whereBetween("f.finance_invoice_term_date_$n", [$period->start(), $period->end()])
                    ->get(array_merge($cols, ["f.finance_invoice_term_date_$n as dt", "f.finance_invoice_term_amount_$n as amt"]))
                    ->each(function ($r) use (&$out, $n) {
                        $out[] = $this->row($r->dt, $r->purchase_order_number ?: '-', $r->vendor, $r->client, $r->lender, (float) $r->amt, ['info', 'Termin '.$n], null);
                    });
            }

            return $out;
        }

        if (in_array($metric, ['outstanding', 'noa_out'], true)) {
            $at = $period->end();

            return $base($this->insight->invoiceOpenAt($at, $lenderId))
                ->select(array_merge($cols, ['f.approved_at']))
                ->selectRaw('COALESCE(f.approved_financing_amount, 0) - '.InsightService::INVOICE_BILLED_SQL.' as sisa', [$at, $at, $at])
                ->get()
                ->map(fn ($r) => $this->row($r->approved_at, $r->purchase_order_number ?: '-', $r->vendor, $r->client, $r->lender, (float) $r->sisa, ['info', 'Berjalan'],
                    'Disetujui '.$this->rp($r->approved_financing_amount)))->all();
        }

        $q = match ($metric) {
            'applications', 'approval', 'po_amount' => $this->apps('tomiinvoice', $period, $lenderId),
            default => null,
        };
        if (! $q) {
            return [];
        }

        return $base($q)->get(array_merge($cols, [DB::raw('f.created_at as d_date')]))
            ->map(fn ($r) => $this->row($r->d_date, $r->purchase_order_number ?: '-', $r->vendor, $r->client, $r->lender,
                (float) ($metric === 'po_amount' ? $r->purchase_order_amount : ($r->approved_financing_amount ?: $r->purchase_order_amount)),
                $decision($r->analyst_decision), null))->all();
    }

    private function gawaiRows(string $metric, InsightPeriod $period, ?int $lenderId): array
    {
        $q = match ($metric) {
            'disbursed', 'noa' => $this->insight->gawaiQuery($lenderId)->whereIn('g.status', ['approved', 'paid'])->whereBetween('g.created_at', [$period->start(), $period->end()]),
            'outstanding', 'noa_out' => $this->insight->gawaiOpenAt($period->end(), $lenderId),
            'repaid' => $this->insight->gawaiQuery($lenderId)->where('g.status', 'paid')->whereBetween('g.updated_at', [$period->start(), $period->end()]),
            'applications', 'approval' => $this->apps('tomigawai', $period, $lenderId),
            default => null,
        };
        if (! $q) {
            return [];
        }
        $status = ['approved' => ['info', 'Berjalan'], 'paid' => ['good', 'Lunas'], 'pending' => ['warn', 'Menunggu'], 'rejected' => ['crit', 'Ditolak']];

        return $q->leftJoin('users as u', 'u.id', '=', 'g.user_id')->leftJoin('lenders as l', 'l.id', '=', 'g.lender_id')
            ->get(['g.created_at', 'g.updated_at', 'g.invoice_number', 'g.status', 'g.total_amount', 'g.tenor_months', 'u.nama_lengkap', 'l.nama as lender'])
            ->map(fn ($r) => $this->row($metric === 'repaid' ? $r->updated_at : $r->created_at, $r->invoice_number ?: '-', $r->nama_lengkap, null, $r->lender,
                (float) $r->total_amount, $status[$r->status] ?? ['mute', $r->status], $r->tenor_months ? 'Tenor '.$r->tenor_months.' bln' : null))->all();
    }

    /* ===================================================================== */
    /* Ringkasan, pecahan, kolom, tautan */
    /* ===================================================================== */

    /** $units = bobot NOA baris ini (upload manual = jumlah peminjam, TomiGaji = jumlah karyawan, lainnya 1). */
    private function row($date, $ref, $name, $company, $lender, float $amount, ?array $status, ?string $extra, int $units = 1): array
    {
        return [
            'date' => $date ? CarbonImmutable::parse($date)->format('Y-m-d H:i') : null,
            'ref' => $ref,
            'name' => $name,
            'company' => $company,
            'lender' => $lender,
            'status' => $status ? ['tone' => $status[0], 'label' => $status[1]] : null,
            'amount' => $amount,
            'extra' => $extra,
            'units' => $units,
        ];
    }

    private function summarize(array $rows, string $kind): array
    {
        $count = count($rows);
        $sum = array_sum(array_column($rows, 'amount'));
        $value = match ($kind) {
            'count' => array_sum(array_column($rows, 'units')),
            'rate' => $count > 0 ? count(array_filter($rows, fn ($r) => in_array($r['status']['label'] ?? '', ['Dicairkan', 'Lunas', 'Disetujui', 'Berjalan', 'Sudah ditransfer', 'Bukti bayar masuk'], true))) / $count : null,
            default => $sum,
        };

        return ['value' => $value, 'count' => $count, 'amount' => $sum];
    }

    /** Pecahan per produk / pemberi dana / perusahaan / status, nilai = nominal atau jumlah akun sesuai jenis metrik. */
    private function breakdowns(array $rows, string $kind, bool $byProduct): array
    {
        $money = in_array($kind, ['money', 'sum'], true);
        $group = function (callable $key, int $limit = 8) use ($rows, $money) {
            $agg = [];
            foreach ($rows as $r) {
                $k = $key($r);
                $agg[$k] ??= ['label' => $k, 'value' => 0.0, 'count' => 0, 'slot' => $r['slot'] ?? null, 'tone' => $r['status']['tone'] ?? null];
                $agg[$k]['value'] += $money ? $r['amount'] : ($r['units'] ?? 1);
                $agg[$k]['count']++;
            }
            usort($agg, fn ($a, $b) => $b['value'] <=> $a['value']);
            if (count($agg) > $limit) {
                $rest = array_slice($agg, $limit - 1);
                $agg = array_slice($agg, 0, $limit - 1);
                $agg[] = ['label' => 'Lainnya ('.count($rest).')', 'value' => array_sum(array_column($rest, 'value')), 'count' => array_sum(array_column($rest, 'count')), 'slot' => null, 'tone' => null];
            }

            return array_values($agg);
        };

        $out = [];
        if ($byProduct) {
            $out[] = ['key' => 'product', 'title' => 'Per produk', 'items' => $group(fn ($r) => $this->fieldValue($r, 'product'), 4)];
        }
        $out[] = ['key' => 'lender', 'title' => 'Per pemberi dana', 'items' => $group(fn ($r) => $this->fieldValue($r, 'lender'))];
        if (array_filter(array_column($rows, 'company'))) {
            $out[] = ['key' => 'company', 'title' => 'Per perusahaan / mitra', 'items' => $group(fn ($r) => $this->fieldValue($r, 'company'))];
        }
        if (array_filter(array_column($rows, 'status'))) {
            $out[] = ['key' => 'status', 'title' => 'Per status', 'items' => $group(fn ($r) => $this->fieldValue($r, 'status'))];
        }

        return array_values(array_filter($out, fn ($b) => count($b['items']) > 0));
    }

    /** Nilai kolom untuk pengelompokan & penyaringan (kosong = "-", sama dengan label di dasbor). */
    private function fieldValue(array $r, string $key): string
    {
        $v = $key === 'status' ? ($r['status']['label'] ?? null) : ($r[$key] ?? null);

        return ($v === null || $v === '') ? '-' : (string) $v;
    }

    private function columns(array $rows, bool $overview, string $dateLabel, string $valueLabel, string $metric, string $scope): array
    {
        $has = fn (string $k) => (bool) array_filter(array_column($rows, $k), fn ($v) => $v !== null && $v !== '');
        $nameLabel = $scope === 'tomiinvoice' ? 'Pelanggan' : 'Peminjam';
        $companyLabel = $scope === 'tomiinvoice' ? 'Mitra' : 'Perusahaan';
        if ($metric === 'repaid' && $scope === 'overview') {
            $dateLabel = 'Tanggal bayar / tagih';
        }
        $cols = [['key' => 'date', 'label' => $dateLabel, 'type' => 'date', 'sortable' => true]];
        if ($overview) {
            $cols[] = ['key' => 'product', 'label' => 'Produk', 'type' => 'product'];
        }
        $cols[] = ['key' => 'ref', 'label' => $metric === 'repaid' ? 'Referensi' : 'Akun', 'type' => 'strong'];
        foreach ([['name', $nameLabel], ['company', $companyLabel], ['lender', 'Pemberi dana'], ['extra', 'Keterangan']] as [$k, $l]) {
            if ($has($k)) {
                $cols[] = ['key' => $k, 'label' => $l, 'type' => 'text'];
            }
        }
        if ($has('status')) {
            $cols[] = ['key' => 'status', 'label' => 'Status', 'type' => 'badge'];
        }
        if (in_array($metric, ['disbursed', 'noa', 'revenue'], true) && array_filter($rows, fn ($r) => ($r['units'] ?? 1) !== 1)) {
            $cols[] = ['key' => 'units', 'label' => 'NOA', 'type' => 'num'];
        }
        $cols[] = ['key' => 'amount', 'label' => $valueLabel, 'type' => 'money', 'sortable' => true];

        return $cols;
    }

    private function definition(string $scope, string $metric): string
    {
        $all = $scope === 'overview';
        $def = [
            'disbursed' => 'Penyaluran pada periode ini (definisi catatan kantor): TomiDana = nominal pinjaman disetujui menurut tanggal pengajuan + upload manual menu Buat Tagihan; TomiGaji = nilai pembiayaan menurut tanggal pengajuan; Tomi Invoice = pembayaran ke pelanggan per termin (pembiayaan disetujui × % termin) menurut tanggal bayar; TomiGawai = cicilan disetujui.',
            'noa' => 'NOA penyaluran (definisi catatan kantor): TomiDana 1 per pinjaman aplikasi + jumlah peminjam di upload manual; TomiGaji per karyawan; Tomi Invoice per termin pembayaran ke pelanggan; TomiGawai per cicilan.',
            'noa_out' => 'NOA outstanding = jumlah akun yang masih berjalan (belum lunas) pada tanggal akhir periode.',
            'outstanding' => 'Sisa yang belum kembali pada tanggal akhir periode. TomiDana: cicilan belum lunas × angsuran per bulan; TomiGaji: pengajuan cair yang belum lunas; Tomi Invoice: pembiayaan disetujui dikurangi termin yang sudah ditagihkan; TomiGawai: cicilan berjalan.',
            'repaid' => $all ? 'Uang yang kembali pada periode ini: pembayaran tagihan TomiDana, pelunasan TomiGaji, termin Tomi Invoice yang ditagihkan, cicilan TomiGawai yang lunas.'
                : 'Pembayaran / pelunasan yang tercatat pada periode ini.',
            'revenue' => 'Pendapatan bagian TOMI (definisi catatan kantor, tanpa bagian pemberi dana): TomiDana = 1% × nominal pinjaman + biaya layanan; upload manual = fee dari file Buat Tagihan; TomiGaji = 1,5% × nilai pembiayaan; Tomi Invoice = 1% × nilai termin dibayar ke pelanggan.',
            'late' => 'Cicilan TomiDana yang sudah lewat jatuh tempo dan belum lunas pada tanggal akhir periode.',
            'applications' => 'Pengajuan yang masuk pada periode ini, apa pun statusnya.',
            'approval' => 'Persentase pengajuan pada periode ini yang sudah disetujui / dicairkan.',
            'po_amount' => 'Total nilai pesanan pembelian (PO) dari tagihan yang diajukan pada periode ini.',
        ];

        return $def[$metric];
    }

    /** Halaman operasional terkait yang boleh dibuka akun ini. */
    private function links(string $scope, string $metric, array $products): array
    {
        $map = [
            'tomidana' => in_array($metric, ['repaid', 'late'], true) ? ['admin.ksp.invoicing.index', 'Buka Daftar Tagihan Perusahaan'] : ['admin.ksp.index', 'Buka Perusahaan (pengajuan)'],
            'tomigaji' => ['admin.salary-financing.index', 'Buka Pemantauan TomiGaji'],
            'tomiinvoice' => ['admin.invoice-financing.index', 'Buka Pemantauan TomiInvoice'],
            'tomigawai' => ['admin.gadget-installment.index', 'Buka Pemantauan TomiGawai'],
        ];
        $keys = $scope === 'overview' ? array_values(array_intersect(array_keys($map), $products)) : [$scope];
        $out = [];
        foreach ($keys as $k) {
            if ($scope === 'overview' && ! self::supports($k, $metric)) {
                continue;
            }
            [$route, $label] = $map[$k];
            if (Route::has($route)) {
                $out[] = ['label' => $label, 'url' => route($route)];
            }
        }

        return $out;
    }

    private function rp($value): string
    {
        $v = (float) $value;

        return $v >= 1e6 ? 'Rp '.number_format($v / 1e6, 1, ',', '.').' jt' : 'Rp '.number_format($v, 0, ',', '.');
    }
}
