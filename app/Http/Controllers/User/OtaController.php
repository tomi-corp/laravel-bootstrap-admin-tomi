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
        $parts = explode('.', $req->version);
        $v1 = $parts[0] . '.' . $parts[1];
        $v2 = $parts[2] . '.' . $parts[3];

        $lastest = OtaUpdate::where('platform', $platform)
            ->orderByDesc('versionMajor')
            ->orderByDesc('versionMinor')
            ->first();

        if ($lastest && version_compare($lastest->versionMajor, $v1, '>')) {
            return response()->json([
                'updateMajor' => true,
                'updateMinor' => false,
                'versionMajor' => $lastest->versionMajor,
                'versionMinor' => $lastest->versionMinor,
                'changelog' => $lastest->changelog,
            ]);
        } else if ($lastest && version_compare($lastest->versionMinor, $v2, '>')) {
            return response()->json([
                'updateMajor' => false,
                'updateMinor' => true,
                'versionMajor' => $lastest->versionMajor,
                'versionMinor' => $lastest->versionMinor,
                'changelog' => $lastest->changelog,
            ]);
        } else {
            return response()->json([
                'updateMajor' => false,
                'updateMinor' => false,
                'versionMajor' => $lastest->versionMajor,
                'versionMinor' => $lastest->versionMinor,
                'changelog' => $lastest->changelog,
            ]);
        }
    }

    public function download($id)
    {
        $update = OtaUpdate::findOrFail(decryptSecure($id));
        return response()->download(storage_path("app/{$update->file_path}"));
    }
}
