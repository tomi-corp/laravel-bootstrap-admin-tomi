<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Ksp\Lender\AddLenderRequest;
use App\Http\Requests\Admin\Lender\UpdateLenderRequest;
use App\Http\Resources\Admin\Lender\DetailLenderResource;
use App\Models\bank;
use App\Models\configurasi_lender;
use App\Models\kecamatan;
use App\Models\kelurahan;
use App\Models\kota;
use App\Models\lender;
use App\Models\lender_update;
use App\Models\provinsi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Response;


class LenderController extends Controller
{
    public function indexLender()
    {
        $bank = bank::get();
        $provinsis = provinsi::get();
        $data = [
            'bank' => $bank,
            'provinsis' => $provinsis,
        ];
        return view('admin.ksp.lender.index', compact('data'));
    }

    public function addLender(AddLenderRequest $req)
    {
        $lender = new lender();
        while (true) {
            $string = '#TomiKspLender' . '-' . Carbon::now()->format('dmY') . '-' . rand(1000, 9999);
            $lenderlist = lender::where('id_code_lender', $string)->first();
            if (!isset($lenderlist)) {
                break;
            }
        }

        $lender->id_code_lender = $string;
        $lender->pic_lender = $req->pic_lender;
        $lender->pic_internal = $req->pic_internal;
        $lender->nama = $req->nama_lender;
        $lender->email_lender = $req->email_lender;
        $lender->alamat_lengkap = $req->alamat_lengkap;
        $lender->telp = $req->telepon;
        $lender->nomor_pks = $req->nomor_pks;
        // file pks
        $file     = $req->file('file_pks');
        $ext      = ".pdf";
        $filename = "LENDER_PKS_{$string}_{$lender->nama}_" . time() . ".{$ext}";
        $path = $file->storeAs('pks_lender', $filename);

        $provinsi = provinsi::where('provinsi', $req->provinsi)->first();
        $kabupaten = kota::where('kabupaten', $req->kabupaten)->first();
        $kecamatan = kecamatan::where('kecamatan', $req->kecamatan)->first();
        $desaaa = explode("-", $req->desa);
        $desa = kelurahan::where('desa', $desaaa[0])
            ->where('kodepos', $desaaa[1])
            ->first();

        $lender->id_provinsi = $provinsi->id;
        $lender->id_kabupaten = $kabupaten->id;
        $lender->id_kecamatan = $kecamatan->id;
        $lender->id_desa = $desa->id;
        $lender->file_pks = $filename;
        $lender->last_update_file_pks = Carbon::now()->format('Y-m-d');
        // bank 
        $lender->id_bank = $req->bank;
        $lender->nama_rekening = $req->an_rekening;
        $lender->no_rekening = $req->no_rekening;
        $lender->created_by = auth('admin')->user()->id;

        $lender->save();

        return response()->json([
            'status' => 200,
            'message' => 'Lender Berhasil Ditambahkan',
        ], 200);
    }

