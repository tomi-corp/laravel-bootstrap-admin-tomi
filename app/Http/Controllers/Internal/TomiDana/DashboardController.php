<?php

namespace App\Http\Controllers\Internal\TomiDana;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Internal\Concerns\ScopesCompanies;
use App\Models\CompanyPks;
use App\Models\Feature;
use App\Models\invoicing_pinjaman;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Dashboard TomiDana: ringkasan kinerja produk (pengajuan, pencairan, outstanding, pendapatan TOMI),
 * tren bulanan, status pengajuan, perusahaan mitra, dan insight. Dibatasi perusahaan yang boleh diakses admin.
 */
class DashboardController extends Controller
{
    use ScopesCompanies;

    private const FEATURE_CODE = 'tomidana';

    /**
     * Pendapatan TOMI per pinjaman (sama dengan perhitungan revenue invoice lender / Biaya Aplikasi):
     * sebelum 17 April tahun pinjaman = 3% + Rp 20.000, sesudahnya = Rp 10.000 + 1%.
     */
    private const REVENUE_SQL = "CASE WHEN DATE(p.created_at) < STR_TO_DATE(CONCAT(YEAR(p.created_at), '-04-17'), '%Y-%m-%d') THEN (COALESCE(p.nominal, 0) * 0.03) + 20000 ELSE 10000 + (COALESCE(p.nominal, 0) * 0.01) END";

    public function index(Request $request)
    {
        $filter = $this->filterValues($request);
        [$from, $to, $periodLabel, $prevFrom, $prevTo, $prevLabel] = $this->period($filter);

        $current = $this->totals($from, $to);
        $previous = $prevFrom ? $this->totals($prevFrom, $prevTo) : null;

        $kpis = [
            ['label' => 'Total Pengajuan', 'icon' => 'mdi-file-document-outline', 'tone' => 'blue', 'value' => $current['applications'], 'prev' => $previous['applications'] ?? null, 'sub' => $this->num($current['application_count']).' pengajuan'],
            ['label' => 'Total Pencairan', 'icon' => 'mdi-cash-fast', 'tone' => 'green', 'value' => $current['disbursed'], 'prev' => $previous['disbursed'] ?? null, 'sub' => $this->num($current['disbursed_count']).' pinjaman dicairkan'],
            ['label' => 'Outstanding', 'icon' => 'mdi-layers-outline', 'tone' => 'violet', 'value' => $current['outstanding'], 'prev' => null, 'sub' => 'sisa angsuran per hari ini'],
            ['label' => 'Pendapatan TOMI', 'icon' => 'mdi-chart-bar', 'tone' => 'pink', 'value' => $current['revenue'], 'prev' => $previous['revenue'] ?? null, 'sub' => 'biaya aplikasi dari pinjaman dicairkan'],
        ];
        foreach ($kpis as &$kpi) {
            $kpi['delta'] = $this->delta($kpi['value'], $kpi['prev']);
        }
        unset($kpi);

        $companies = $this->partnerCompanies($from, $to);

        return view('internal.tomi-dana.dashboard.index', [
            'kpis' => $kpis,
            'prevLabel' => $prevLabel,
            'trend' => $this->trend($filter),
            'status' => $this->statusBreakdown($from, $to),
            'companies' => $companies,
            'insights' => $this->insights($current, $previous, $prevLabel, $companies),
            'periodLabel' => $periodLabel,
            'filter' => $filter,
            'products' => [
                ['name' => 'TomiDana', 'route' => 'internal.tomi-dana.dashboard', 'active' => true],
                ['name' => 'TomiDanaPlus', 'route' => 'internal.tomi-dana-plus.dashboard', 'active' => false],
                ['name' => 'TomiGaji', 'route' => 'internal.tomi-gaji.dashboard', 'active' => false],
                ['name' => 'TomiInvoice', 'route' => 'internal.tomi-invoice.dashboard', 'active' => false],
                ['name' => 'TomiGawai', 'route' => 'internal.tomi-gawai.dashboard', 'active' => false],
            ],
        ]);
    }

    /** Total KPI pada rentang tanggal (null = semua waktu). */
    private function totals(?Carbon $from, ?Carbon $to): array
    {
        $applications = $this->loans()->when($from, fn ($q) => $q->whereBetween('p.created_at', [$from, $to]));
        $disbursed = $this->disbursedLoans()->when($from, fn ($q) => $q->whereBetween('d.created_at', [$from, $to]));

        // Outstanding = angsuran yang belum dibayar x angsuran per bulan pinjaman itu (posisi saat ini).
        $outstanding = DB::table('jatuh_tempos as j')
            ->join('pinjamen as p', 'p.id', '=', 'j.id_pinjaman')
            ->whereIn('p.id_perusahaan', $this->visibleCompanies()->select('list_perusahaans.id'))
            ->whereNull('p.deleted_at')
            ->whereNull('j.deleted_at')
            ->where('j.status', '!=', 'lunas')
            ->whereIn('p.status', ['approve'])
            ->sum('p.total_angsuran_perbulan');

        return [
            'applications' => (float) (clone $applications)->sum('p.nominal'),
            'application_count' => (clone $applications)->count(),
            'disbursed' => (float) (clone $disbursed)->sum('d.dana_yang_dicairkan'),
            'disbursed_count' => (clone $disbursed)->count(),
            'outstanding' => (float) $outstanding,
            'revenue' => (float) (clone $disbursed)->sum(DB::raw(self::REVENUE_SQL)),
        ];
    }

