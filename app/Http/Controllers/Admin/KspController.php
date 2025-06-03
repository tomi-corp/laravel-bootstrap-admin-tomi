<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PinjamanPendingExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Ksp\AddKonfigurasiRequest;
use App\Http\Requests\Admin\Ksp\AddPerusahaanRequest;
use App\Http\Requests\Admin\Ksp\AmountPinjamanRequest;
use App\Http\Requests\Admin\Ksp\EditPerusahaanRequest;
use App\Http\Requests\Admin\Ksp\UploadListKaryawanRequest;
use App\Http\Resources\Admin\Ksp\DetailPerusahaanResource;
use App\Http\Resources\Admin\Ksp\InvoiceTableResource;
use App\Http\Resources\Admin\Ksp\ListKaryawanPendingResource;
use App\Http\Resources\Admin\Ksp\TenorPinjamanResource;
use Illuminate\Http\Request;
use App\Http\Resources\Admin\Pengajuan\DetailPengajuanResource;
use App\Imports\ExportImportData;
use App\Imports\KaryawanPendingImport;
use App\Models\amount_pinjaman;
use App\Models\invoicing_pinjaman;
use App\Models\list_data_ksp;
use App\Models\list_data_ksp_pending;
use App\Models\ListPerusahaan;
use App\Models\notification;
use App\Models\pinjaman;
use App\Models\tenor_pinjaman;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use Psy\Readline\Hoa\Console;
use Spatie\Browsershot\Browsershot;


class KspController extends Controller
{
    public function index()
    {
        return view('admin.ksp.perusahaan.index');
        // return view('admin.ksp.invoicing.index');
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

        $perusahaan->id_code_perusahaan = $string;
        $perusahaan->pic_perusahaan = $req->pic_perusahaan;
        $perusahaan->pic_internal = $req->pic_internal;
        $perusahaan->nama = $req->nama_perusahaan;
        $perusahaan->alamat = $req->alamat;
        $perusahaan->telp = $req->telepon;
        $perusahaan->nomor_pks = $req->nomor_pks;
        $perusahaan->save();
        return response()->json([
            'status' => 200,
            'message' => 'Perusahaan Berhasil Ditambahkan',
        ], 200);
    }

    public function detailPerusahaan(Request $req)
    {
        $perusahaan =  ListPerusahaan::where('id', $req->id)->first();
        return response()->json([
            'status' => 200,
            'data' => new DetailPerusahaanResource($perusahaan),
        ], 200);
    }

    public function editPerusahaan(EditPerusahaanRequest $req)
    {
        $perusahaan =  ListPerusahaan::where('id', $req->id)->first();
        $perusahaan->nama = $req->nama_perusahaan;
        $perusahaan->alamat = $req->alamat;
        $perusahaan->telp = $req->telepon;
        $perusahaan->nomor_pks = $req->nomor_pks;
        $perusahaan->status = $req->status;
        $perusahaan->save();
        return response()->json([
            'status' => 200,
            'message' => 'Perusahaan Berhasil Diubah',
        ], 200);
    }

