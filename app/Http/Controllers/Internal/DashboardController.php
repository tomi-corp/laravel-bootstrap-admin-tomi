<?php

namespace App\Http\Controllers\Internal;

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
use Illuminate\Database\Eloquent\Builder;

/**
 * Dashboard internal: ringkasan data asli dari tiap fitur, dibatasi perusahaan yang boleh diakses admin.
 * Data pengajuan/pencairan/tagihan saat ini hanya tersedia untuk TomiDana (tabel pinjaman).
 */
class DashboardController extends Controller
{
    use ScopesCompanies;

    /**
     * Produk di menu internal: kode fitur (tabel features) => slug route & slot warna.
     */
    private const PRODUCTS = [
        'tomidana' => ['name' => 'TomiDana', 'route' => 'tomi-dana', 'slot' => 1],
        'tomidanaplus' => ['name' => 'TomiDana Plus', 'route' => 'tomi-dana-plus', 'slot' => 1],
        'tomigaji' => ['name' => 'TomiGaji', 'route' => 'tomi-gaji', 'slot' => 2],
        'tomiinvoice' => ['name' => 'TomiInvoice', 'route' => 'tomi-invoice', 'slot' => 3],
        'tomigawai' => ['name' => 'TomiGawai', 'route' => 'tomi-gawai', 'slot' => 4],
    ];

    private const STATUS_BADGES = [
        'on_proses' => ['DIPROSES', 'info'],
        'pending' => ['MENUNGGU PENCAIRAN', 'warn'],
        'approve' => ['AKTIF', 'good'],
        'reject' => ['DITOLAK', 'crit'],
        'done' => ['LUNAS', 'good'],
    ];

    /** Tagihan dianggap "mendekati jatuh tempo" mulai H-5. */
    private const DUE_SOON_DAYS = 5;