    /** Tren bulanan: pencairan (batang) dan pendapatan TOMI (garis). */
    private function trend(array $filter): array
    {
        if ($filter['periode'] === 'tahun') {
            $start = Carbon::create((int) $filter['tahun'], 1, 1);
            $months = 12;
        } else {
            $end = $filter['periode'] === 'bulan' ? Carbon::createFromFormat('Y-m', $filter['bulan']) : Carbon::now();
            $start = $end->copy()->startOfMonth()->subMonths(9);
            $months = 10;
        }

        $rows = $this->disbursedLoans()
            ->whereBetween('d.created_at', [$start->copy()->startOfMonth(), $start->copy()->addMonths($months - 1)->endOfMonth()])
            ->selectRaw("DATE_FORMAT(d.created_at, '%Y-%m') as period")
            ->selectRaw('SUM(d.dana_yang_dicairkan) as disbursed')
            ->selectRaw('SUM('.self::REVENUE_SQL.') as revenue')
            ->groupBy('period')
            ->get()
            ->keyBy('period');

        $labels = $disbursed = $revenue = [];
        for ($i = 0; $i < $months; $i++) {
            $month = $start->copy()->addMonths($i);
            $labels[] = [$month->isoFormat('MMM'), $month->format('Y')];
            $disbursed[] = round((float) ($rows[$month->format('Y-m')]->disbursed ?? 0));
            $revenue[] = round((float) ($rows[$month->format('Y-m')]->revenue ?? 0));
        }

        return compact('labels', 'disbursed', 'revenue');
    }

    /** Status pengajuan pada periode. */
    private function statusBreakdown(?Carbon $from, ?Carbon $to): array
    {
        $counts = $this->loans()
            ->when($from, fn ($q) => $q->whereBetween('p.created_at', [$from, $to]))
            ->selectRaw('p.status, COUNT(*) as total')
            ->groupBy('p.status')
            ->pluck('total', 'status');

        // token = variabel warna tema (resources/css/ui-core.css), dipakai view & grafik.
        $items = [
            ['label' => 'Disetujui', 'token' => '--s1', 'value' => (int) ($counts['approve'] ?? 0)],
            ['label' => 'Proses', 'token' => '--warn-fill', 'value' => (int) (($counts['on_proses'] ?? 0) + ($counts['pending'] ?? 0))],
            ['label' => 'Selesai', 'token' => '--good-fill', 'value' => (int) ($counts['done'] ?? 0)],
        ];
        if (($counts['reject'] ?? 0) > 0) {
            $items[] = ['label' => 'Ditolak', 'token' => '--crit-fill', 'value' => (int) $counts['reject']];
        }

        $total = array_sum(array_column($items, 'value'));
        foreach ($items as &$item) {
            $item['percent'] = $total > 0 ? round($item['value'] / $total * 100, 1) : 0;
        }
        unset($item);

        return ['total' => $total, 'items' => $items];
    }

    /** Perusahaan ber-PKS TomiDana: jumlah karyawan terverifikasi dan nominal pencairan pada periode. */
    private function partnerCompanies(?Carbon $from, ?Carbon $to): array
    {
        $featureId = Feature::where('code', self::FEATURE_CODE)->value('id');
        $companyIds = CompanyPks::where('feature_id', $featureId)
            ->whereIn('company_id', $this->visibleCompanies()->select('list_perusahaans.id'))
            ->distinct()
            ->pluck('company_id');

        $disbursed = $this->disbursedLoans()
            ->whereIn('p.id_perusahaan', $companyIds)
            ->when($from, fn ($q) => $q->whereBetween('d.created_at', [$from, $to]))
            ->selectRaw('p.id_perusahaan, SUM(d.dana_yang_dicairkan) as total')
            ->groupBy('p.id_perusahaan')
            ->pluck('total', 'id_perusahaan');

        $employees = DB::table('list_karyawans')
            ->whereNull('deleted_at')
            ->whereIn('id_list_perusahaan', $companyIds)
            ->where('approve_satu', 'approve')
            ->where('approve_dua', 'approve')
            ->selectRaw('id_list_perusahaan, COUNT(*) as total')
            ->groupBy('id_list_perusahaan')
            ->pluck('total', 'id_list_perusahaan');

        return $this->visibleCompanies()
            ->whereIn('list_perusahaans.id', $companyIds)
            ->get(['list_perusahaans.id', 'list_perusahaans.nama', 'list_perusahaans.status'])
            ->map(fn ($company) => [
                'id' => $company->id,
                'name' => Str::upper($company->nama),
                'employees' => (int) ($employees[$company->id] ?? 0),
                'nominal' => (float) ($disbursed[$company->id] ?? 0),
                'active' => $company->status === 'active',
            ])
            ->sortByDesc('nominal')
            ->values()
            ->take(5)
            ->all();
    }

