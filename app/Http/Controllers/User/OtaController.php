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
        $currentVersion = $req->version;

        $latest = OtaUpdate::where('platform', $platform)
            ->orderByDesc('id')
            ->first();

        if ($latest && version_compare($latest->version, $currentVersion, '>')) {
            return response()->json([
                'update' => true,
                'version' => $latest->version,
                'changelog' => $latest->changelog,
                'url' => url("/ota/download/" . encryptSecure($latest->id))
            ]);
        }

        return response()->json(['update' => false]);
    }

    public function download($id)
    {
        $update = OtaUpdate::findOrFail(decryptSecure($id));
        return response()->download(storage_path("app/{$update->file_path}"));
    }
}
