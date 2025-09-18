<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\OtaUpdate;
use Illuminate\Http\Request;

class OtaController extends Controller
{
    public function checkUpdate(Request $req)
    {
        $platform = $req->platform;
        $currentVersion = $req->current_version;
        $manifest_version  = $req->manifest_version ;
        $ota_update_id = $req->ota_update_id;

        $lastest = OtaUpdate::where('platform', $platform)
            ->orderByDesc('id')
            ->first();

        if ($lastest && version_compare($lastest->versionMajor, $currentVersion, '>')) {
            return response()->json([
                'updateMajor' => true,
                'updateMinor' => false,
                'versionMajor' => $lastest->versionMajor,
                'versionMinor' => $lastest->versionMinor,
                'changelog' => $lastest->changelog,
                'url' => ''
            ]);
        } else if ($lastest && version_compare($lastest->versionMinor, $manifest_version , '>') && $ota_update_id != $lastest->otaUpdateID) {
            return response()->json([
                'updateMajor' => false,
                'updateMinor' => true,
                'versionMajor' => $lastest->versionMajor,
                'versionMinor' => $lastest->versionMinor,
                'changelog' => $lastest->changelog,
                'url' => url("/ota/download/" . encryptSecure($lastest->id))
            ]);
        } else {
            return response()->json([
                'updateMajor' => false,
                'updateMinor' => false,
                'versionMajor' => $lastest->versionMajor,
                'versionMinor' => $lastest->versionMinor,
                'changelog' => $lastest->changelog,
                'url' => ''
            ]);
        }
    }

    public function download($id)
    {
        $update = OtaUpdate::findOrFail(decryptSecure($id));
        return response()->download(storage_path("app/{$update->file_path}"));
    }
}
