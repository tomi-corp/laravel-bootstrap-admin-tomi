<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PinjamanPendingExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Ksp\AddKonfigurasiRequest;
use App\Http\Requests\Admin\Ksp\AddPerusahaanRequest;
use App\Http\Requests\Admin\Ksp\AmountPinjamanRequest;
use App\Http\Requests\Admin\Ksp\EditPerusahaanRequest;
use App\Http\Requests\Admin\Ksp\UpdateKaryawanRequest;
use App\Http\Requests\Admin\Ksp\UploadListKaryawanRequest;
use App\Http\Resources\Admin\Bank\BankResource;
use App\Http\Resources\Admin\Ksp\CheckAngsuranResource;
use App\Http\Resources\Admin\Ksp\DetailPerusahaanResource;
use App\Http\Resources\Admin\Ksp\InvoiceTableResource;
use App\Http\Resources\Admin\Ksp\ListKaryawanPendingResource;
use App\Http\Resources\Admin\Ksp\TenorPinjamanResource;
use Illuminate\Http\Request;
use App\Http\Resources\Admin\Pengajuan\DetailPengajuanResource;
use App\Imports\ExportImportData;
use App\Imports\KaryawanPendingImport;
use App\Models\amount_pinjaman;
use App\Models\bank;
use App\Models\cost;
use App\Models\invoicing_pinjaman;
use App\Models\kecamatan;
use App\Models\kelurahan;
use App\Models\kota;
use App\Models\list_karyawan;
use App\Models\ListPerusahaan;
use App\Models\notification;
use App\Models\pinjaman;
use App\Models\provinsi;
use App\Models\tenor_pinjaman;
use App\Models\xenditApi;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use Psy\Readline\Hoa\Console;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Http;



class KspController extends Controller
{
    public function index()
    {
        $provinsis = provinsi::get();
        return view('admin.ksp.perusahaan.index', compact('provinsis'));
    }

    public function kabupaten(Request $req)
    {
        $kabupaten = kota::where('provinsi', $req->provinsi)->get();
        return $kabupaten;
    }

    public function kecamatan(Request $req)
    {
        $kecamatan = kecamatan::where('kabupaten', $req->kabupaten)->get();
        return $kecamatan;
    }


    public function desa(Request $req)
    {
        $kelurahan = kelurahan::where('kecamatan', $req->kecamatan)->get();
        return $kelurahan;
    }

    public function addPerusahaan(AddPerusahaanRequest $req)
    {
        $perusahaan = new ListPerusahaan();

        while (true) {
            $string = '#TomiKsp' . '-' . Carbon::now()->format('dmY') . '-' . rand(1000, 9999);
            $listPerushaan = ListPerusahaan::where('id_code_perusahaan', $string)->first();
            if (!isset($listPerushaan)) {
                break;
            }
        }

        $perusahaan->unique_code = $req->unique_code;
        $perusahaan->id_code_perusahaan = $string;
        $perusahaan->pic_perusahaan = $req->pic_perusahaan;
        $perusahaan->pic_internal = $req->pic_internal;
        $perusahaan->nama = $req->nama_perusahaan;
        $perusahaan->email_perusahaan = $req->email_perusahaan;
        $perusahaan->alamat_lengkap = $req->alamat_lengkap;
        $perusahaan->telp = $req->telepon;
        $perusahaan->payment_date = $req->payment_date;
        $perusahaan->nomor_pks = $req->nomor_pks;

        $provinsi = provinsi::where('provinsi', $req->provinsi)->first();
        $kabupaten = kota::where('kabupaten', $req->kabupaten)->first();
        $kecamatan = kecamatan::where('kecamatan', $req->kecamatan)->first();
        $desaaa = explode("-", $req->desa);
        $desa = kelurahan::where('desa', $desaaa[0])
            ->where('kodepos', $desaaa[1])
            ->first();

        $perusahaan->id_provinsi = $provinsi->id;
        $perusahaan->id_kabupaten = $kabupaten->id;
        $perusahaan->id_kecamatan = $kecamatan->id;
        $perusahaan->id_desa = $desa->id;
        $perusahaan->save();
        return response()->json([
            'status' => 200,
            'message' => 'Perusahaan Berhasil Ditambahkan',
        ], 200);
    }

    public function detailPerusahaan(Request $req)
    {
        $perusahaan =  ListPerusahaan::where('id', $req->id)->with([
            'provinsi',
            'kabupaten',
            'kecamatan',
            'desa',
        ])->first();
        return response()->json([
            'status' => 200,
            'data' => new DetailPerusahaanResource($perusahaan),
        ], 200);
    }