    /** Kalimat insight dari data periode ini. */
    private function insights(array $current, ?array $previous, ?string $prevLabel, array $companies): array
    {
        $lines = [];

        $delta = $this->delta($current['disbursed'], $previous['disbursed'] ?? null);
        if ($delta) {
            $lines[] = 'Pencairan TomiDana '.($delta['up'] ? 'naik' : 'turun').' '.$delta['text'].' dibanding '.$prevLabel.'.';
        } elseif ($current['disbursed'] > 0) {
            $lines[] = 'Total pencairan TomiDana mencapai '.$this->rupiah($current['disbursed']).'.';
        } else {
            $lines[] = 'Belum ada pencairan TomiDana pada periode ini.';
        }

        $top = collect($companies)->firstWhere('nominal', '>', 0);
        if ($top) {
            $lines[] = 'Pencairan terbesar dari '.$top['name'].' sebesar '.$this->rupiah($top['nominal']).'.';
        }

        $unpaid = invoicing_pinjaman::query()
            ->whereIn('id_list_perusahaan', $this->visibleCompanies()->select('list_perusahaans.id'))
            ->where('status', 'belum_lunas')
            ->count();
        if ($unpaid > 0) {
            $lines[] = $this->num($unpaid).' tagihan perusahaan masih belum lunas.';
        }

        return $lines;
    }

    /** Pengajuan TomiDana tervalidasi WhatsApp milik perusahaan yang boleh diakses (alias p). */
    private function loans(): Builder
    {
        return DB::table('pinjamen as p')
            ->whereNull('p.deleted_at')
            ->where('p.status_otp', 'success')
            ->whereIn('p.id_perusahaan', $this->visibleCompanies()->select('list_perusahaans.id'));
    }

    /** Pinjaman yang sudah dicairkan (alias p = pinjamen, d = disbursements). */
    private function disbursedLoans(): Builder
    {
        return DB::table('disbursements as d')
            ->join('pinjamen as p', 'p.id', '=', 'd.pinjaman_id')
            ->whereNull('d.deleted_at')
            ->whereNull('p.deleted_at')
            ->whereIn('p.id_perusahaan', $this->visibleCompanies()->select('list_perusahaans.id'));
    }

    /**
     * Periode terpilih beserta periode pembanding.
     *
     * @return array{0: ?Carbon, 1: ?Carbon, 2: string, 3: ?Carbon, 4: ?Carbon, 5: ?string}
     */
    private function period(array $filter): array
    {
        if ($filter['periode'] === 'semua') {
            return [null, null, 'Semua waktu', null, null, null];
        }

        if ($filter['periode'] === 'tahun') {
            $start = Carbon::create((int) $filter['tahun'], 1, 1)->startOfDay();
            $prev = $start->copy()->subYear();

            return [$start, $start->copy()->endOfYear(), 'Tahun '.$start->year, $prev, $prev->copy()->endOfYear(), 'tahun '.$prev->year];
        }

        $start = Carbon::createFromFormat('Y-m', $filter['bulan'])->startOfMonth();
        $prev = $start->copy()->subMonthNoOverflow()->startOfMonth();

        return [$start, $start->copy()->endOfMonth(), $start->isoFormat('MMMM Y'), $prev, $prev->copy()->endOfMonth(), $prev->isoFormat('MMMM Y')];
    }

    private function filterValues(Request $request): array
    {
        $periode = in_array($request->query('periode'), ['bulan', 'tahun', 'semua'], true) ? $request->query('periode') : 'bulan';
        $bulan = preg_match('/^\d{4}-\d{2}$/', (string) $request->query('bulan')) ? $request->query('bulan') : now()->format('Y-m');
        $tahun = preg_match('/^\d{4}$/', (string) $request->query('tahun')) ? $request->query('tahun') : now()->format('Y');

        return compact('periode', 'bulan', 'tahun');
    }

    /** Persentase perubahan; null jika tidak ada pembanding atau pembanding 0. */
    private function delta(float $current, ?float $previous): ?array
    {
        if ($previous === null || $previous == 0) {
            return null;
        }

        $percent = ($current - $previous) / $previous * 100;

        return ['up' => $percent >= 0, 'text' => $this->num(abs($percent)).'%'];
    }

    private function num($value): string
    {
        return number_format((float) $value, 0, ',', '.');
    }

    private function rupiah($value): string
    {
        return 'Rp. '.number_format((float) $value, 0, ',', '.');
    }
}
