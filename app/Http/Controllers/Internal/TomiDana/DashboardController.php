<?php

namespace App\Http\Controllers\Internal\TomiDana;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Internal\Concerns\ScopesCompanies;
use App\Models\CompanyPks;
use App\Models\disbursement;
use App\Models\Feature;
use App\Models\invoicing_pinjaman;
use App\Models\list_karyawan;
use App\Models\pinjaman;
use App\Models\repayment;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Dashboard TomiDana: KPI pinjaman, PKS, dan pekerjaan operasional, dibatasi perusahaan yang boleh diakses admin.
 */
class DashboardController extends Controller
{
    use ScopesCompanies;

    private const FEATURE_CODE = 'tomidana';

    public function index(Request $request)
    {
        [$from, $to, $periodLabel] = $this->period($request);
        $companyIds = $this->visibleCompanies()->select('list_perusahaans.id');
        $num = fn ($value) => number_format((float) $value, 0, ',', '.');
        $rupiah = fn ($value) => 'Rp. '.number_format((float) $value, 0, ',', '.');

        $loans = fn () => pinjaman::query()->whereIn('id_perusahaan', $companyIds)->where('status_otp', 'success');
        $pinjamanTable = (new pinjaman)->getTable();
        $disbursementTable = (new disbursement)->getTable();
        $disbursed = disbursement::query()
            ->join($pinjamanTable, "{$pinjamanTable}.id", '=', "{$disbursementTable}.pinjaman_id")
            ->whereIn("{$pinjamanTable}.id_perusahaan", $companyIds)
            ->whereNull("{$pinjamanTable}.deleted_at")
            ->when($from, fn ($q) => $q->whereBetween("{$disbursementTable}.created_at", [$from, $to]));
        $repaid = repayment::query()
            ->whereHas('invoice', fn ($q) => $q->whereIn('id_list_perusahaan', $companyIds))
            ->when($from, fn ($q) => $q->whereBetween('created_at', [$from, $to]));

        $kpis = [
            ['label' => 'PENGAJUAN MASUK', 'value' => $num($loans()->when($from, fn ($q) => $q->whereBetween('created_at', [$from, $to]))->count()), 'sub' => 'tervalidasi WhatsApp', 'icon' => 'mdi-file-document-outline', 'color' => 'info'],
            ['label' => 'PENYALURAN', 'value' => $rupiah((clone $disbursed)->sum("{$disbursementTable}.dana_yang_dicairkan")), 'sub' => $num((clone $disbursed)->count()).' pinjaman dicairkan', 'icon' => 'mdi-bank-transfer-out', 'color' => 'primary'],
            ['label' => 'PINJAMAN BERJALAN', 'value' => $rupiah($loans()->where('status', 'approve')->sum('nominal')), 'sub' => $num($loans()->where('status', 'approve')->count()).' pinjaman aktif saat ini', 'icon' => 'mdi-cash-multiple', 'color' => 'success'],
            ['label' => 'PINJAMAN LUNAS', 'value' => $num($loans()->where('status', 'done')->when($from, fn ($q) => $q->whereBetween('updated_at', [$from, $to]))->count()), 'sub' => 'pinjaman selesai', 'icon' => 'mdi-check-decagram-outline', 'color' => 'success'],
            ['label' => 'PEMBAYARAN DITERIMA', 'value' => $rupiah((clone $repaid)->sum('amount_total')), 'sub' => $num((clone $repaid)->count()).' pembayaran tagihan', 'icon' => 'mdi-cash-check', 'color' => 'warning'],
        ];

        $operations = [
            ['label' => 'PENGAJUAN DIPROSES', 'value' => $num($loans()->whereIn('status', ['on_proses', 'pending'])->count()), 'sub' => 'menunggu persetujuan / pencairan', 'icon' => 'mdi-file-clock-outline', 'color' => 'info', 'url' => route('internal.tomi-dana.application-history')],
            ['label' => 'MENUNGGU PENCAIRAN', 'value' => $num($loans()->where('status', 'pending')->where('document_privy', 'y')->count()), 'sub' => 'dokumen sudah ditandatangani', 'icon' => 'mdi-bank-transfer', 'color' => 'danger', 'url' => route('internal.tomi-dana.disbursement')],
            ['label' => 'TAGIHAN BELUM LUNAS', 'value' => $num(invoicing_pinjaman::query()->whereIn('id_list_perusahaan', $companyIds)->where('status', 'belum_lunas')->count()), 'sub' => 'invoice perusahaan', 'icon' => 'mdi-file-alert-outline', 'color' => 'warning', 'url' => route('internal.tomi-dana.repayment')],
            ['label' => 'KARYAWAN MENUNGGU APPROVAL', 'value' => $num(list_karyawan::query()->whereIn('id_list_perusahaan', $companyIds)->where(fn ($q) => $q->where('approve_satu', 'pending')->orWhere('approve_dua', 'pending'))->count()), 'sub' => 'approval layer 1 / 2', 'icon' => 'mdi-account-clock-outline', 'color' => 'secondary', 'url' => route('internal.tomi-dana.companies')],
        ];

        return view('internal.tomi-dana.dashboard.index', [
            'kpis' => $kpis,
            'operations' => $operations,
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

    /** PKS TomiDana: PKS terakhir per perusahaan (berakhir paling lambat; tanpa tanggal berakhir = aktif). */
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
