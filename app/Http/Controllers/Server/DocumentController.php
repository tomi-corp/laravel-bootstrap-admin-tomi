<?php

namespace App\Http\Controllers\Server;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class DocumentController extends Controller
{
    public function pk(Request $request)
    {
        $path = 'assets/pk/' . $request->document;
        $disk = Storage::disk('local');
        $html = $disk->get($path);

        // $document = 'data:application/pdf;base64,' . $base64Pdf;

        // if (Storage::disk('local')->exists('app/assets/pk/' . $request->document)) {
        //     // The file exists on the 'public' disk
        //     return "dsadsadsa";
        // } else {
        //     // The file does not exist
        // }
        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $html . '"'
        ]);
    }
}