    public function index()
    {
        $today = Carbon::today();
        $yesterday = $today->copy()->subDay();
        $monthStart = $today->copy()->startOfMonth();
        $lastMonthStart = $monthStart->copy()->subMonthNoOverflow();
        $lastMonthSameDay = $today->copy()->subMonthNoOverflow()->endOfDay();

        // ---------- Perlu perhatian ----------
        $alerts = [
            [
                'value' => $this->applications()->where('status', 'pending')->where('document_privy', 'y')->count(),
                'label' => 'Menunggu Pencairan',
                'icon' => 'bolt',
                'cls' => 'crit',
                'url' => route('internal.tomi-dana.disbursement'),
            ],
            [
                'value' => $this->invoices()
                    ->where('status', 'belum_lunas')
                    ->whereDate('period', '<=', $today->copy()->addDays(self::DUE_SOON_DAYS))
                    ->count(),
                'label' => 'Tagihan Jatuh Tempo (≤ H-'.self::DUE_SOON_DAYS.')',
                'icon' => 'clock',
                'cls' => 'warn',
                'url' => route('internal.tomi-dana.repayment'),
            ],
            [
                'value' => $this->scoped(list_karyawan::query(), 'id_list_perusahaan')
                    ->where(fn ($q) => $q->where('approve_satu', 'pending')->orWhere('approve_dua', 'pending'))
                    ->count(),
                'label' => 'Karyawan Menunggu Approval',
                'icon' => 'users',
                'cls' => 'info',
                'url' => route('internal.tomi-dana.companies'),
            ],
        ];
        $totalTasks = array_sum(array_column($alerts, 'value'));

        // ---------- Statistik utama ----------
        $activeCompanies = $this->visibleCompanies()->where('status', 'active')->count();
        $registeredCompanies = $this->visibleCompanies()->count();

        $applicationsToday = $this->applications()->whereDate('created_at', $today)->count();
        $applicationsYesterday = $this->applications()->whereDate('created_at', $yesterday)->count();

        $disbursedThisMonth = $this->disbursedBetween($monthStart, $today->copy()->endOfDay());
        $disbursedLastMonth = $this->disbursedBetween($lastMonthStart, $lastMonthSameDay);

        $stats = [
            [
                'label' => 'Total Perusahaan Aktif',
                'value' => $this->num($activeCompanies),
                'delta' => null,
                'sub' => 'dari '.$this->num($registeredCompanies).' perusahaan terdaftar',
                'icon' => 'bank',
                'tone' => 'var(--s1)',
                'url' => route('internal.companies'),
            ],
            [
                'label' => 'Pengajuan Hari Ini',
                'value' => $this->num($applicationsToday),
                'delta' => $this->delta($applicationsToday, $applicationsYesterday),
                'sub' => 'dibanding kemarin',
                'icon' => 'table',
                'tone' => 'var(--s3)',
                'url' => route('internal.tomi-dana.application-history'),
            ],
            [
                'label' => 'Pencairan Bulan Ini',
                'value' => $this->rupiah($disbursedThisMonth),
                'delta' => $this->delta($disbursedThisMonth, $disbursedLastMonth),
                'sub' => 'dibanding periode sama bulan lalu',
                'icon' => 'bolt',
                'tone' => 'var(--s4)',
                'url' => route('internal.tomi-dana.disbursement'),
            ],
            [
                'label' => 'Tugas Perlu Tindakan',
                'value' => $this->num($totalTasks),
                'delta' => null,
                'sub' => 'pencairan, tagihan & approval karyawan',
                'icon' => 'check',
                'tone' => 'var(--crit-fill)',
                'url' => '#perlu-perhatian',
            ],
        ];

        // ---------- Ringkasan & distribusi produk ----------
        $features = Feature::whereIn('code', array_keys(self::PRODUCTS))->pluck('id', 'code');
        $visibleCompanyIds = $this->visibleCompanies()->select('list_perusahaans.id');
        $companyCounts = CompanyPks::query()
            ->whereIn('company_id', $visibleCompanyIds)
            ->whereIn('feature_id', $features->values())
            ->selectRaw('feature_id, COUNT(DISTINCT company_id) as total')
            ->groupBy('feature_id')
            ->pluck('total', 'feature_id');
        $activePksCounts = CompanyPks::query()
            ->whereIn('company_id', $this->visibleCompanies()->select('list_perusahaans.id'))
            ->whereIn('feature_id', $features->values())
            ->where(fn ($q) => $q->whereNull('end_date')->orWhereDate('end_date', '>=', $today))
            ->selectRaw('feature_id, COUNT(DISTINCT company_id) as total')
            ->groupBy('feature_id')
            ->pluck('total', 'feature_id');

        $statusCounts = $this->applications()->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

        $products = [];
        foreach (self::PRODUCTS as $code => $product) {
            $featureId = $features[$code] ?? null;
            $hasApplications = $code === 'tomidana';

            $products[] = $product + [
                'companies' => (int) ($companyCounts[$featureId] ?? 0),
                'active_pks' => (int) ($activePksCounts[$featureId] ?? 0),
                'has_applications' => $hasApplications,
                'total' => $hasApplications ? (int) $statusCounts->sum() : null,
                'process' => $hasApplications ? (int) (($statusCounts['on_proses'] ?? 0) + ($statusCounts['pending'] ?? 0)) : null,
                'active' => $hasApplications ? (int) ($statusCounts['approve'] ?? 0) : null,
                'url' => route('internal.'.$product['route'].'.companies'),
            ];
        }
        $maxCompanies = max(1, ...array_column($products, 'companies'));

        // ---------- Operasional hari ini (TomiDana) ----------
        $operations = [
            [
                'label' => 'Pengajuan Baru',
                'value' => $applicationsToday,
                'sub' => 'pengajuan masuk',
                'icon' => 'table',
                'cls' => 'info',
                'url' => route('internal.tomi-dana.application-history'),
            ],
            [
                'label' => 'Dana Dicairkan',
                'value' => $this->disbursements()->whereDate($this->disbursementTable().'.created_at', $today)->count(),
                'sub' => 'pinjaman dicairkan',
                'icon' => 'bolt',
                'cls' => 'good',
                'url' => route('internal.tomi-dana.disbursement'),
            ],
            [
                'label' => 'Pembayaran Tagihan',
                'value' => repayment::whereHas('invoice', fn ($q) => $this->scoped($q, 'id_list_perusahaan'))
                    ->whereDate('created_at', $today)
                    ->count(),
                'sub' => 'tagihan dibayar',
                'icon' => 'check',
                'cls' => 'info',
                'url' => route('internal.tomi-dana.repayment'),
            ],
            [
                'label' => 'Perusahaan Baru',
                'value' => $this->visibleCompanies()->where('list_perusahaans.created_at', '>=', $monthStart)->count(),
                'sub' => 'terdaftar bulan ini',
                'icon' => 'users',
                'cls' => 'warn',
                'url' => route('internal.companies'),
            ],
        ];

        // ---------- Aktivitas terbaru ----------
        $activities = $this->applications()
            ->with(['data_karyawan:id,nama_karyawan', 'perusahaan:id,nama'])
            ->latest('updated_at')
            ->limit(8)
            ->get()
            ->map(function (pinjaman $row) {
                [$status, $cls] = self::STATUS_BADGES[$row->status] ?? [strtoupper((string) $row->status), 'info'];
                $name = $row->data_karyawan?->nama_karyawan ?? '-';

                return [
                    'time' => Carbon::parse($row->updated_at)->format('d-m-Y H:i'),
                    'user' => $name,
                    'hue' => crc32($name) % 360,
                    'activity' => 'Pengajuan pinjaman '.$this->rupiah($row->nominal).' · '.($row->perusahaan?->nama ?? '-'),
                    'product' => 'TomiDana',
                    'status' => $status,
                    'cls' => $cls,
                    'url' => $row->id_perusahaan
                        ? route('internal.tomi-dana.applications', $row->id_perusahaan)
                        : route('internal.tomi-dana.application-history'),
                ];
            });

        return view('internal.dashboard.index', compact(
            'stats', 'alerts', 'products', 'maxCompanies', 'operations', 'activities'
        ));
    }

