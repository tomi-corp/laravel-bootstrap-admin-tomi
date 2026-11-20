<?php

namespace App\Http\Controllers\Internal\TomiInvoice;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Internal\Concerns\ScopesCompanies;
use App\Models\CompanyPks;
use App\Models\Feature;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Dashboard TomiInvoice: KPI pembiayaan tagihan (financing_request_invoices) dan PKS, dibatasi perusahaan yang boleh diakses admin.
 */
class DashboardController extends Controller
{
    use ScopesCompanies;

    private const FEATURE_CODE = 'tomiinvoice';

    public function index(Request $request)
    {
        [$from, $to, $periodLabel] = $this->period($request);
        $companyIds = $this->visibleCompanies()->select('list_perusahaans.id');
        $num = fn ($value) => number_format((float) $value, 0, ',', '.');
        $rupiah = fn ($value) => 'Rp. '.number_format((float) $value, 0, ',', '.');

        $requests = fn () => DB::table('financing_request_invoices')
            ->whereNull('deleted_at')
            ->whereIn('company_id', $companyIds);
        $submitted = $requests()->when($from, fn ($q) => $q->whereBetween('created_at', [$from, $to]));
        $approved = $requests()
            ->where('analyst_decision', 'approved')
            ->when($from, fn ($q) => $q->whereBetween(DB::raw('COALESCE(approved_at, updated_at)'), [$from, $to]));

        $kpis = [
            ['label' => 'PENGAJUAN MASUK', 'value' => $num((clone $submitted)->count()), 'sub' => 'nilai PO '.$rupiah((clone $submitted)->sum('purchase_order_amount')), 'icon' => 'mdi-file-document-outline', 'color' => 'info'],
            ['label' => 'PEMBIAYAAN DISETUJUI', 'value' => $rupiah((clone $approved)->sum('approved_financing_amount')), 'sub' => $num((clone $approved)->count()).' pengajuan disetujui', 'icon' => 'mdi-check-decagram-outline', 'color' => 'success'],
            ['label' => 'MENUNGGU ANALIS', 'value' => $num($requests()->where(fn ($q) => $q->whereNull('analyst_decision')->orWhereIn('analyst_decision', ['pending', 'revision']))->count()), 'sub' => 'belum diputuskan saat ini', 'icon' => 'mdi-file-clock-outline', 'color' => 'warning'],
            ['label' => 'DITOLAK', 'value' => $num($requests()->where('analyst_decision', 'rejected')->when($from, fn ($q) => $q->whereBetween('updated_at', [$from, $to]))->count()), 'sub' => 'pengajuan ditolak analis', 'icon' => 'mdi-file-cancel-outline', 'color' => 'danger'],
        ];

        return view('internal.tomi-invoice.dashboard.index', [
            'kpis' => $kpis,
            'pks' => $this->pksStats(),
            'periodLabel' => $periodLabel,
            'filter' => $this->filterValues($request),
        ]);
    }

    /**
     * Periode dari query: periode=bulan (default, bulan=Y-m) / tahun (tahun=Y) / semua.
     *
     * @return array{0: ?Carbon, 1: ?Carbon, 2: string}
     */
    private function period(Request $request): array
    {
        $filter = $this->filterValues($request);

        if ($filter['periode'] === 'semua') {
            return [null, null, 'Semua waktu'];
        }

        if ($filter['periode'] === 'tahun') {
            $start = Carbon::create((int) $filter['tahun'], 1, 1)->startOfDay();

            return [$start, $start->copy()->endOfYear(), 'Tahun '.$start->year];
        }

        $start = Carbon::createFromFormat('Y-m', $filter['bulan'])->startOfMonth();

        return [$start, $start->copy()->endOfMonth(), $start->isoFormat('MMMM Y')];
    }

    private function filterValues(Request $request): array
    {
        $periode = in_array($request->query('periode'), ['bulan', 'tahun', 'semua'], true) ? $request->query('periode') : 'bulan';
        $bulan = preg_match('/^\d{4}-\d{2}$/', (string) $request->query('bulan')) ? $request->query('bulan') : now()->format('Y-m');
        $tahun = preg_match('/^\d{4}$/', (string) $request->query('tahun')) ? $request->query('tahun') : now()->format('Y');

        return compact('periode', 'bulan', 'tahun');
    }

    /** PKS TomiInvoice: PKS terakhir per perusahaan (berakhir paling lambat; tanpa tanggal berakhir = aktif). */
    private function pksStats(): array
    {
        $today = Carbon::today();
        $featureId = Feature::where('code', self::FEATURE_CODE)->value('id');

        $latest = CompanyPks::query()
            ->where('feature_id', $featureId)
            ->whereIn('company_id', $this->visibleCompanies()->select('list_perusahaans.id'))
            ->get(['company_id', 'end_date'])
            ->groupBy('company_id')
            ->map(fn ($rows) => $rows->contains(fn ($row) => $row->end_date === null) ? null : $rows->max(fn ($row) => Carbon::parse($row->end_date)));

        $activeCompanies = $latest->isEmpty() ? 0 : $this->visibleCompanies()->whereIn('list_perusahaans.id', $latest->keys())->where('status', 'active')->count();

        return [
            'companies' => $latest->count(),
            'active_companies' => $activeCompanies,
            'active' => $latest->filter(fn ($end) => $end === null || $end->gte($today))->count(),
            'expiring' => $latest->filter(fn ($end) => $end !== null && $end->between($today, $today->copy()->addDays(30)))->count(),
            'expired' => $latest->filter(fn ($end) => $end !== null && $end->lt($today))->count(),
        ];
    }
}