    public function listPerusahaanTable()
    {
        $perusahaan = ListPerusahaan::with(['karyawan', 'karyawanPending', 'pinjaman'])
            ->get();

        return DataTables::of($perusahaan)
            ->addIndexColumn()

            ->addColumn('list_karyawan', function ($row) {
                return '<a href="' . route('admin.list.karyawan', $row->id) . '" target="_blank">
                <span class="badge badge-info" title="Total karyawan yang telah di approve 2 layer">' . $row->karyawan->count() . '</span> 
                <span class="badge badge-pink" title="Total karyawan yang menunggu di approve">' . $row->karyawanPending->count() . '</span></a>';
            })

            ->addColumn('proses_pengajuan', function ($row) {
                return '<a href="' . route('admin.ksp.pengajuan', $row->id) . '" target="_blank">
                    <span class="badge badge-warning" title="Proses pengajuan dengan status pending">' . $row->pinjaman->where('status', 'pending')->count() . '</span>
                    <span class="badge badge-purple" title="Proses pengajuan dengan status exported">' . $row->pinjaman->where('status', 'exported')->count() . '</span>
                    <span class="badge badge-info" title="Proses pengajuan dengan status approve">' . $row->pinjaman->where('status', 'approve')->count() . '</span>
                    <span class="badge badge-pink" title="Proses pengajuan dengan status reject">' . $row->pinjaman->where('status', 'reject')->count() . '</span>
                </a>';
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

            ->rawColumns(['action', 'status', 'proses_pengajuan', 'list_karyawan'])
            ->toJson();
    }

    public function deletePerusahaan(Request $req)
    {
        pinjaman::where('id_perusahaan', $req->id)->delete();
        list_data_ksp::where('id_list_perusahaan', $req->id)->delete();
        ListPerusahaan::where('id', $req->id)->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil menghapus Perusahaan, data karyawan, dan pinjaman',
        ], 200);
    }

