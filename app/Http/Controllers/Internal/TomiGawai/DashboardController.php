<?php

namespace App\Http\Controllers\Internal\TomiGawai;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Internal\Concerns\ScopesCompanies;
use App\Models\CompanyPks;
use App\Models\Feature;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Dashboard TomiGawai: KPI cicilan gadget (gadget_installments) dan PKS.
 * Cicilan gadget tidak menyimpan perusahaan, jadi KPI tidak ditampilkan untuk admin PIC (hanya PKS perusahaannya).
 */
class DashboardController extends Controller
{
    use ScopesCompanies;

    private const FEATURE_CODE = 'tomigawai';

    public function index(Request $request)
    {
        [$from, $to, $periodLabel] = $this->period($request);
        $num = fn ($value) => number_format((float) $value, 0, ',', '.');
        $rupiah = fn ($value) => 'Rp. '.number_format((float) $value, 0, ',', '.');

        $kpis = [];
        if (auth('admin')->user()?->type !== 'pic') {
            $installments = fn () => DB::table('gadget_installments')->whereNull('deleted_at');
            $approved = $installments()
                ->whereIn('status', ['approved', 'paid'])
                ->when($from, fn ($q) => $q->whereBetween('created_at', [$from, $to]));

            $kpis = [
                ['label' => 'PENGAJUAN MASUK', 'value' => $num($installments()->when($from, fn ($q) => $q->whereBetween('created_at', [$from, $to]))->count()), 'sub' => 'pengajuan cicilan gadget', 'icon' => 'mdi-file-document-outline', 'color' => 'info'],
                ['label' => 'CICILAN DISETUJUI', 'value' => $rupiah((clone $approved)->sum('total_amount')), 'sub' => $num((clone $approved)->count()).' cicilan', 'icon' => 'mdi-check-decagram-outline', 'color' => 'primary'],
                ['label' => 'CICILAN BERJALAN', 'value' => $rupiah($installments()->where('status', 'approved')->sum('total_amount')), 'sub' => $num($installments()->where('status', 'approved')->count()).' cicilan belum lunas', 'icon' => 'mdi-cellphone-arrow-down', 'color' => 'success'],
                ['label' => 'CICILAN LUNAS', 'value' => $num($installments()->where('status', 'paid')->when($from, fn ($q) => $q->whereBetween('updated_at', [$from, $to]))->count()), 'sub' => 'cicilan selesai', 'icon' => 'mdi-cash-check', 'color' => 'warning'],
            ];
        }

        return view('internal.tomi-gawai.dashboard.index', [
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

    /** PKS TomiGawai: PKS terakhir per perusahaan (berakhir paling lambat; tanpa tanggal berakhir = aktif). */
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