    /** Pengajuan TomiDana yang sudah tervalidasi WhatsApp. */
    private function applications(): Builder
    {
        return $this->scoped(pinjaman::query(), 'id_perusahaan')->where('status_otp', 'success');
    }

    private function invoices(): Builder
    {
        return $this->scoped(invoicing_pinjaman::query(), 'id_list_perusahaan');
    }

    private function disbursements(): Builder
    {
        $pinjamanTable = (new pinjaman)->getTable();

        return disbursement::query()
            ->join($pinjamanTable, "{$pinjamanTable}.id", '=', $this->disbursementTable().'.pinjaman_id')
            ->whereIn("{$pinjamanTable}.id_perusahaan", $this->visibleCompanies()->select('list_perusahaans.id'))
            ->whereNull("{$pinjamanTable}.deleted_at");
    }

    private function disbursedBetween(Carbon $from, Carbon $to): float
    {
        $table = $this->disbursementTable();

        return (float) $this->disbursements()
            ->whereBetween("{$table}.created_at", [$from, $to])
            ->sum("{$table}.dana_yang_dicairkan");
    }

    private function disbursementTable(): string
    {
        return (new disbursement)->getTable();
    }

    private function scoped(Builder $query, string $column): Builder
    {
        return $query->whereIn($column, $this->visibleCompanies()->select('list_perusahaans.id'));
    }

    /**
     * Persentase perubahan dibanding pembanding. Null jika pembanding 0 (tidak bisa dihitung).
     */
    private function delta(float $current, float $previous): ?array
    {
        if ($previous == 0) {
            return $current == 0 ? ['text' => '0%', 'up' => null] : null;
        }

        $percent = ($current - $previous) / $previous * 100;

        return [
            'text' => $this->num(abs($percent)).'%',
            'up' => $percent == 0 ? null : $percent > 0,
        ];
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