    public function listKaryawanTable($id)
    {
        $perusahaan = list_data_ksp::where('id_list_perusahaan', $id)
            ->where('approve_satu', 'approve')
            ->where('approve_dua', 'approve')
            ->get();

        return DataTables::of($perusahaan)
            ->addIndexColumn()

            ->addColumn('penghasilan', function ($row) {
                return 'Rp' . number_format($row->penghasilan);
            })
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-sm btn-primary mr-2" onclick="edit(' . $row->id . ')"><i class="mdi mdi-pencil"></i></button>
                <button class="btn btn-sm btn-info mr-2" onclick="approve(' . $row->id . ')"><i class=" mdi mdi-file-check"></i> Verifikasi</button>
                ';
            })

            ->rawColumns(['action'])
            ->toJson();
    }

    public function listKaryawan($id)
    {
        $perusahaan = ListPerusahaan::where('id', $id)->first();
        if (isset($perusahaan)) {
            return view('admin.ksp.perusahaan.karyawan', compact(['id', 'perusahaan']));
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
            $listKaryawanPending = list_data_ksp::where('id_list_perusahaan', $req->id)
                ->where('approve_satu', 'pending')
                ->get();
        } elseif ($admin == "master") {
            $listKaryawanPending = list_data_ksp::where('id_list_perusahaan', $req->id)
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


        $pengajuan = pinjaman::where('status', 'pending')->with('user')->get();

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
                $plafon = "Rp" . number_format($row->nominal);
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
            $list = list_data_ksp::where('id_list_perusahaan', $req->id)
                ->where('approve_satu', 'pending')
                ->count();
        } elseif ($admin == "master") {
            $list = list_data_ksp::where('id_list_perusahaan', $req->id)
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
            $list = list_data_ksp::where('id_list_perusahaan', $req->id)->update(['approve_satu' => 'approve']);
        } elseif ($admin == "master") {
            $list = list_data_ksp::where('id_list_perusahaan', $req->id)->update(['approve_dua' => 'approve']);
        }
        return response()->json([
            'status' => 200,
            'message' => 'Sukses approve list karyawan',
        ], 200);
    }

    public function konfigurasi()
    {
        $amount = amount_pinjaman::first();
        return view('admin.ksp.konfigurasi.index', compact('amount'));
    }

    public function konfigurasiTable()
    {

        $tenor = tenor_pinjaman::get();

        return DataTables::of($tenor)
            ->addIndexColumn()

            ->addColumn('tenor', function ($row) {
                return $row->tenor . ' Bulan';
            })
            ->addColumn('rate', function ($row) {
                return $row->rate . ' %';
            })
            ->addColumn('biaya_admin', function ($row) {
                return $row->biaya_admin . ' %';
            })

            ->addColumn('iuran_anggota', function ($row) {
                return "Rp" . number_format($row->iuran_anggota);
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
        $tenor->tenor = $req->tenor;
        $tenor->rate = $req->rate;
        $tenor->biaya_admin = $req->biaya_admin;
        $tenor->iuran_anggota = $req->iuran_anggota;
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
        $tenor->iuran_anggota = $req->iuran_anggota;
        $tenor->save();
        return response()->json([
            'status' => 200,
            'message' => 'Sukses edit configurasi tenor',
        ], 200);
    }
    public function pengajuanIndex($id)
    {
        $perusahaan = ListPerusahaan::where('id', $id)->first();
        if (isset($perusahaan)) {
            return view('admin.ksp.pengajuan.index', compact(['id', 'perusahaan']));
        } else {
            abort(404);
        }
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
                return "Rp" . number_format($row->nominal);
            })

            ->addColumn('tenor', function ($row) {
                return $row->tenor . " Bulan";
            })
            ->addColumn('biaya_admin', function ($row) {
                return $row->biaya_admin . " %";
            })

            ->addColumn('rate', function ($row) {
                return $row->rate . " %";
            })

            ->addColumn('iuran_anggota', function ($row) {
                return "Rp" . number_format($row->iuran_anggota);
            })

            ->addColumn('total_biaya_admin', function ($row) {
                return "Rp" . number_format($row->total_biaya_admin);
            })

            ->addColumn('jumlah_yang_diterima', function ($row) {
                return "Rp" . number_format($row->jumlah_yang_diterima);
            })
            // ->rawColumns(['action'])
            ->toJson();
    }
    public function pengajuanExportTable(Request $req)
    {
        $pinjaman = pinjaman::where('id_perusahaan', $req->id)
            ->where('status', 'exported')
            ->where('status_otp', 'success')
            ->with('data_karyawan')
            ->get();

        return DataTables::of($pinjaman)
            ->addIndexColumn()

            ->addColumn('nama', function ($row) {
                return $row->data_karyawan->nama_karyawan;
            })
            ->addColumn('nominal', function ($row) {
                return "Rp" . number_format($row->nominal);
            })

            ->addColumn('tenor', function ($row) {
                return $row->tenor . " Bulan";
            })
            ->addColumn('biaya_admin', function ($row) {
                return $row->biaya_admin . " %";
            })

            ->addColumn('rate', function ($row) {
                return $row->rate . " %";
            })

            ->addColumn('iuran_anggota', function ($row) {
                return "Rp" . number_format($row->iuran_anggota);
            })

            ->addColumn('total_biaya_admin', function ($row) {
                return "Rp" . number_format($row->total_biaya_admin);
            })

            ->addColumn('jumlah_yang_diterima', function ($row) {
                return "Rp" . number_format($row->jumlah_yang_diterima);
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
                return "Rp" . number_format($row->nominal);
            })

            ->addColumn('tenor', function ($row) {
                return $row->tenor . " Bulan";
            })
            ->addColumn('biaya_admin', function ($row) {
                return $row->biaya_admin . " %";
            })

            ->addColumn('rate', function ($row) {
                return $row->rate . " %";
            })

            ->addColumn('iuran_anggota', function ($row) {
                return "Rp" . number_format($row->iuran_anggota);
            })

            ->addColumn('total_biaya_admin', function ($row) {
                return "Rp" . number_format($row->total_biaya_admin);
            })

            ->addColumn('jumlah_yang_diterima', function ($row) {
                return "Rp" . number_format($row->jumlah_yang_diterima);
            })
            // ->rawColumns(['action'])
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
                return "Rp" . number_format($row->nominal);
            })

            ->addColumn('tenor', function ($row) {
                return $row->tenor . " Bulan";
            })
            ->addColumn('biaya_admin', function ($row) {
                return $row->biaya_admin . " %";
            })

            ->addColumn('rate', function ($row) {
                return $row->rate . " %";
            })

            ->addColumn('iuran_anggota', function ($row) {
                return "Rp" . number_format($row->iuran_anggota);
            })

            ->addColumn('total_biaya_admin', function ($row) {
                return "Rp" . number_format($row->total_biaya_admin);
            })

            ->addColumn('jumlah_yang_diterima', function ($row) {
                return "Rp" . number_format($row->jumlah_yang_diterima);
            })
            // ->rawColumns(['action'])
            ->toJson();
    }




    public function pengajuanPendingExport(Request $req)
    {
        $perusahaan = ListPerusahaan::where('id', $req->id)->first();
        $pinjaman = pinjaman::where('id_perusahaan', $perusahaan->id)
            ->where('status', 'pending')
            ->where('status_otp', 'success')
            ->with('data_karyawan')
            ->get();

        return Excel::download(new PinjamanPendingExport($pinjaman), $perusahaan->nama . "-" . Carbon::now()->format('Y-m-d') . '-pinjaman-pending.xlsx');

        // return (new PinjamanPendingExport((int) $req->id))->download($perusahaan->nama . "-" . Carbon::now()->format('Y-m-d') . '-pinjaman-pending.xlsx');
    }
    public function pengajuanProsesToExport(Request $req)
    {
        $pinjamans = pinjaman::where('id_perusahaan', $req->id)
            ->where('status', 'pending')
            ->where('status_otp', 'success')
            ->get();

        foreach ($pinjamans as $pinjaman) {
            $notif = new notification();
            $notif->user_id = $pinjaman->user_id;
            $notif->title = "Pengajuan Anda Telah Diperbarui";
            $notif->description = "Selamat! Status pengajuan Anda telah diperbarui, kami akan meneruskan data pinjaman anda kepada pihak perusahaan anda. Silakan cek aplikasi untuk detail selengkapnya.";
            $notif->type = 'pinjaman_payroll';
            $notif->read = 'belum';
            $notif->save();
        }

        $pinjamans = pinjaman::where('id_perusahaan', $req->id)
            ->where('status', 'pending')
            ->where('status_otp', 'success')
            ->update(['status' => 'exported']);
        // 
        return response()->json([
            'status' => 200,
            'message' => 'Sukses mengubah status menjadi exported',
        ], 200);
    }

    public function pengajuanExportUpload(UploadListKaryawanRequest $req)
    {
        try {
            Excel::import(new ExportImportData(), $req->file('file'));
            return response()->json([
                'status' => 200,
                'message' => 'Berhasil Update Data Pinjaman',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 400,
                'message' => 'Harap Periksa Kembali Data Anda',
            ], 400);
        }
    }

    public function konfigurasiAmount(AmountPinjamanRequest $req)
    {
        $amount = amount_pinjaman::FirstOrNew();
        $amount->minimal = $req->minimal;
        $amount->maximal = $req->maximal;
        $amount->save();
        return response()->json([
            'status' => 200,
            'message' => 'Amount pinjaman berhasil diperbarui',
        ], 200);
    }

    public function pengajuanStatistic(Request $req)
    {
        $pengajuan = pinjaman::where('id_perusahaan', $req->id)->first();
        return response()->json([
            'status' => 200,
            'belum_validasi_wa' => $pengajuan->where('status_otp', 'pending')->count(),
            'pending' => $pengajuan->where('status_otp', 'success')->where('status', 'pending')->count(),
            'exported' => $pengajuan->where('status_otp', 'success')->where('status', 'exported')->count(),
            'approve' => $pengajuan->where('status_otp', 'success')->where('status', 'approve')->count(),
            'reject' => $pengajuan->where('status_otp', 'success')->where('status', 'reject')->count(),
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
                return "Rp." . number_format($row->total_fee_base);
            })

            ->addColumn('income_ksp', function ($row) {
                return "Rp." . number_format($row->income_ksp);
            })

            ->addColumn('iuran_anggota', function ($row) {
                return "Rp." . number_format($row->iuran_anggota);
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
}
