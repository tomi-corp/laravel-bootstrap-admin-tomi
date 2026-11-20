<?php

namespace App\Http\Controllers\Internal\TomiDanaPlus;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Internal\Concerns\ScopesCompanies;
use App\Models\CompanyPks;
use App\Models\Feature;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Dashboard TomiDana Plus: KPI pinjaman dari tabel loan_plus dan PKS, dibatasi perusahaan yang boleh diakses admin.
 */
class DashboardController extends Controller
{
    use ScopesCompanies;

    private const FEATURE_CODE = 'tomidanaplus';

    public function index(Request $request)
    {
        [$from, $to, $periodLabel] = $this->period($request);
        $companyIds = $this->visibleCompanies()->select('list_perusahaans.id');
        $num = fn ($value) => number_format((float) $value, 0, ',', '.');
        $rupiah = fn ($value) => 'Rp. '.number_format((float) $value, 0, ',', '.');

        $loans = fn () => DB::table('loan_plus')
            ->whereNull('deleted_at')
            ->whereIn('company_id', $companyIds)
            ->where('otp_status', 'success');
        $disbursed = DB::table('loan_plus_disbursements as d')
            ->join('loan_plus as l', 'l.id', '=', 'd.loan_id')
            ->whereNull('d.deleted_at')
            ->whereNull('l.deleted_at')
            ->whereIn('l.company_id', $companyIds)
            ->when($from, fn ($q) => $q->whereBetween('d.created_at', [$from, $to]));
        $unpaidInvoices = DB::table('loan_plus_invoices')
            ->whereNull('deleted_at')
            ->whereIn('company_id', $companyIds)
            ->where('status', 'unpaid');

        $kpis = [
            ['label' => 'PENGAJUAN MASUK', 'value' => $num($loans()->when($from, fn ($q) => $q->whereBetween('created_at', [$from, $to]))->count()), 'sub' => 'tervalidasi WhatsApp', 'icon' => 'mdi-file-document-outline', 'color' => 'info'],
            ['label' => 'PENYALURAN', 'value' => $rupiah((clone $disbursed)->sum('d.disbursed_amount')), 'sub' => $num((clone $disbursed)->count()).' pinjaman dicairkan', 'icon' => 'mdi-bank-transfer-out', 'color' => 'primary'],
            ['label' => 'PINJAMAN BERJALAN', 'value' => $rupiah($loans()->where('status', 'approved')->sum('principal_amount')), 'sub' => $num($loans()->where('status', 'approved')->count()).' pinjaman aktif saat ini', 'icon' => 'mdi-cash-multiple', 'color' => 'success'],
            ['label' => 'PINJAMAN LUNAS', 'value' => $num($loans()->where('status', 'completed')->when($from, fn ($q) => $q->whereBetween('updated_at', [$from, $to]))->count()), 'sub' => 'pinjaman selesai', 'icon' => 'mdi-check-decagram-outline', 'color' => 'success'],
            ['label' => 'TAGIHAN BELUM DIBAYAR', 'value' => $num((clone $unpaidInvoices)->count()), 'sub' => 'invoice perusahaan', 'icon' => 'mdi-file-alert-outline', 'color' => 'warning'],
        ];

        return view('internal.tomi-dana-plus.dashboard.index', [
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

    /** PKS TomiDana Plus: PKS terakhir per perusahaan (berakhir paling lambat; tanpa tanggal berakhir = aktif). */
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
