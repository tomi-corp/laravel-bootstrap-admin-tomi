<?php

namespace App\Http\Controllers\Server;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function pk(Request $request)
    {
        $disk = Storage::disk('local');
        $html = $disk->get('assets/pk/' . $request->document);
        return $html;
    }

    public function image(Request $request)
    {
        $disk = Storage::disk('local');
        $path = 'assets/users/' . $request->image;
        $image = $disk->get($path);
        $mimeType = $disk->mimeType($path);
        $data = [
            'content-type' => $mimeType,
            'image' => $image
        ];
        return $image;
    }
}
