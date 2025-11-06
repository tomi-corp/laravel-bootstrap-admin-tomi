<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\download;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;



class DownloadController extends Controller
{
    protected $allowedFiles = [
        'tomi-101.apk',
    ];

    public function download($filename)
    {

        if (!in_array($filename, $this->allowedFiles)) {
            abort(404);
        }

        $filePath = 'aplikasi/' . $filename;

        if (!Storage::disk('local')->exists($filePath)) {
            abort(404);
        }

        $download = new download();
        $download->ip_address = request()->ip();
        $download->user_agent = request()->userAgent();
        $download->save();

        $absolutePath = Storage::disk('local')->path($filePath);

        return Response::download($absolutePath, $filename, [
            'Content-Type' => 'application/vnd.android.package-archive',
        ]);
    }
}
