<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Pengajuan\DetailPengajuanResource;
use App\Models\pinjaman;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;


class PengajuanPinjamanController extends Controller
{
    public function index()
    {
        return view('admin.pengajuanPinjaman.index');
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
}
