<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PinjamanPendingExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Ksp\AddKonfigurasiRequest;
use App\Http\Requests\Admin\Ksp\AddPerusahaanRequest;
use App\Http\Requests\Admin\Ksp\AmountPinjamanRequest;
use App\Http\Requests\Admin\Ksp\EditPerusahaanRequest;
use App\Http\Requests\Admin\Ksp\UpdateInvoicingLenderRequest;
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
use App\Models\history_invoicing_lender;
use App\Models\invoicing_pinjaman;
use App\Models\jatuh_tempo;
use App\Models\kecamatan;
use App\Models\kelurahan;
use App\Models\kota;
use App\Models\lender;
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
        $perusahaan = ListPerusahaan::select('id', 'status')->get();

        $perusahaanTidakAktif = $perusahaan->where('status', 'nonactive')->count();
        $perusahaanAktif = $perusahaan->where('status', 'active')->count();


        return view('admin.ksp.perusahaan.index', compact('provinsis', 'perusahaanTidakAktif', 'perusahaanAktif'));
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

        while (true) {
            $unique_codeString = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 4);
            $unique_code = ListPerusahaan::where('unique_code', $unique_codeString)->first();
            if (!isset($unique_code)) {
                break;
            }
        }

        $perusahaan->unique_code = $unique_codeString;
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
            'lender',
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

            ->addColumn('lender', function ($row) {
                return $row->lender->nama;
            })
            ->addColumn('list_karyawan', function ($row) {
                return '<a href="' . route('admin.list.karyawan', encryptSecure($row->id)) . '" target="_blank">
                <span class="badge badge-primary">
                            <span class="badge badge-info" title="Total karyawan yang telah di approve 2 layer">' . $row->karyawan->count() . '</span> 
                            <span class="badge badge-pink" title="Total karyawan yang menunggu di approve">' . $row->karyawanPending->count() . '</span>
                </span>
                 </a>';
            })

            ->addColumn('proses_pengajuan', function ($row) {
                return '<a href="' . route('admin.ksp.pengajuan',  encryptSecure($row->id)) . '" target="_blank">
                <span class="badge badge-primary">

                    <span class="badge badge-warning" title="Proses pengajuan dengan status on proses">' . $row->pinjaman->where('status', 'on_proses')->count() . '</span>
                    <span class="badge badge-purple" title="Proses pengajuan dengan status exported">' . $row->pinjaman->where('status', 'exported')->count() . '</span>
                    <span class="badge badge-info" title="Proses pengajuan dengan status approve">' . $row->pinjaman->where('status', 'approve')->count() . '</span>
                    <span class="badge badge-pink" title="Proses pengajuan dengan status reject">' . $row->pinjaman->where('status', 'reject')->count() . '</span>
                </span>
                
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

            ->addColumn('asuransi', function ($row) {
                return $row->asuransi . ' %';
            })

            ->addColumn('provisi', function ($row) {
                return $row->provisi . ' %';
            })

            ->addColumn('biaya_admin', function ($row) {
                return $row->biaya_admin . ' %';
            })

            ->addColumn('fee_tomi', function ($row) {
                return $row->fee_tomi . ' %';
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
        $tenor->asuransi = $req->asuransi;
        $tenor->provisi = $req->asuransi;
        $tenor->biaya_admin = $req->biaya_admin;
        $tenor->fee_tomi = $req->fee_tomi;
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
        $tenor->asuransi = $req->asuransi;
        $tenor->provisi = $req->provisi;
        $tenor->biaya_admin = $req->biaya_admin;
        $tenor->fee_tomi = $req->fee_tomi;
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
        $startDate = null;
        $endDate = null;

        if (!empty($req->range)) {
            $dates = explode(' - ', $req->range);
            if (count($dates) === 2) {
                try {
                    $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                    $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();
                } catch (\Exception $e) {
                    $startDate = null;
                    $endDate = null;
                }
            }
        }

        $query = pinjaman::where('id_perusahaan', $req->id)
            ->where('status', 'on_proses')
            ->where('status_otp', 'success')
            ->with('data_karyawan');

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        $pinjaman = $query->get();

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
        $startDate = null;
        $endDate = null;

        if (!empty($req->range)) {
            $dates = explode(' - ', $req->range);
            if (count($dates) === 2) {
                try {
                    $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                    $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();
                } catch (\Exception $e) {
                    $startDate = null;
                    $endDate = null;
                }
            }
        }

        $query = pinjaman::where('id_perusahaan', $req->id)
            ->where('status', 'pending')
            ->where('status_otp', 'success')
            ->with('data_karyawan');

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        $pinjaman = $query->get();

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

        $startDate = null;
        $endDate = null;

        if (!empty($req->range)) {
            $dates = explode(' - ', $req->range);
            if (count($dates) === 2) {
                try {
                    $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                    $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();
                } catch (\Exception $e) {
                    $startDate = null;
                    $endDate = null;
                }
            }
        }

        $query = pinjaman::where('id_perusahaan', $req->id)
            ->where('status', 'approve')
            ->where('status_otp', 'success')
            ->with('data_karyawan');

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        $pinjaman = $query->get();

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
        $startDate = null;
        $endDate = null;

        if (!empty($req->range)) {
            $dates = explode(' - ', $req->range);
            if (count($dates) === 2) {
                try {
                    $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                    $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();
                } catch (\Exception $e) {
                    $startDate = null;
                    $endDate = null;
                }
            }
        }

        $query =  pinjaman::where('id_perusahaan', $req->id)
            ->where('status', 'reject')
            ->where('status_otp', 'success')
            ->with('data_karyawan');

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        $pinjaman = $query->get();


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

        $startDate = null;
        $endDate = null;

        if (!empty($req->range)) {
            $dates = explode(' - ', $req->range);
            if (count($dates) === 2) {
                try {
                    $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                    $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();
                } catch (\Exception $e) {
                    $startDate = null;
                    $endDate = null;
                }
            }
        }

        $query =  pinjaman::where('id_perusahaan', $req->id)
            ->where('status', 'done')
            ->where('status_otp', 'success')
            ->with('data_karyawan');

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        $pinjaman = $query->get();

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
        $amount->id_perusahaan = $req->id;
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


    public function invoicingTable(Request $req)
    {

        $invoice = invoicing_pinjaman::where('id_list_perusahaan', $req->id)
            ->with(['pinjaman' => function ($q) {
                $q->with('angsuran');
            }, 'perusahaan'])
            ->get();


        return DataTables::of($invoice)
            ->addIndexColumn()

            ->addColumn('period', function ($row) {
                return $row->tahun . ' - ' . $row->bulan;
            })

            ->addColumn('total_tagihan', function ($row) {
                return "Rp " . number_format($row->biaya_admin_pandu + $row->biaya_layanan);
            })

            ->addColumn('status_invoicing', function ($row) {
                if ($row->period == null) {
                    return "Belum Lunas";
                } else {
                    return "Lunas";
                }
            })

            ->addColumn('download_invoice', function ($row) use ($req) {
                $url = route('admin.ksp.invoicing.download', [encryptSecure($req->id), encryptSecure($row->tahun), encryptSecure($row->bulan)]);
                $periode = Carbon::createFromDate($row->tahun, $row->bulan, 1);
                $tanggalSekarang = Carbon::now();
                if (isset($row->period)) {
                    return '<p href="#" onclick="return false;" style="text-color: #fff"><i class="mdi mdi-download"></i></p>';
                } else {
                    if ($periode->format('Ym') > $tanggalSekarang->format('Ym')) {
                        return '<p href="#" onclick="return false;" style="text-color: #fff"><i class="mdi mdi-download"></i></p>';
                    } else {
                        return '<a href="' . $url . '" target="_blank"><i class="mdi mdi-download"></i></a>';
                    }
                }
            })

            ->addColumn('action', function ($row) {
                $periode = Carbon::createFromDate($row->tahun, $row->bulan, 1);
                $tanggalSekarang = Carbon::now();

                if (isset($row->period)) {
                    return '<p href="#" onclick="return false;" style="text-color: #fff">-</p>';
                } else {
                    // if ($periode->format('Ym') < $tanggalSekarang->format('Ym')) {
                    if ($periode->format('Ym') > $tanggalSekarang->format('Ym')) {
                        return '<p href="#" onclick="return false;" style="text-color: #fff">-</p>';
                    } else {
                        return '<button class="btn btn-sm btn-primary mr-2" onclick="editInvoicing(' . $row->tahun . ',' . $row->bulan . ')"><i class="mdi mdi-pencil"></i> </button>';
                    }
                }
            })

            ->rawColumns(['download_invoice', 'action'])
            ->only(['id', 'period', 'total_tagihan', 'status_invoicing', 'download_invoice', 'action'])
            ->toJson();
    }

    public function invoicingUpdate(UpdateInvoicingLenderRequest $req)
    {
        $history = new history_invoicing_lender();
        $history->periode = Carbon::createFromDate($req->tahun, $req->bulan, 1);
        $history->id_list_perusahaan = $req->id_list_perusahaan;
        $history->save();
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

    public function kspInvoicingIndex()
    {
        return view('admin.ksp.perusahaan.invoicing.index');
    }

    public function listPerusahaanInvoicingTable()
    {
        $today = Carbon::today();

        $invoice = invoicing_pinjaman::with([
            'perusahaan:id,id_code_perusahaan,nama',
            'pinjaman' => function ($q) {
                $q->whereIn('status', ['approve', 'done'])
                    ->select(['id', 'id_perusahaan', 'total_angsuran_perbulan']); // ambil langsung dari tabel pinjaman
                // ->with(['angsuran:id,id_pinjaman,jatuh_tempo']);
            }
        ])
            // ->select(['id', 'period', 'status', 'id_list_perusahaan'])
            ->get();


        return DataTables::of($invoice)
            ->addIndexColumn()
            ->addColumn(
                'id_code_perusahaan',
                function ($row) {
                    return "<b>{$row->perusahaan->id_code_perusahaan}</b>";
                }
            )

            ->addColumn(
                'nama_perusahaan',
                function ($row) {
                    return "<b>{$row->perusahaan->nama}</b>";
                }
            )

            ->addColumn('prioritas', function ($row) use ($today) {
                if ($row->status === "lunas") {
                    return '<span class="badge badge-success">Lunas</span>';
                }

                $parse = Carbon::parse($row->period)->startOfDay();
                $diffInDays = $parse->diffInDays($today, false);

                return match (true) {
                    $diffInDays <= -5 => '⚪️ Belum Jatuh Tempo (H-5)',
                    $diffInDays == -4 => '⚪️ Belum Jatuh Tempo (H-4)',
                    $diffInDays == -3 => '⚪️ Belum Jatuh Tempo (H-3)',
                    $diffInDays == -2 => '⚪️ Belum Jatuh Tempo (H-2)',
                    $diffInDays == -1 => '⚪️ Belum Jatuh Tempo (H-1)',
                    $diffInDays == 0  => '🔵 Baru Masuk (H0)',
                    $diffInDays == 1  => '🟡 Normal (H+1)',
                    $diffInDays == 2  => '🟠 Mendesak (H+2)',
                    $diffInDays >= 3  => '🔴 Sangat Mendesak (H+' . $diffInDays . ')',
                    default            => '⛔ Status Tidak Dikenal'
                };
            })

            ->addColumn('tanggal_period', fn($row) => $row->period)

            ->addColumn('total_tagihan', function ($row) {
                $targetMonth = Carbon::parse($row->period)->format('m-Y');
                $totalPeminjam = 0;

                foreach ($row->pinjaman as $pinjaman) {
                    $totalPeminjam += $pinjaman->angsuran
                        ->filter(fn($a) => Carbon::parse($a->jatuh_tempo)->format('m-Y') === $targetMonth)
                        ->count();
                }

                return "{$totalPeminjam} Pinjaman";
            })

            ->addColumn('total_pinjaman', function ($row) {
                $targetMonth = Carbon::parse($row->period)->format('m-Y');
                $totalTagihan = 0;

                foreach ($row->pinjaman as $pinjaman) {
                    $hasAngsuranBulanIni = $pinjaman->angsuran
                        ->contains(fn($a) => Carbon::parse($a->jatuh_tempo)->format('m-Y') === $targetMonth);

                    if ($hasAngsuranBulanIni) {
                        $totalTagihan += $pinjaman->total_angsuran_perbulan; // ambil dari tabel pinjaman
                    }
                }

                return "Rp " . number_format($totalTagihan);
            })

            ->addColumn(
                'status_invoicing',
                fn($row) =>
                $row->status === "belum_lunas" ? "Belum Lunas" : "Lunas"
            )

            ->rawColumns(['nama_perusahaan', 'id_code_perusahaan', 'prioritas', 'total_tagihan'])
            ->toJson();
    }



    public function invoicingDownload($id)
    {
        $dec = decryptSecure($id);
        $invoice = invoicing_pinjaman::where('id', $dec)->first();
        $periodMonth = Carbon::parse($invoice->period)->format('m');
        $periodMonthT = Carbon::parse($invoice->period)->format('M');
        $jatuhTempo  = jatuh_tempo::whereMonth('jatuh_tempo', $periodMonth)->get();
        $perusahaan = ListPerusahaan::where('id', $invoice->id_list_perusahaan)->first();

        $invoice = invoicing_pinjaman::where('id', $dec)
            ->with(['pinjaman' => function ($query) use ($periodMonth) {
                $query->whereHas('jatuh_tempoSesuaiInvoice', function ($q) use ($periodMonth) {
                    $q->whereMonth('jatuh_tempo', $periodMonth);
                });
            }, 'pinjaman.angsuran'])
            ->first();
        $pinjaman = $invoice->pinjaman;
        $noInv = "INV-" . $periodMonthT . '-' . $perusahaan->id_code_perusahaan . "-" . $invoice->id;
        // ada perbaikan invoicing nantiiii
        // $rekeningRepayment = rekening_tomi::with('bank')->first();
        $lender = lender::where('id', auth('admin')->user()->id_lender)->with('bank')->first();

        return view("admin.ksp.invoicing.perusahaan.index", compact('invoice', 'lender', 'pinjaman', 'noInv'));
    }
}
