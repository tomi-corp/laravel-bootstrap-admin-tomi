<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Ksp\AddPerusahaanRequest;
use App\Http\Requests\Admin\Ksp\EditPerusahaanRequest;
use App\Http\Resources\Admin\Ksp\DetailPerusahaanResource;
use App\Models\kecamatan;
use App\Models\kelurahan;
use App\Models\kota;
use App\Models\ListPerusahaan;
use App\Models\list_karyawan;
use App\Models\pinjaman;
use App\Models\provinsi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class TomiDanaController extends Controller
{
    public function companies()
    {
        $provinsis = provinsi::get();
        $perusahaan = ListPerusahaan::select('id', 'status')->get();

        return view('admin.tomi-dana.companies.index', [
            'provinsis' => $provinsis,
            'perusahaanTidakAktif' => $perusahaan->where('status', 'nonactive')->count(),
            'perusahaanAktif' => $perusahaan->where('status', 'active')->count(),
        ]);
    }

    public function kabupaten(Request $request)
    {
        return kota::where('provinsi', $request->provinsi)->get();
    }

    public function kecamatan(Request $request)
    {
        return kecamatan::where('kabupaten', $request->kabupaten)->get();
    }

    public function desa(Request $request)
    {
        return kelurahan::where('kecamatan', $request->kecamatan)->get();
    }

    public function addCompany(AddPerusahaanRequest $request)
    {
        $perusahaan = new ListPerusahaan();
        $perusahaan->id_code_perusahaan = $this->generateCompanyCode();
        $perusahaan->unique_code = $this->generateUniqueCode();
        $this->fillCompany($perusahaan, $request);
        $perusahaan->save();

        return response()->json([
            'status' => 200,
            'message' => 'Perusahaan Berhasil Ditambahkan',
        ]);
    }

    public function detailCompany(Request $request)
    {
        $perusahaan = ListPerusahaan::with(['provinsi', 'kabupaten', 'kecamatan', 'desa'])
            ->findOrFail($request->id);

        return response()->json([
            'status' => 200,
            'data' => new DetailPerusahaanResource($perusahaan),
        ]);
    }

    public function updateCompany(EditPerusahaanRequest $request)
    {
        $perusahaan = ListPerusahaan::findOrFail($request->id);
        $this->fillCompany($perusahaan, $request);
        $perusahaan->status = $request->status;
        $perusahaan->save();

        return response()->json([
            'status' => 200,
            'message' => 'Perusahaan Berhasil Diubah',
        ]);
    }

    public function deleteCompany(Request $request)
    {
        $request->validate(['id' => 'required|exists:list_perusahaans,id']);

        pinjaman::where('id_perusahaan', $request->id)->delete();
        list_karyawan::where('id_list_perusahaan', $request->id)->delete();
        ListPerusahaan::where('id', $request->id)->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil menghapus Perusahaan, data karyawan, dan pinjaman',
        ]);
    }

    public function companiesTable()
    {
        try {
            $query = ListPerusahaan::query()
                ->select('list_perusahaans.*')
                ->with(['lender', 'karyawan', 'karyawanPending', 'pinjaman', 'provinsi', 'kabupaten', 'kecamatan', 'desa']);

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
                    return '<a href="' . route('admin.list.karyawan', encryptSecure($row->id)) . '" target="_blank"><span class="badge badge-primary"><span class="badge badge-info">' . $row->karyawan->count() . '</span><span class="badge badge-pink">' . $row->karyawanPending->count() . '</span></span></a>';
                })
                ->addColumn('proses_pengajuan', function ($row) {
                    return '<a href="' . route('admin.ksp.pengajuan', encryptSecure($row->id)) . '" target="_blank"><span class="badge badge-primary"><span class="badge badge-warning">' . $row->pinjaman->where('status', 'on_proses')->count() . '</span><span class="badge badge-purple">' . $row->pinjaman->where('status', 'exported')->count() . '</span><span class="badge badge-info">' . $row->pinjaman->where('status', 'approve')->count() . '</span><span class="badge badge-pink">' . $row->pinjaman->where('status', 'reject')->count() . '</span></span></a>';
                })
                ->addColumn('provinsi', fn ($row) => optional($row->provinsi)->provinsi ?? '-')
                ->addColumn('unique_code', fn ($row) => $row->unique_code ?? '-')
                ->addColumn('kabupaten', fn ($row) => optional($row->kabupaten)->kabupaten ?? '-')
                ->addColumn('kecamatan', fn ($row) => optional($row->kecamatan)->kecamatan ?? '-')
                ->addColumn('desa', fn ($row) => $row->desa ? ($row->desa->desa ?? '-') . ' - ' . ($row->desa->kodepos ?? '-') : '-')
                ->addColumn('alamat_lengkap', fn ($row) => $row->alamat_lengkap ?? '-')
                ->addColumn('konfigurasi_pinjaman', fn ($row) => '<a href="' . route('admin.ksp.konfigurasi', encryptSecure($row->id)) . '" target="_blank"><i class="mdi mdi-settings"></i></a>')
                ->addColumn('status', function ($row) {
                    return match ($row->status) {
                        'active' => '<span class="badge badge-success">ACTIVE</span>',
                        'nonactive' => '<span class="badge badge-warning">INACTIVE</span>',
                        default => '<span class="badge badge-secondary">UNKNOWN</span>',
                    };
                })
                ->addColumn('action', fn ($row) => '<button type="button" class="btn btn-sm btn-primary mr-1" onclick="edit(' . $row->id . ')"><i class="mdi mdi-pencil"></i></button><button type="button" class="btn btn-sm btn-danger" onclick="deletePerusahaan(' . $row->id . ')"><i class="mdi mdi-trash-can"></i></button>')
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

    private function fillCompany(ListPerusahaan $perusahaan, Request $request): void
    {
        $perusahaan->pic_perusahaan = $request->pic_perusahaan;
        $perusahaan->pic_internal = $request->pic_internal;
        $perusahaan->nama = $request->nama_perusahaan;
        $perusahaan->email_perusahaan = $request->email_perusahaan ?? $perusahaan->email_perusahaan;
        $perusahaan->alamat_lengkap = $request->alamat_lengkap;
        $perusahaan->telp = $request->telepon;
        $perusahaan->payment_date = $request->payment_date;
        $perusahaan->nomor_pks = $request->nomor_pks;
        $perusahaan->id_provinsi = provinsi::where('provinsi', $request->provinsi)->value('id');
        $perusahaan->id_kabupaten = kota::where('kabupaten', $request->kabupaten)->value('id');
        $perusahaan->id_kecamatan = kecamatan::where('kecamatan', $request->kecamatan)->value('id');

        [$desa, $kodepos] = array_pad(explode('-', $request->desa, 2), 2, null);
        $perusahaan->id_desa = kelurahan::where('desa', $desa)->where('kodepos', $kodepos)->value('id');
    }

    private function generateCompanyCode(): string
    {
        do {
            $code = '#TomiDana-' . Carbon::now()->format('dmY') . '-' . random_int(1000, 9999);
        } while (ListPerusahaan::where('id_code_perusahaan', $code)->exists());

        return $code;
    }

    private function generateUniqueCode(): string
    {
        do {
            $code = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 4);
        } while (ListPerusahaan::where('unique_code', $code)->exists());

        return $code;
    }
}
