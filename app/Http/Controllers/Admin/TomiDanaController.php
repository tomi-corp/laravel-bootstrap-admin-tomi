<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Ksp\AddKonfigurasiRequest;
use App\Http\Requests\Admin\Ksp\AmountPinjamanRequest;
use App\Http\Requests\Admin\Ksp\AddPerusahaanRequest;
use App\Http\Requests\Admin\Ksp\EditPerusahaanRequest;
use App\Http\Resources\Admin\Ksp\DetailPerusahaanResource;
use App\Http\Resources\Admin\Ksp\TenorPinjamanResource;
use App\Models\amount_pinjaman;
use App\Models\kecamatan;
use App\Models\kelurahan;
use App\Models\kota;
use App\Models\ListPerusahaan;
use App\Models\list_karyawan;
use App\Models\lender;
use App\Models\bank;
use App\Models\pinjaman;
use App\Models\provinsi;
use App\Models\tenor_pinjaman;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class TomiDanaController extends Controller
{
    public function configurations()
    {
        $perusahaans = ListPerusahaan::with('lender')
            ->orderBy('nama')
            ->get();

        return view('admin.tomi-dana.configurations.companies', compact('perusahaans'));
    }

    public function configuration($id)
    {
        $companyId = decryptSecure($id);
        $list_perusahaan = ListPerusahaan::with('amount_pinjaman')->findOrFail($companyId);
        $max_plafon = list_karyawan::where('id_list_perusahaan', $list_perusahaan->id)->max('max_plafon') ?? 0;

        return view('admin.tomi-dana.configurations.index', compact('list_perusahaan', 'max_plafon'));
    }

    public function configurationTable(Request $request)
    {
        $tenor = tenor_pinjaman::where('id_perusahaan', $request->id)->orderBy('tenor')->get();
        $amount = amount_pinjaman::where('id_perusahaan', $request->id)->first();

        return DataTables::of($tenor)
            ->addIndexColumn()
            ->addColumn('tenor', fn ($row) => $row->tenor . ' bulan')
            ->addColumn('rate', fn ($row) => $row->rate . ' %')
            ->addColumn('asuransi', fn ($row) => $row->asuransi . ' %')
            ->addColumn('provisi', fn ($row) => $row->provisi . ' %')
            ->addColumn('biaya_admin', fn ($row) => $row->biaya_admin . ' %')
            ->addColumn('fee_tomi', fn ($row) => $row->fee_tomi . ' %')
            ->addColumn('profesional_fee_1', fn ($row) => $row->profesional_fee_1 . ' %')
            ->addColumn('profesional_fee_2', fn ($row) => $row->profesional_fee_2 . ' %')
            ->addColumn('ksp', fn ($row) => $row->ksp . ' %')
            ->addColumn('iuran_anggota', fn ($row) => 'Rp. ' . number_format($row->tenor * ($amount?->iuran_anggota ?? 0), 0, ',', '.'))
            ->addColumn('biaya_layanan', fn ($row) => 'Rp. ' . number_format($row->biaya_layanan, 0, ',', '.'))
            ->addColumn('minimal_nominal', fn ($row) => 'Rp. ' . number_format($row->minimal_nominal, 0, ',', '.'))
            ->addColumn('maximal_nominal', fn ($row) => 'Rp. ' . number_format($row->maximal_nominal, 0, ',', '.'))
            ->addColumn('action', fn ($row) => '<button class="btn btn-sm btn-primary mr-2" onclick="edit(' . $row->id . ')"><i class="mdi mdi-pencil"></i></button><button class="btn btn-sm btn-danger" onclick="deleteKonfigurasi(' . $row->id . ')"><i class="mdi mdi-trash-can"></i></button>')
            ->rawColumns(['action'])
            ->toJson();
    }

    public function addConfiguration(AddKonfigurasiRequest $request)
    {
        $tenor = new tenor_pinjaman();
        $this->fillConfiguration($tenor, $request);
        $tenor->id_perusahaan = $request->id;
        $tenor->save();

        return response()->json(['status' => 200, 'message' => 'Konfigurasi tenor berhasil ditambahkan.']);
    }

    public function detailConfiguration(Request $request)
    {
        return response()->json([
            'status' => 200,
            'data' => new TenorPinjamanResource(tenor_pinjaman::findOrFail($request->id)),
        ]);
    }

    public function updateConfiguration(AddKonfigurasiRequest $request)
    {
        $tenor = tenor_pinjaman::findOrFail($request->id);
        $this->fillConfiguration($tenor, $request);
        $tenor->save();

        return response()->json(['status' => 200, 'message' => 'Konfigurasi tenor berhasil diubah.']);
    }

    public function deleteConfiguration(Request $request)
    {
        $request->validate(['id' => 'required|exists:tenor_pinjaman,id']);
        tenor_pinjaman::findOrFail($request->id)->delete();

        return response()->json(['status' => 200, 'message' => 'Konfigurasi tenor berhasil dihapus.']);
    }

    public function updateAmount(AmountPinjamanRequest $request)
    {
        $amount = amount_pinjaman::firstOrNew(['id_perusahaan' => $request->id]);
        $amount->iuran_anggota = $request->iuran_anggota;
        $amount->minimal = $request->minimal;
        $amount->maximal = $request->maximal;
        $amount->maximal_persentase = $request->maximal_persentase;
        $amount->save();

        return response()->json(['status' => 200, 'message' => 'Amount pinjaman berhasil diperbarui.']);
    }

    private function fillConfiguration(tenor_pinjaman $tenor, AddKonfigurasiRequest $request): void
    {
        $tenor->tenor = $request->tenor;
        $tenor->rate = $request->rate;
        $tenor->asuransi = $request->asuransi;
        $tenor->provisi = $request->provisi;
        $tenor->biaya_admin = $request->biaya_admin;
        $tenor->fee_tomi = $request->fee_tomi;
        $tenor->biaya_layanan = $request->biaya_layanan;
        $tenor->minimal_nominal = $request->minimal_nominal;
        $tenor->maximal_nominal = $request->maximal_nominal;
        $tenor->profesional_fee_1 = $request->profesional_fee_1;
        $tenor->profesional_fee_2 = $request->profesional_fee_2;
        $tenor->ksp = $request->ksp;
    }

    public function lenders()
    {
        return view('admin.tomi-dana.lender.index', [
            'banks' => bank::orderBy('code_bank')->get(),
        ]);
    }

    public function lendersTable()
    {
        return DataTables::eloquent(lender::with('bank')->latest())
            ->addIndexColumn()
            ->addColumn('bank', fn ($row) => $row->bank?->code_bank ?? '-')
            ->addColumn('status_label', function ($row) {
                return $row->status === 'active'
                    ? '<span class="badge badge-success">ACTIVE</span>'
                    : '<span class="badge badge-warning">INACTIVE</span>';
            })
            ->addColumn('action', fn ($row) => '<a href="' . route('admin.tomi-dana.lender.pinjaman', ['lender_id' => $row->id]) . '" class="btn btn-sm btn-info mr-1" title="Lihat pinjaman"><i class="mdi mdi-format-list-bulleted"></i></a><button type="button" class="btn btn-sm btn-primary mr-1" onclick="editLender(' . $row->id . ')"><i class="mdi mdi-pencil"></i></button><button type="button" class="btn btn-sm btn-danger" onclick="deleteLender(' . $row->id . ')"><i class="mdi mdi-trash-can"></i></button>')
            ->rawColumns(['status_label', 'action'])
            ->toJson();
    }

    public function lenderLoans(Request $request)
    {
        $lender = $request->filled('lender_id')
            ? lender::find($request->integer('lender_id'))
            : null;
        $perusahaans = ListPerusahaan::query()
            ->select('id', 'nama')
            ->orderBy('nama')
            ->get();

        return view('admin.tomi-dana.lender.pinjaman', compact('lender', 'perusahaans'));
    }

    public function lenderLoansTable(Request $request)
    {
        return DataTables::eloquent(
            pinjaman::with(['data_karyawan', 'perusahaan', 'lender'])
                ->when($request->filled('lender_id'), fn ($query) => $query->where('id_lender', $request->integer('lender_id')))
                ->when($request->filled('company_id'), fn ($query) => $query->where('id_perusahaan', $request->integer('company_id')))
                ->when($request->filled('start_month') || $request->filled('end_month'), function ($query) use ($request) {
                    $startDate = $request->filled('start_month')
                        ? Carbon::createFromFormat('Y-m', $request->input('start_month'))->startOfMonth()
                        : null;
                    $endDate = $request->filled('end_month')
                        ? Carbon::createFromFormat('Y-m', $request->input('end_month'))->endOfMonth()
                        : null;

                    if ($startDate && $endDate) {
                        $query->whereBetween('created_at', [$startDate, $endDate]);
                    } elseif ($startDate) {
                        $query->where('created_at', '>=', $startDate);
                    } elseif ($endDate) {
                        $query->where('created_at', '<=', $endDate);
                    }
                })
                ->latest()
        )
            ->addIndexColumn()
            ->addColumn('id_pinjaman', fn ($row) => $row->id)
            ->addColumn('nama_karyawan', fn ($row) => strtoupper($row->data_karyawan?->nama_karyawan ?? '-'))
            ->addColumn('perusahaan', fn ($row) => strtoupper($row->perusahaan?->nama ?? '-'))
            ->addColumn('lender', fn ($row) => strtoupper($row->lender?->nama ?? '-'))
            ->addColumn('nominal', fn ($row) => 'Rp. ' . number_format((float) $row->nominal, 0, ',', '.'))
            ->addColumn('tenor_label', fn ($row) => ($row->tenor ?? 0) . ' bulan')
            ->addColumn('status_label', function ($row) {
                $status = strtoupper((string) ($row->status ?? '-'));

                return '<span class="badge badge-secondary">' . e($status) . '</span>';
            })
            ->addColumn('tanggal_pengajuan', fn ($row) => optional($row->created_at)->format('d/m/Y H:i') ?? '-')
            ->rawColumns(['status_label'])
            ->toJson();
    }

    public function storeLender(Request $request)
    {
        $data = $request->validate([
            'pic_lender' => 'required|string|max:255',
            'pic_internal' => 'required|string|max:255',
            'nama' => 'required|string|max:255',
            'email_lender' => 'required|email|max:255',
            'telp' => 'required|numeric',
            'alamat_lengkap' => 'required|string',
            'id_bank' => 'nullable|exists:banks,id',
            'nama_rekening' => 'nullable|string|max:255',
            'no_rekening' => 'nullable|string|max:100',
            'nomor_pks' => 'nullable|string|max:255',
            'status' => 'required|in:active,nonactive',
        ]);

        $lender = new lender($data);
        $lender->id_code_lender = $this->generateLenderCode();
        $lender->created_by = auth('admin')->id();
        $lender->save();

        return response()->json(['status' => 200, 'message' => 'Lender berhasil ditambahkan.']);
    }

    public function detailLender(Request $request)
    {
        $lender = lender::with('bank')->findOrFail($request->id);

        return response()->json(['status' => 200, 'data' => $lender]);
    }

    public function updateLender(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|exists:lenders,id',
            'pic_lender' => 'required|string|max:255',
            'pic_internal' => 'required|string|max:255',
            'nama' => 'required|string|max:255',
            'email_lender' => 'required|email|max:255',
            'telp' => 'required|numeric',
            'alamat_lengkap' => 'required|string',
            'id_bank' => 'nullable|exists:banks,id',
            'nama_rekening' => 'nullable|string|max:255',
            'no_rekening' => 'nullable|string|max:100',
            'nomor_pks' => 'nullable|string|max:255',
            'status' => 'required|in:active,nonactive',
        ]);

        $lender = lender::findOrFail($data['id']);
        unset($data['id']);
        $lender->fill($data);
        $lender->updated_by = auth('admin')->id();
        $lender->save();

        return response()->json(['status' => 200, 'message' => 'Lender berhasil diubah.']);
    }

    public function deleteLender(Request $request)
    {
        $request->validate(['id' => 'required|exists:lenders,id']);
        lender::findOrFail($request->id)->delete();

        return response()->json(['status' => 200, 'message' => 'Lender berhasil dihapus.']);
    }

    private function generateLenderCode(): string
    {
        do {
            $code = '#TomiDanaLender-' . Carbon::now()->format('dmY') . '-' . random_int(1000, 9999);
        } while (lender::where('id_code_lender', $code)->exists());

        return $code;
    }

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
                ->addColumn('konfigurasi_pinjaman', fn ($row) => '<a href="' . route('admin.tomi-dana.configurations.index', encryptSecure($row->id)) . '" target="_blank"><i class="mdi mdi-settings"></i></a>')
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