    public function lenderTable()
    {
        $lender = Lender::with(['provinsi', 'kabupaten', 'kecamatan', 'desa', 'pendingApproval', 'bank'])->get();
        $configurasi = configurasi_lender::first();

        return DataTables::of($lender)
            ->addIndexColumn()

            ->addColumn('pendingApproval', function ($row) {
                if (isset($row->pendingApproval)) {
                    $pendingApproval = '<span class="badge badge-warning">Locked</span>';
                } else {
                    $pendingApproval = '<span class="badge badge-success">Available to Edit</span>';
                }
                return $pendingApproval;
            })

            ->addColumn('bank', function ($row) {
                return $row->bank->code_bank;
            })

            ->addColumn('last_updated', function ($row) {
                return Carbon::parse($row->updated_at)->format('Y-m-d H:i:s');
            })

            ->addColumn('pks', function ($row) {
                return '<button class="btn btn-sm btn-primary mr-2" onclick="filePks(' . $row->id . ')"><i class="mdi mdi-file-document-outline"></i> </button>';
            })

            ->addColumn('expired_updated', function ($row) use ($configurasi) {;
                return Carbon::parse($row->updated_at)->addDays($configurasi->expired_days)->format('Y-m-d H:i:s');
            })

            ->addColumn('provinsi', function ($row) {
                return $row->provinsi->provinsi;
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

            ->addColumn('status', function ($row) {
                if ($row->status == "active") {
                    $status = '<span class="badge badge-success">ACTIVE</span>';
                } elseif ($row->status == "nonactive") {
                    $status = '<span class="badge badge-warning">INACTIVE</span>';
                }
                return $status;
            })
            ->addColumn('action', function ($row) {

                if (isset($row->pendingApproval)) {
                    $action = '<button class="btn btn-sm btn-primary mr-2 disabled" ><i class="mdi mdi-pencil"></i> </button>
                    <button class="btn btn-sm btn-danger mr-2 disabled"><i class=" mdi mdi-trash-can"></i> </button>';
                } else {
                    $action = '<button class="btn btn-sm btn-primary mr-2" onclick="editLender(' . $row->id . ')"><i class="mdi mdi-pencil"></i> </button>
                    <button class="btn btn-sm btn-danger mr-2" onclick="deleteLender(' . $row->id . ')"><i class=" mdi mdi-trash-can"></i> </button>';
                }
                return $action;
            })

            ->rawColumns(['action', 'status', 'pks', 'pendingApproval'])
            ->only([
                'DT_RowIndex',
                'id_code_lender',
                'pks',
                'pic_lender',
                'pic_internal',
                'nama',
                'email_lender',
                'telp',
                'alamat_lengkap',
                'nomor_pks',
                'last_updated',
                'expired_updated',
                'provinsi',
                'kabupaten',
                'kecamatan',
                'desa',
                'status',
                'action',
                'pendingApproval',
                'bank',
                'nama_rekening',
                'no_rekening',
            ])
            ->toJson();
    }

    public function pksShow(Request $req)
    {
        $lender = lender::where('id', $req->id)->first();
        $disk = Storage::disk('local');
        $file = $disk->get('pks_lender/' . $lender->file_pks);
        return Response::make($file, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="PKS Lender"'
        ]);
    }

    public function lenderDetail(Request $req)
    {
        $lender =  lender::where('id', $req->id)->with([
            'provinsi',
            'kabupaten',
            'kecamatan',
            'desa',
        ])->first();
        return response()->json([
            'status' => 200,
            'data' => new DetailLenderResource($lender),
        ], 200);
    }

    public function updateLender(UpdateLenderRequest $req)
    {
        $lender = lender::where('id', $req->id)->first();
        $oldData = $lender->getOriginal();

        // Lakukan perubahan pada model (sebelum disimpan ke database)
        $lender->pic_lender = $req->pic_lender;
        $lender->pic_internal = $req->pic_internal;
        $lender->nama = $req->nama_lender;
        $lender->email_lender = $req->email_lender;
        $lender->alamat_lengkap = $req->alamat_lengkap;
        $lender->telp = $req->telepon;
        $lender->nomor_pks = $req->nomor_pks;
        // file pks
        if ($req->file('file_pks') !== null) {
            $file     = $req->file('file_pks');
            $ext      = ".pdf";
            $filename = "LENDER_PKS_{$lender->id_code_lender}_{$lender->nama}_" . time() . ".{$ext}";
            $path = $file->storeAs('pks_lender', $filename);
            $lender->file_pks = $filename;
            $lender->last_update_file_pks = Carbon::now()->format('Y-m-d');
        }

        $provinsi = provinsi::where('provinsi', $req->provinsi)->first();
        $kabupaten = kota::where('kabupaten', $req->kabupaten)->first();
        $kecamatan = kecamatan::where('kecamatan', $req->kecamatan)->first();
        $desaaa = explode("-", $req->desa);
        $desa = kelurahan::where('desa', $desaaa[0])
            ->where('kodepos', $desaaa[1])
            ->first();

        $lender->id_provinsi = $provinsi->id;
        $lender->id_kabupaten = $kabupaten->id;
        $lender->id_kecamatan = $kecamatan->id;
        $lender->id_desa = $desa->id;
        // bank 
        $lender->id_bank = $req->bank;
        $lender->nama_rekening = $req->an_rekening;
        $lender->no_rekening = $req->no_rekening;
        $lender->update_by = auth('admin')->user()->id;

        // Dapatkan data yang diubah (hanya kolom yang berubah)
        $changedData = $lender->getDirty();

        // Gabungkan oldData dan newData ke dalam satu array untuk JSON
        $logData = [
            'old_data' => array_intersect_key($oldData, $changedData), // Ambil data lama hanya untuk kolom yang berubah
            'new_data' => $changedData
        ];

        // Simpan data perubahan ke tabel sementara
        $lender_updates = new lender_update();
        $lender_updates->lender_id = $lender->id;
        $lender_updates->new_data = json_encode($logData);
        $lender_updates->requester_id = auth('admin')->user()->id;
        $lender_updates->status_pertama = 'pending';
        $lender_updates->status_kedua = 'pending';
        $lender_updates->save();

        return response()->json([
            'status' => 200,
            'message' => 'Request Update Lender Berhasil, Harap menunggu approval',
        ], 200);
    }

    public function deleteLender(Request $req)
    {
        $lender = lender::where('id', $req->id)
            ->with('pendingApproval')
            ->first();

        if (isset($lender->pendingApproval)) {
            return response()->json([
                'status' => 400,
                'message' => 'Data lender sedang tahap pending approval, tidak bisa dihapus',
            ], 400);
        }

        $oldData = $lender->getOriginal();
        $lender->deleted_at = now();
        $changedData = $lender->getDirty();

        $logData = [
            'old_data' => array_intersect_key($oldData, $changedData),
            'new_data' => $changedData
        ];
        // Simpan data perubahan ke tabel sementara
        $lender_updates = new lender_update();
        $lender_updates->lender_id = $lender->id;
        $lender_updates->new_data = json_encode($logData);
        $lender_updates->requester_id = auth('admin')->user()->id;
        $lender_updates->status_pertama = 'pending';
        $lender_updates->status_kedua = 'pending';
        $lender_updates->save();

        return response()->json([
            'status' => 200,
            'message' => 'Request Delete Lender Berhasil, Harap menunggu approval',
        ], 200);
    }
}
