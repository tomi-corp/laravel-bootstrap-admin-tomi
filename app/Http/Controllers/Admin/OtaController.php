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
        $file     = $req->file('file');

        $ext      = $file->getClientOriginalExtension();
        $filename = "ota_{$platform}_v{$versionMajor}_{$versionMinor}_{$otaUpdateID}" . time() . ".{$ext}";
        $path = $file->storeAs('ota', $filename);

        $ota = new OtaUpdate();
        $ota->platform = $platform;
        $ota->versionMajor = $versionMajor;
        $ota->versionMinor = $versionMinor;
        $ota->otaUpdateID = $otaUpdateID;
        $ota->changelog = $req->changelog;
        $ota->file_path = $filename;
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
            ->addColumn('file', function ($row) {
                return '<a href="' . route('admin.ota.download', $row->id) . '" target="_blank">Download</a>';
            })
            ->addColumn('action', function ($row) {
                // $btn = '<form action="' . route('admin.ota.destroy', $row->id) . '" method="POST" style="display:inline-block;">';
                // $btn .= csrf_field() . method_field('DELETE');
                // $btn .= '<button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Hapus update ini?\')">Hapus</button>';
                // $btn .= '</form>';
                // return $btn;
                return "av";
            })

            ->editColumn('created_at', function ($row) {
                return Carbon::parse($row->created_at)->format('Y-m-d H:i:s');
            })
            ->rawColumns(['file', 'action'])
            ->toJson();
    }

    public function otaDownload($id)
    {
        $dec = decryptSecure($id);
        $update = OtaUpdate::findOrFail($dec);
        return Storage::disk('ota')->download($update->file_path);
    }
}
