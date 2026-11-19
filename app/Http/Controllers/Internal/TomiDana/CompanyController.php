<?php

namespace App\Http\Controllers\Internal\TomiDana;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Internal\Concerns\ScopesCompanies;
use App\Models\Feature;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

/**
 * Daftar perusahaan TomiDana: hanya perusahaan yang punya PKS fitur TomiDana.
 */
class CompanyController extends Controller
{
    use ScopesCompanies;

    public const FEATURE_CODE = 'tomidana';

    public function index()
    {
        return view('internal.tomi-dana.companies.index', [
            'feature' => $this->feature(),
        ]);
    }

    public function table()
    {
        $feature = $this->feature();
        $pksOfFeature = fn ($query) => $query->where('feature_id', $feature->id);

        $query = $this->visibleCompanies()
            ->select('list_perusahaans.*')
            ->whereHas('pks', $pksOfFeature)
            ->with(['lender:id,nama', 'pks' => fn ($query) => $pksOfFeature($query)->latest('end_date')])
            ->withCount([
                'karyawan',
                'karyawanPending',
                'pinjaman as pengajuan_on_proses_count' => fn ($query) => $query->where('status', 'on_proses'),
                'pinjaman as pengajuan_exported_count' => fn ($query) => $query->where('status', 'exported'),
            ]);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('lender', fn ($row) => Str::upper($row->lender?->nama ?? '-'))
            ->addColumn('pks_number', fn ($row) => Str::upper($row->pks->first()?->agreement_number ?? '-'))
            ->addColumn('pks_period', function ($row) {
                $pks = $row->pks->first();

                return $pks ? $pks->start_date?->format('d-m-Y').' s/d '.$pks->end_date?->format('d-m-Y') : '-';
            })
            ->addColumn('pks_status', function ($row) {
                $pks = $row->pks->first();

                return $pks && $pks->end_date?->lt(today())
                    ? '<span class="badge badge-secondary">BERAKHIR</span>'
                    : '<span class="badge badge-success">AKTIF</span>';
            })
            ->editColumn('status', fn ($row) => $row->status === 'active'
                ? '<span class="badge badge-success">AKTIF</span>'
                : '<span class="badge badge-warning">TIDAK AKTIF</span>')
            ->editColumn('nama', fn ($row) => Str::upper($row->nama))
            ->editColumn('pic_perusahaan', fn ($row) => Str::upper($row->pic_perusahaan))
            ->editColumn('pic_internal', fn ($row) => Str::upper($row->pic_internal))
            ->editColumn('email_perusahaan', fn ($row) => Str::lower((string) $row->email_perusahaan))
            ->filterColumn('lender', fn ($query, $keyword) => $query->whereHas('lender', fn ($q) => $q->where('nama', 'like', "%{$keyword}%")))
            ->filterColumn('pks_number', fn ($query, $keyword) => $query->whereHas('pks', fn ($q) => $pksOfFeature($q)->where('agreement_number', 'like', "%{$keyword}%")))
            ->addColumn('configuration', fn ($row) => $this->modalButton($row, 'internal.tomi-dana.configuration', 'Konfigurasi', 'btn-outline-secondary',
                '<i class="mdi mdi-settings"></i>'))
            ->addColumn('applications', fn ($row) => $this->modalButton($row, 'internal.tomi-dana.applications', 'Proses Pengajuan', 'btn-outline-primary',
                $this->countBadge($row->pengajuan_on_proses_count, 'warning', 'Pengajuan on proses')
                .$this->countBadge($row->pengajuan_exported_count, 'purple', 'Pengajuan exported')))
            ->addColumn('employees', fn ($row) => $this->modalButton($row, 'internal.tomi-dana.employees', 'Daftar Karyawan', 'btn-outline-info',
                $this->countBadge($row->karyawan_count, 'info', 'Karyawan disetujui 2 layer')
                .$this->countBadge($row->karyawan_pending_count, 'pink', 'Karyawan menunggu persetujuan')))
            ->rawColumns(['status', 'pks_status', 'configuration', 'applications', 'employees'])
            ->make(true);
    }

    /**
     * Tombol yang membuka halaman (konfigurasi/pengajuan/karyawan) di modal lewat iframe mode embed.
     */
    private function modalButton($row, string $route, string $title, string $class, string $content): string
    {
        return '<button type="button" class="btn btn-sm '.$class.' js-company-modal" title="'.e($title).'"'
            .' data-url="'.e(route($route, ['company' => $row->id, 'embed' => 1])).'"'
            .' data-title="'.e($title.' - '.Str::upper($row->nama)).'">'.$content.'</button>';
    }

    private function countBadge(int $count, string $class, string $title): string
    {
        return '<span class="badge badge-'.$class.' mx-1" title="'.$title.'">'.number_format($count, 0, ',', '.').'</span>';
    }

    private function feature(): Feature
    {
        return Feature::query()->where('code', self::FEATURE_CODE)->firstOrFail();
    }
}
