<?php

namespace App\Http\Controllers\Admin\TomiDana;

use App\Http\Controllers\Controller;
use App\Models\lender;
use App\Models\ListPerusahaan;
use App\Models\provinsi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Yajra\DataTables\Facades\DataTables;

class CompanyController extends Controller
{
    public function index()
    {
        $provinsis = provinsi::get();
        $features = Schema::hasTable('features')
            ? DB::table('features')->whereNull('deleted_at')->orderBy('id')->get(['id', 'name'])
            : collect();

        $companies = ListPerusahaan::query()
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('company_features')
                    ->join('features', 'features.id', '=', 'company_features.feature_id')
                    ->whereColumn('company_features.company_id', 'list_perusahaans.id')
                    ->whereRaw("REPLACE(LOWER(features.name), ' ', '') = ?", ['tomidana'])
                    ->whereNull('company_features.deleted_at')
                    ->whereNull('features.deleted_at');
            })
            ->select('id', 'status')
            ->get();

        return view('admin.tomi-dana.companies.index', [
            'provinsis' => $provinsis,
            'lenders' => lender::select('id', 'nama')->orderBy('nama')->get(),
            'features' => $features,
            'perusahaanTidakAktif' => $companies->where('status', 'nonactive')->count(),
            'perusahaanAktif' => $companies->where('status', 'active')->count(),
        ]);
    }

    public function table()
    {
        try {
            $query = ListPerusahaan::query()
                ->select('list_perusahaans.*')
                ->with(['lender', 'karyawan', 'karyawanPending', 'pinjaman', 'provinsi', 'kabupaten', 'kecamatan', 'desa'])
                ->whereExists(function ($featureQuery) {
                    $featureQuery->select(DB::raw(1))
                        ->from('company_features')
                        ->join('features', 'features.id', '=', 'company_features.feature_id')
                        ->whereColumn('company_features.company_id', 'list_perusahaans.id')
                        ->whereRaw("REPLACE(LOWER(features.name), ' ', '') = ?", ['tomidana'])
                        ->whereNull('company_features.deleted_at')
                        ->whereNull('features.deleted_at');
                });

            $admin = auth()->guard('admin')->user();
            if ($admin?->type === 'pic') {
                $query->when($admin->company_id, fn ($companyQuery) => $companyQuery->where('list_perusahaans.id', $admin->company_id));
                if (empty($admin->company_id)) {
                    $query->whereRaw('1 = 0');
                }
            }

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->addColumn('lender', fn ($row) => optional($row->lender)->nama ?? '-')
                ->addColumn('list_karyawan', function ($row) {
                    return '<a href="'.route('admin.list.karyawan', encryptSecure($row->id)).'" target="_blank"><span class="badge badge-primary"><span class="badge badge-info">'.$row->karyawan->count().'</span><span class="badge badge-pink">'.$row->karyawanPending->count().'</span></span></a>';
                })
                ->addColumn('proses_pengajuan', function ($row) {
                    return '<a href="'.route('admin.ksp.pengajuan', encryptSecure($row->id)).'" target="_blank"><span class="badge badge-primary"><span class="badge badge-warning">'.$row->pinjaman->where('status', 'on_proses')->count().'</span><span class="badge badge-purple">'.$row->pinjaman->where('status', 'exported')->count().'</span><span class="badge badge-info">'.$row->pinjaman->where('status', 'approve')->count().'</span><span class="badge badge-pink">'.$row->pinjaman->where('status', 'reject')->count().'</span></span></a>';
                })
                ->addColumn('provinsi', fn ($row) => optional($row->provinsi)->provinsi ?? '-')
                ->addColumn('unique_code', fn ($row) => $row->unique_code ?? '-')
                ->addColumn('kabupaten', fn ($row) => optional($row->kabupaten)->kabupaten ?? '-')
                ->addColumn('kecamatan', fn ($row) => optional($row->kecamatan)->kecamatan ?? '-')
                ->addColumn('desa', fn ($row) => $row->desa ? ($row->desa->desa ?? '-').' - '.($row->desa->kodepos ?? '-') : '-')
                ->addColumn('alamat_lengkap', fn ($row) => $row->alamat_lengkap ?? '-')
                ->addColumn('konfigurasi_pinjaman', fn ($row) => '<a href="'.route('admin.tomi-dana.configurations.index', encryptSecure($row->id)).'" target="_blank"><i class="mdi mdi-settings"></i></a>')
                ->addColumn('status', function ($row) {
                    return match ($row->status) {
                        'active' => '<span class="badge badge-success">ACTIVE</span>',
                        'nonactive' => '<span class="badge badge-warning">INACTIVE</span>',
                        default => '<span class="badge badge-secondary">UNKNOWN</span>',
                    };
                })
                ->addColumn('action', fn ($row) => '<button type="button" class="btn btn-sm btn-primary mr-1" onclick="edit('.$row->id.')"><i class="mdi mdi-pencil"></i></button><button type="button" class="btn btn-sm btn-danger" onclick="deletePerusahaan('.$row->id.')"><i class="mdi mdi-trash-can"></i></button>')
                ->filterColumn('lender', fn ($query, $keyword) => $query->whereHas('lender', fn ($q) => $q->where('nama', 'like', "%{$keyword}%")))
                ->filterColumn('provinsi', fn ($query, $keyword) => $query->whereHas('provinsi', fn ($q) => $q->where('provinsi', 'like', "%{$keyword}%")))
                ->filterColumn('kabupaten', fn ($query, $keyword) => $query->whereHas('kabupaten', fn ($q) => $q->where('kabupaten', 'like', "%{$keyword}%")))
                ->filterColumn('kecamatan', fn ($query, $keyword) => $query->whereHas('kecamatan', fn ($q) => $q->where('kecamatan', 'like', "%{$keyword}%")))
                ->filterColumn('desa', fn ($query, $keyword) => $query->whereHas('desa', fn ($q) => $q->where('desa', 'like', "%{$keyword}%")->orWhere('kodepos', 'like', "%{$keyword}%")))
                ->rawColumns(['status', 'action', 'proses_pengajuan', 'list_karyawan', 'konfigurasi_pinjaman'])
                ->make(true);
        } catch (\Throwable $exception) {
            Log::error('ERROR TOMI DANA COMPANIES TABLE', ['message' => $exception->getMessage()]);

            return response()->json(['error' => true, 'message' => 'Gagal mengambil data perusahaan.'], 500);
        }
    }
}