    public function editPerusahaan(EditPerusahaanRequest $req)
    {
        $perusahaan =  ListPerusahaan::where('id', $req->id)->first();
        $perusahaan->nama = $req->nama_perusahaan;
        $perusahaan->alamat_lengkap = $req->alamat_lengkap;
        $perusahaan->telp = $req->telepon;
        $perusahaan->nomor_pks = $req->nomor_pks;

        $provinsi = provinsi::where('provinsi', $req->provinsi)->first();
        $kabupaten = kota::where('kabupaten', $req->kabupaten)->first();
        $kecamatan = kecamatan::where('kecamatan', $req->kecamatan)->first();
        $desaaa = explode("-", $req->desa);
        $desa = kelurahan::where('desa', $desaaa[0])
            ->where('kodepos', $desaaa[1])
            ->first();

        $perusahaan->id_provinsi = $provinsi->id;
        $perusahaan->id_kabupaten = $kabupaten->id;
        $perusahaan->id_kecamatan = $kecamatan->id;
        $perusahaan->id_desa = $desa->id;
        $perusahaan->status = $req->status;
        $perusahaan->id_desa = $desa->id;
        $perusahaan->bank = $req->status;

        $perusahaan->save();
        return response()->json([
            'status' => 200,
            'message' => 'Perusahaan Berhasil Diubah',
        ], 200);
    }

