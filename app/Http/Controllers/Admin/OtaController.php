<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Ota\OtaUploadRequest;
use App\Models\OtaUpdate;
use App\Models\provinsi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class OtaController extends Controller
{
    public function otaIndex()
    {
        return view('admin.ota.index');
    }

    public function otaUpload(OtaUploadRequest $req)
    {
        $platform = $req->platform;
        $versionMajor  = $req->versionMajor;
        $versionMinor  = $req->versionMinor;
        $otaUpdateID  = $req->otaUpdateID;

        $ota = new OtaUpdate();
        $ota->platform = $platform;
        $ota->versionMajor = $versionMajor;
        $ota->versionMinor = $versionMinor;
        $ota->otaUpdateID = $otaUpdateID;
        $ota->changelog = $req->changelog;
        $ota->save();
        return response()->json([
            'status' => 200,
            'message' => 'OTA berhasil diupload'
        ], 200);
    }
    public function otaTable()
    {
        $data = OtaUpdate::latest();

        return DataTables::of($data)
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-sm btn-danger mr-2" onclick="deleteOta(' . $row->id . ')"><i class=" mdi mdi-trash-can"></i> </button>
                ';
            })

            ->editColumn('created_at', function ($row) {
                return Carbon::parse($row->created_at)->format('Y-m-d H:i:s');
            })
            ->rawColumns(['file', 'action'])
            ->toJson();
    }


    public function deleteOta(Request $req)
    {
        $data = OtaUpdate::where('id', $req->id)->first()->delete();
        return response()->json([
            'status' => 200,
            'message' => 'OTA berhasil dihapus'
        ], 200);
    }
}