    public function listPerusahaanTable()
    {
        $perusahaan = ListPerusahaan::with([
            'karyawan',
            'karyawanPending',
            'pinjaman',
            'provinsi',
            'kabupaten',
            'kecamatan',
            'desa',
        ])->get();

        return DataTables::of($perusahaan)
            ->addIndexColumn()

            ->addColumn('list_karyawan', function ($row) {
                return '<a href="' . route('admin.list.karyawan', encryptSecure($row->id)) . '" target="_blank">
                <span class="badge badge-info" title="Total karyawan yang telah di approve 2 layer">' . $row->karyawan->count() . '</span> 
                <span class="badge badge-pink" title="Total karyawan yang menunggu di approve">' . $row->karyawanPending->count() . '</span></a>';
            })

            ->addColumn('proses_pengajuan', function ($row) {
                return '<a href="' . route('admin.ksp.pengajuan',  encryptSecure($row->id)) . '" target="_blank">
                    <span class="badge badge-warning" title="Proses pengajuan dengan status on proses">' . $row->pinjaman->where('status', 'on_proses')->count() . '</span>
                    <span class="badge badge-purple" title="Proses pengajuan dengan status exported">' . $row->pinjaman->where('status', 'exported')->count() . '</span>
                    <span class="badge badge-info" title="Proses pengajuan dengan status approve">' . $row->pinjaman->where('status', 'approve')->count() . '</span>
                    <span class="badge badge-pink" title="Proses pengajuan dengan status reject">' . $row->pinjaman->where('status', 'reject')->count() . '</span>
                </a>';
            })

            ->addColumn('provinsi', function ($row) {
                return $row->provinsi->provinsi;
            })

            ->addColumn('unique_code', function ($row) {
                return $row->unique_code;
            })

            ->addColumn('kabupaten', function ($row) {
                return $row->kabupaten->kabupaten;
            })

            ->addColumn('kecamatan', function ($row) {
                return $row->kecamatan->kecamatan;
            })

            ->addColumn('desa', function ($row) {
                return $row->desa->desa . ' - ' . $row->desa->kodepos;
            })

            ->addColumn('alamat_lengkap', function ($row) {
                return $row->alamat_lengkap;
            })


            ->addColumn('konfigurasi_pinjaman', function ($row) {
                return '<a href="' . route('admin.ksp.konfigurasi',  encryptSecure($row->id)) . '" target="_blank"><i class=" mdi mdi-settings"></i></a>';
            })

            ->addColumn('status', function ($row) {
                if ($row->status == "active") {
                    $status = '<span class="badge badge-success">ACTIVE</span>';
                } elseif ($row->status == "nonactive") {
                    $status = '<span class="badge badge-warning">INACTIVE</span>';
                }
                return $status;
            })
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-sm btn-primary mr-2" onclick="edit(' . $row->id . ')"><i class="mdi mdi-pencil"></i> </button>
                <button class="btn btn-sm btn-danger mr-2" onclick="deletePerusahaan(' . $row->id . ')"><i class=" mdi mdi-trash-can"></i> </button>
                ';
            })

            ->rawColumns(['action', 'status', 'proses_pengajuan', 'list_karyawan', 'konfigurasi_pinjaman'])
            ->toJson();
    }

    public function deletePerusahaan(Request $req)
    {
        pinjaman::where('id_perusahaan', $req->id)->delete();
        list_karyawan::where('id_list_perusahaan', $req->id)->delete();
        ListPerusahaan::where('id', $req->id)->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil menghapus Perusahaan, data karyawan, dan pinjaman',
        ], 200);
    }

    public function listKaryawanTable($id)
    {
        $perusahaan = list_karyawan::where('id_list_perusahaan', $id)
            ->where('approve_satu', 'approve')
            ->where('approve_dua', 'approve')
            ->with('bank')
            ->get();

        return DataTables::of($perusahaan)
            ->addIndexColumn()

            ->addColumn('max_plafon', function ($row) {
                return 'Rp  ' . number_format($row->max_plafon);
            })

            ->addColumn('bank', function ($row) {
                return $row->bank->code_bank;
            })

            ->addColumn('action', function ($row) {
                return '<button class="btn btn-sm btn-primary mr-2" onclick="edit(' . $row->id . ')"><i class="mdi mdi-pencil"></i></button>';
            })

            ->rawColumns(['action'])
            ->toJson();
    }
    public function detailKaryawan(Request $req)
    {
        $karyawan = list_karyawan::where('id', $req->id)
            ->where('approve_satu', 'approve')
            ->where('approve_dua', 'approve')
            ->with('bank')
            ->first();

        return response()->json([
            'status' => 200,
            'data' => [
                'nama' => $karyawan->nama_karyawan,
                'tanggal_bergabung' => $karyawan->tanggal_bergabung,
                'nik' => $karyawan->nik,
                'nip' => $karyawan->nip,
                'whatsapp' => $karyawan->whatsapp,
                'alamat' => $karyawan->alamat,
                'divisi' => $karyawan->divisi,
                'departement' => $karyawan->departement,
                'jabatan' => $karyawan->jabatan,
                'status' => $karyawan->status,
                'status_sp' => $karyawan->status_sp,
                'bank' => $karyawan->id_nama_bank,
                'nomor_bank' => $karyawan->nomor_bank,
                'penghasilan' => $karyawan->penghasilan,
            ]
        ], 200);
    }

    public function updateKaryawan(UpdateKaryawanRequest $req)
    {
        $karyawan = list_karyawan::where('id', $req->id)
            ->where('approve_satu', 'approve')
            ->where('approve_dua', 'approve')
            ->with('bank')
            ->first();

        $karyawan->nama_karyawan = $req->nama_karyawan;
        $karyawan->tanggal_bergabung = $req->tanggal_bergabung;
        $karyawan->nik = $req->nik;
        $karyawan->nip = $req->nip;
        $karyawan->whatsapp = $req->whatsapp;
        $karyawan->alamat = $req->alamat_lengkap;
        $karyawan->divisi = $req->divisi;
        $karyawan->departement = $req->departement;
        $karyawan->jabatan = $req->jabatan;
        $karyawan->status = $req->status;
        $karyawan->status_sp = $req->statusSp;
        $karyawan->id_nama_bank = $req->bank;
        $karyawan->nomor_bank = $req->nomor_bank;
        $karyawan->penghasilan = $req->penghasilan;
        $karyawan->save();
        return response()->json([
            'status' => 200,
            'message' => 'Data Karyawan Berhasil diubah'
        ], 200);
    }

    public function listKaryawan($id)
    {
        $dec = decryptSecure($id);
        $perusahaan = ListPerusahaan::where('id', $dec)
            ->with([
                'provinsi',
                'kabupaten',
                'kecamatan',
                'desa'
            ])
            ->first();

        $banks = BankResource::collection(bank::get());


        if (isset($perusahaan)) {
            return view('admin.ksp.perusahaan.karyawan', compact(['id', 'perusahaan', 'banks']));
        } else {
            abort(404);
        }
    }

    public function listKaryawanUpload(UploadListKaryawanRequest $req)
    {
        try {
            Excel::import(new KaryawanPendingImport(), $req->file('file'));
            return response()->json([
                'status' => 200,
                'message' => 'Berhasil Upload Data Karyawan',
            ], 200);
        } catch (\Throwable $th) {
            return $th;
            return response()->json([
                'status' => 400,
                'message' => 'Duplicate Data NIK, NIP / Kode ID Perusahaan tidak benar, Harap periksa kembali',
            ], 400);
        }
    }


    public function listKaryawanPending(Request $req)
    {
        $admin = auth('admin')->user()->type;
        if ($admin == "admin") {
            $listKaryawanPending = list_karyawan::where('id_list_perusahaan', $req->id)
                ->where('approve_satu', 'pending')
                ->get();
        } elseif ($admin == "master") {
            $listKaryawanPending = list_karyawan::where('id_list_perusahaan', $req->id)
                ->where('approve_dua', 'pending')
                ->get();
        }

        return response()->json([
            'status' => 200,
            'data' => ListKaryawanPendingResource::collection($listKaryawanPending),
        ], 200);
    }


    public function pendingPinjamanTable(Request $req)
    {
        $start = $req->mulai;
        $end = $req->akhir;
        $wilayah = $req->wilayah;
        if ($start == null) {
            $start = Carbon::parse('1970-01-1');
        }
        if ($end == null) {
            $end = Carbon::now();
        }


        $pengajuan = pinjaman::where('status', 'on_proses')
            ->with('user')->get();

        return DataTables::of($pengajuan)
            ->addIndexColumn()
            ->addColumn('datetime', function ($row) {
                $datetime = Carbon::parse($row->created_at)->format('Y-m-d H:i:s');
                return $datetime;
            })
            ->addColumn('nama', function ($row) {
                return $row->user->name;
            })

            ->addColumn('nik', function ($row) {
                $nik = $row->nik;
                return $nik;
            })

            ->addColumn('plafon', function ($row) {
                $plafon = "Rp  " . number_format($row->nominal);
                return $plafon;
            })

            ->addColumn('tenor', function ($row) {
                $tenor = $row->tenor . " Bulan - " . $row->biaya_admin . " %";
                return $tenor;
            })

            ->addColumn('status', function ($row) {
                if ($row->status == "pending") {
                    $status = '<span class="badge badge-warning">Pending</span>';
                } elseif ($row->status == "approve") {
                    $status = '<span class="badge badge-success">Approve</span>';
                } elseif ($row->status == "reject") {
                    $status = '<span class="badge badge-danger">Reject</span>';
                } else {
                    $status = '<span class="badge badge-primary">Done</span>';
                }
                return $status;
            })
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-sm btn-primary mr-2" onclick="detail(' . $row->id . ')"><i class="mdi mdi-eye"></i> Detail</button>
                <button class="btn btn-sm btn-info mr-2" onclick="approve(' . $row->id . ')"><i class=" mdi mdi-file-check"></i> Verifikasi</button>
                ';
            })

            ->rawColumns(['action', 'nama', 'plafon', 'tenor', 'status'])
            ->toJson();
    }

    public function detailPengajuan(Request $req)
    {
        $pengajuan = pinjaman::where('id', $req->id)->with('user')->first();
        return response()->json([
            'status' => 200,
            'message' => 'detail pengajuan',
            'pengajuan' => new DetailPengajuanResource($pengajuan)
        ], 200);
    }

    public function verifikasiPengajuan(Request $req)
    {
        $pengajuan = pinjaman::where('id', $req->id)->first();
        $pengajuan->status = $req->status;
        $pengajuan->note = $req->note;
        $pengajuan->save();
        return response()->json([
            'status' => 200,
            'message' => 'Pinjaman berhasil divalidasi',
        ], 200);
    }

    public function listKaryawanPendingCount(Request $req)
    {

        $admin = auth('admin')->user()->type;
        if ($admin == "admin") {
            $list = list_karyawan::where('id_list_perusahaan', $req->id)
                ->where('approve_satu', 'pending')
                ->count();
        } elseif ($admin == "master") {
            $list = list_karyawan::where('id_list_perusahaan', $req->id)
                ->where('approve_dua', 'pending')
                ->count();
        }

        return response()->json([
            'status' => 200,
            'pending' => $list,
        ], 200);
    }

    public function approveKaryawanPending(Request $req)
    {
        $admin = auth('admin')->user()->type;
        if ($admin == "admin") {
            $list = list_karyawan::where('id_list_perusahaan', $req->id)->update(['approve_satu' => 'approve']);
        } elseif ($admin == "master") {
            $list = list_karyawan::where('id_list_perusahaan', $req->id)->update(['approve_dua' => 'approve']);
        }
        return response()->json([
            'status' => 200,
            'message' => 'Sukses approve list karyawan',
        ], 200);
    }

    public function konfigurasi($id)
    {
        $dec = decryptSecure($id);
        $list_perusahaan = ListPerusahaan::where('id', $dec)
            ->with('amount_pinjaman')
            ->first();
        if (isset($list_perusahaan)) {
            return view('admin.ksp.konfigurasi.index', compact('list_perusahaan'));
        } else {
            return abort(404);
        }
    }

    public function konfigurasiTable(Request $req)
    {
        $tenor = tenor_pinjaman::where('id_perusahaan', $req->id)->get();
        $amount_pinjaman = amount_pinjaman::where('id_perusahaan', $req->id)->first();

        return DataTables::of($tenor)
            ->addIndexColumn()

            ->addColumn('tenor', function ($row) {
                return $row->tenor . ' bulan';
            })
            ->addColumn('rate', function ($row) {
                return $row->rate . ' %';
            })
            ->addColumn('biaya_admin', function ($row) {
                return $row->biaya_admin . ' %';
            })

            ->addColumn('iuran_anggota', function ($row) use ($amount_pinjaman) {
                return "Rp  " . number_format($row->tenor * $amount_pinjaman->iuran_anggota);
            })

            ->addColumn('biaya_layanan', function ($row) {
                return "Rp  " . number_format($row->biaya_layanan);
            })

            ->addColumn('minimal_nominal', function ($row) {
                return "Rp  " . number_format($row->minimal_nominal);
            })

            ->addColumn('maximal_nominal', function ($row) {
                return "Rp  " . number_format($row->maximal_nominal);
            })

            ->addColumn('action', function ($row) {
                return '<button class="btn btn-sm btn-primary mr-2" onclick="edit(' . $row->id . ')"><i class="mdi mdi-pencil"></i></button>
                <button class="btn btn-sm btn-danger mr-2" onclick="deleteKonfigurasi(' . $row->id . ')"><i class=" mdi mdi-trash-can"></i></button>
                ';
            })

            ->rawColumns(['action'])
            ->toJson();
    }

    public function addKonfigurasi(AddKonfigurasiRequest $req)
    {
        $tenor = new tenor_pinjaman();
        $tenor->id_perusahaan = $req->id;
        $tenor->tenor = $req->tenor;
        $tenor->rate = $req->rate;
        $tenor->biaya_admin = $req->biaya_admin;
        $tenor->biaya_layanan = $req->biaya_layanan;
        $tenor->minimal_nominal = $req->minimal_nominal;
        $tenor->maximal_nominal = $req->maximal_nominal;
        $tenor->save();
        return response()->json([
            'status' => 200,
            'message' => 'Sukses menambah configurasi tenor',
        ], 200);
    }

    public function deleteKonfigurasi(Request $req)
    {
        tenor_pinjaman::where('id', $req->id)->first()->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Sukses delete konfigurasi',
        ], 200);
    }

    public function detailKonfigurasi(Request $req)
    {
        $tenor = tenor_pinjaman::where('id', $req->id)->first();
        return response()->json([
            'status' => 200,
            'data' => new TenorPinjamanResource($tenor)
        ], 200);
    }

    public function editKonfigurasi(AddKonfigurasiRequest $req)
    {
        $tenor =  tenor_pinjaman::where('id', $req->id)->first();
        $tenor->tenor = $req->tenor;
        $tenor->rate = $req->rate;
        $tenor->biaya_admin = $req->biaya_admin;
        $tenor->biaya_layanan = $req->biaya_layanan;
        $tenor->minimal_nominal = $req->minimal_nominal;
        $tenor->maximal_nominal = $req->maximal_nominal;
        $tenor->save();
        return response()->json([
            'status' => 200,
            'message' => 'Sukses edit configurasi tenor',
        ], 200);
    }
    public function pengajuanIndex($id)
    {
        $dec = decryptSecure($id);
        $perusahaan = ListPerusahaan::where('id', $dec)
            ->with([
                'provinsi',
                'kabupaten',
                'kecamatan',
                'desa'
            ])
            ->first();
        if (isset($perusahaan)) {
            return view('admin.ksp.pengajuan.index', compact(['id', 'perusahaan']));
        } else {
            abort(404);
        }
    }

    public function pengajuanOnprosesTable(Request $req)
    {
        $pinjaman = pinjaman::where('id_perusahaan', $req->id)
            ->where('status', 'on_proses')
            ->where('status_otp', 'success')
            ->with('data_karyawan')
            ->get();

        return DataTables::of($pinjaman)
            ->addIndexColumn()

            ->addColumn('nama', function ($row) {
                return $row->data_karyawan->nama_karyawan;
            })
            ->addColumn('nominal', function ($row) {
                return "Rp  " . number_format($row->nominal);
            })

            ->addColumn('angsuran_perbulan', function ($row) {
                return "Rp  " . number_format($row->total_angsuran_perbulan);
            })

            ->addColumn('tenor', function ($row) {
                return $row->tenor . " bulan";
            })

            ->addColumn('biaya_admin_ksp', function ($row) {
                return "Rp  " . number_format($row->biaya_admin_ksp);
            })

            ->addColumn('dpp_adm_ksp', function ($row) {
                return "Rp  " . number_format($row->dpp_adm_ksp);
            })

            ->addColumn('ppn_adm_ksp', function ($row) {
                return "Rp  " . number_format($row->ppn_adm_ksp);
            })

            ->addColumn('biaya_admin_pandu', function ($row) {
                return "Rp  " . number_format($row->biaya_admin_pandu);
            })

            ->addColumn('dpp_adm_pandu', function ($row) {
                return "Rp  " . number_format($row->dpp_adm_pandu);
            })

            ->addColumn('ppn_adm_pandu', function ($row) {
                return "Rp  " . number_format($row->ppn_adm_pandu);
            })

            ->addColumn('rate', function ($row) {
                return $row->rate . " %";
            })

            ->addColumn('iuran_anggota', function ($row) {
                return "Rp  " . number_format($row->iuran_anggota);
            })

            ->addColumn('biaya_layanan', function ($row) {
                return "Rp  " . number_format($row->biaya_layanan);
            })

            ->addColumn('total_biaya_admin', function ($row) {
                return "Rp  " . number_format($row->total_biaya_admin);
            })

            ->addColumn('jumlah_yang_diterima', function ($row) {
                return "Rp  " . number_format($row->jumlah_yang_diterima);
            })
            // ->rawColumns(['action'])
            ->toJson();
    }
    public function pengajuanPendingTable(Request $req)
    {
        $pinjaman = pinjaman::where('id_perusahaan', $req->id)
            ->where('status', 'pending')
            ->where('status_otp', 'success')
            ->with('data_karyawan')
            ->get();

        return DataTables::of($pinjaman)
            ->addIndexColumn()

            ->addColumn('nama', function ($row) {
                return $row->data_karyawan->nama_karyawan;
            })
            ->addColumn('nominal', function ($row) {
                return "Rp  " . number_format($row->nominal);
            })
            ->addColumn('angsuran_perbulan', function ($row) {
                return "Rp  " . number_format($row->total_angsuran_perbulan);
            })

            ->addColumn('tenor', function ($row) {
                return $row->tenor . " bulan";
            })

            ->addColumn('biaya_admin_ksp', function ($row) {
                return "Rp  " . number_format($row->biaya_admin_ksp);
            })

            ->addColumn('dpp_adm_ksp', function ($row) {
                return "Rp  " . number_format($row->dpp_adm_ksp);
            })

            ->addColumn('ppn_adm_ksp', function ($row) {
                return "Rp  " . number_format($row->ppn_adm_ksp);
            })

            ->addColumn('biaya_admin_pandu', function ($row) {
                return "Rp  " . number_format($row->biaya_admin_pandu);
            })

            ->addColumn('dpp_adm_pandu', function ($row) {
                return "Rp  " . number_format($row->dpp_adm_pandu);
            })

            ->addColumn('ppn_adm_pandu', function ($row) {
                return "Rp  " . number_format($row->ppn_adm_pandu);
            })

            ->addColumn('rate', function ($row) {
                return $row->rate . " %";
            })

            ->addColumn('iuran_anggota', function ($row) {
                return "Rp  " . number_format($row->iuran_anggota);
            })
            ->addColumn('biaya_layanan', function ($row) {
                return "Rp  " . number_format($row->biaya_layanan);
            })

            ->addColumn('total_biaya_admin', function ($row) {
                return "Rp  " . number_format($row->total_biaya_admin);
            })

            ->addColumn('jumlah_yang_diterima', function ($row) {
                return "Rp  " . number_format($row->jumlah_yang_diterima);
            })
            // ->rawColumns(['action'])
            ->toJson();
    }

    public function pengajuanApproveTable(Request $req)
    {
        $pinjaman = pinjaman::where('id_perusahaan', $req->id)
            ->where('status', 'approve')
            ->where('status_otp', 'success')
            ->with('data_karyawan')
            ->get();

        return DataTables::of($pinjaman)
            ->addIndexColumn()

            ->addColumn('nama', function ($row) {
                return $row->data_karyawan->nama_karyawan;
            })
            ->addColumn('nominal', function ($row) {
                return "Rp  " . number_format($row->nominal);
            })
            ->addColumn('angsuran_perbulan', function ($row) {
                return "Rp  " . number_format($row->total_angsuran_perbulan);
            })

            ->addColumn('tenor', function ($row) {
                return $row->tenor . " bulan";
            })

            ->addColumn('biaya_admin_ksp', function ($row) {
                return "Rp  " . number_format($row->biaya_admin_ksp);
            })

            ->addColumn('dpp_adm_ksp', function ($row) {
                return "Rp  " . number_format($row->dpp_adm_ksp);
            })

            ->addColumn('ppn_adm_ksp', function ($row) {
                return "Rp  " . number_format($row->ppn_adm_ksp);
            })

            ->addColumn('biaya_admin_pandu', function ($row) {
                return "Rp  " . number_format($row->biaya_admin_pandu);
            })

            ->addColumn('dpp_adm_pandu', function ($row) {
                return "Rp  " . number_format($row->dpp_adm_pandu);
            })

            ->addColumn('ppn_adm_pandu', function ($row) {
                return "Rp  " . number_format($row->ppn_adm_pandu);
            })

            ->addColumn('rate', function ($row) {
                return $row->rate . " %";
            })

            ->addColumn('iuran_anggota', function ($row) {
                return "Rp  " . number_format($row->iuran_anggota);
            })

            ->addColumn('biaya_layanan', function ($row) {
                return "Rp  " . number_format($row->biaya_layanan);
            })

            ->addColumn('total_biaya_admin', function ($row) {
                return "Rp  " . number_format($row->total_biaya_admin);
            })

            ->addColumn('jumlah_yang_diterima', function ($row) {
                return "Rp  " . number_format($row->jumlah_yang_diterima);
            })

            ->addColumn('cost_tomi', function ($row) {
                return "Rp  " . number_format($row->cost_tomi);
            })

            ->addColumn('pendapatan_pandu', function ($row) {
                return "Rp  " . number_format($row->dpp_adm_pandu - $row->cost_tomi);
            })

            ->addColumn('detail_angsuran', function ($row) {
                return '<button class="btn btn-sm btn-primary mr-2" onclick="detailAngsuran(' . $row->id . ')"><i class="mdi mdi-eye"></i></button>';
            })

            ->rawColumns(['detail_angsuran'])
            ->toJson();
    }

    public function pengajuanRejectTable(Request $req)
    {
        $pinjaman = pinjaman::where('id_perusahaan', $req->id)
            ->where('status', 'reject')
            ->where('status_otp', 'success')
            ->with('data_karyawan')
            ->get();

        return DataTables::of($pinjaman)
            ->addIndexColumn()

            ->addColumn('nama', function ($row) {
                return $row->data_karyawan->nama_karyawan;
            })
            ->addColumn('nominal', function ($row) {
                return "Rp  " . number_format($row->nominal);
            })

            ->addColumn('angsuran_perbulan', function ($row) {
                return "Rp  " . number_format($row->total_angsuran_perbulan);
            })
            ->addColumn('tenor', function ($row) {
                return $row->tenor . " bulan";
            })

            ->addColumn('biaya_admin_ksp', function ($row) {
                return "Rp  " . number_format($row->biaya_admin_ksp);
            })

            ->addColumn('dpp_adm_ksp', function ($row) {
                return "Rp  " . number_format($row->dpp_adm_ksp);
            })

            ->addColumn('ppn_adm_ksp', function ($row) {
                return "Rp  " . number_format($row->ppn_adm_ksp);
            })

            ->addColumn('biaya_admin_pandu', function ($row) {
                return "Rp  " . number_format($row->biaya_admin_pandu);
            })

            ->addColumn('dpp_adm_pandu', function ($row) {
                return "Rp  " . number_format($row->dpp_adm_pandu);
            })

            ->addColumn('ppn_adm_pandu', function ($row) {
                return "Rp  " . number_format($row->ppn_adm_pandu);
            })

            ->addColumn('rate', function ($row) {
                return $row->rate . " %";
            })

            ->addColumn('iuran_anggota', function ($row) {
                return "Rp  " . number_format($row->iuran_anggota);
            })

            ->addColumn('biaya_layanan', function ($row) {
                return "Rp  " . number_format($row->biaya_layanan);
            })
            ->addColumn('total_biaya_admin', function ($row) {
                return "Rp  " . number_format($row->total_biaya_admin);
            })

            ->addColumn('jumlah_yang_diterima', function ($row) {
                return "Rp  " . number_format($row->jumlah_yang_diterima);
            })
            // ->rawColumns(['action'])
            ->toJson();
    }

    public function pengajuanDoneTable(Request $req)
    {
        $pinjaman = pinjaman::where('id_perusahaan', $req->id)
            ->where('status', 'done')
            ->where('status_otp', 'success')
            ->with('data_karyawan')
            ->get();

        return DataTables::of($pinjaman)
            ->addIndexColumn()

            ->addColumn('nama', function ($row) {
                return $row->data_karyawan->nama_karyawan;
            })
            ->addColumn('nominal', function ($row) {
                return "Rp  " . number_format($row->nominal);
            })
            ->addColumn('angsuran_perbulan', function ($row) {
                return "Rp  " . number_format($row->total_angsuran_perbulan);
            })

            ->addColumn('tenor', function ($row) {
                return $row->tenor . " bulan";
            })

            ->addColumn('biaya_admin_ksp', function ($row) {
                return "Rp  " . number_format($row->biaya_admin_ksp);
            })

            ->addColumn('dpp_adm_ksp', function ($row) {
                return "Rp  " . number_format($row->dpp_adm_ksp);
            })

            ->addColumn('ppn_adm_ksp', function ($row) {
                return "Rp  " . number_format($row->ppn_adm_ksp);
            })

            ->addColumn('biaya_admin_pandu', function ($row) {
                return "Rp  " . number_format($row->biaya_admin_pandu);
            })

            ->addColumn('dpp_adm_pandu', function ($row) {
                return "Rp  " . number_format($row->dpp_adm_pandu);
            })

            ->addColumn('ppn_adm_pandu', function ($row) {
                return "Rp  " . number_format($row->ppn_adm_pandu);
            })

            ->addColumn('rate', function ($row) {
                return $row->rate . " %";
            })

            ->addColumn('iuran_anggota', function ($row) {
                return "Rp  " . number_format($row->iuran_anggota);
            })

            ->addColumn('biaya_layanan', function ($row) {
                return "Rp  " . number_format($row->biaya_layanan);
            })

            ->addColumn('total_biaya_admin', function ($row) {
                return "Rp  " . number_format($row->total_biaya_admin);
            })

            ->addColumn('jumlah_yang_diterima', function ($row) {
                return "Rp  " . number_format($row->jumlah_yang_diterima);
            })

            ->addColumn('cost_tomi', function ($row) {
                return "Rp  " . number_format($row->cost_tomi);
            })

            ->addColumn('pendapatan_pandu', function ($row) {
                return "Rp  " . number_format($row->dpp_adm_pandu - $row->cost_tomi);
            })

            ->addColumn('detail_angsuran', function ($row) {
                return '<button class="btn btn-sm btn-primary mr-2" onclick="detailAngsuran(' . $row->id . ')"><i class="mdi mdi-eye"></i></button>';
            })

            ->rawColumns(['detail_angsuran'])
            ->toJson();
    }


    public function konfigurasiAmount(AmountPinjamanRequest $req)
    {
        $amount = amount_pinjaman::where('id_perusahaan', $req->id)
            ->FirstOrNew();
        $amount->iuran_anggota = $req->iuran_anggota;
        $amount->minimal = $req->minimal;
        $amount->maximal = $req->maximal;
        $amount->maximal_persentase = $req->maximal_persentase;
        $amount->save();
        return response()->json([
            'status' => 200,
            'message' => 'Amount pinjaman berhasil diperbarui',
        ], 200);
    }

    public function pengajuanStatistic(Request $req)
    {
        $pengajuan = pinjaman::where('id_perusahaan', $req->id)->first();

        $belum_validasi =  isset($pengajuan) ? $pengajuan->where('status_otp', 'pending')->count() : 0;
        $on_proses =  isset($pengajuan) ? $pengajuan->where('status_otp', 'success')->where('status', 'on_proses')->count() : 0;
        $pending = isset($pengajuan) ? $pengajuan->where('status_otp', 'success')->where('status', 'pending')->count() : 0;
        $approve = isset($pengajuan) ? $pengajuan->where('status_otp', 'success')->where('status', 'approve')->count() : 0;
        $done = isset($pengajuan) ? $pengajuan->where('status_otp', 'success')->where('status', 'done')->count() : 0;

        return response()->json([
            'status' => 200,
            'belum_validasi_wa' => $belum_validasi,
            'on_proses' => $on_proses,
            'pending' => $pending,
            'approve' =>  $approve,
            'done' => $done,
        ], 200);
    }

    public function invoicingDownload($id)
    {
        $invoice = invoicing_pinjaman::where('id', $id)
            ->with('perusahaan')
            ->first();
        $pinjaman = pinjaman::where('status', 'approve')
            ->whereYear('created_at', $invoice->created_at)
            ->whereMonth('created_at', $invoice->created_at)
            ->with('data_karyawan')
            ->get();
        return view("admin.ksp.invoicing.index", compact('pinjaman', 'invoice'));
    }


    public function invoicingTable(Request $req)
    {
        $invoice = invoicing_pinjaman::where('id_list_perusahaan', $req->id)
            ->with('pinjaman')
            ->get();

        return DataTables::of($invoice)
            ->addIndexColumn()

            ->addColumn('period', function ($row) {
                return Carbon::parse($row->period)->format('F Y');
            })

            ->addColumn('total_transaksi_approve', function ($row) {
                return $row->total_transaksi_approve;
            })

            ->addColumn('total_fee_base', function ($row) {
                return "Rp  " . number_format($row->total_fee_base);
            })

            ->addColumn('iuran_anggota', function ($row) {
                return "Rp  " . number_format($row->iuran_anggota);
            })

            ->addColumn('biaya_layanan', function ($row) {
                return "Rp  " . number_format($row->biaya_layanan);
            })

            ->addColumn('status_invoicing', function ($row) {
                if ($row->status == "belum_lunas") {
                    return "Belum Lunas";
                } else {
                    return "Lunas";
                }
            })

            ->addColumn('download_invoice', function ($row) {
                $url = route('admin.ksp.invoicing.download', $row->id);
                return '<a href="' . $url . '" target="_blank"><i class="mdi mdi-download"></i></a>';
            })

            ->addColumn('action', function ($row) {
                return '<button class="btn btn-sm btn-primary mr-2" onclick="edit(' . $row->id . ')"><i class="mdi mdi-pencil"></i></button>';
            })

            ->rawColumns(['action', 'download_invoice'])
            ->toJson();
    }

    public function invoicingUpdate(Request $req)
    {
        $tagihan = invoicing_pinjaman::where('id', $req->id)->first();
        $tagihan->status = "lunas";
        $tagihan->save();
        return response()->json([
            'status' => 200,
            'message' => 'Tagihan berhasil di perbarui',
        ], 200);
    }


    // 
    public function checkAngsuran(Request $req)
    {
        $pinjaman = pinjaman::where('id', $req->id)->with('angsuran')->first();
        return response()->json([
            'status' => 200,
            'message' =>  CheckAngsuranResource::collection($pinjaman->angsuran),
        ], 200);
    }
}
