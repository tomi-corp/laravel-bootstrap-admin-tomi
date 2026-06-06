<?php

namespace App\Http\Controllers\Server;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function pk(Request $request)
    {
        $disk = Storage::disk('local');
        $fileName = $request->document;

        if ($this->hasInvalidFileName($fileName)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid filename',
            ], 400);
        }

        $path = 'assets/pk/' . $fileName;

        if (!$disk->exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'File not found',
            ], 404);
        }

        $file = $disk->get($path);

        return Response::make($file, 200, [
            'Content-Type' => $disk->mimeType($path) ?: 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($fileName) . '"',
        ]);
    }

    public function image(Request $request)
    {
        $disk = Storage::disk('local');
        $fileName = $request->image;

        if ($this->hasInvalidFileName($fileName)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid filename',
            ], 400);
        }

        $path = 'assets/users/' . $fileName;

        if (!$disk->exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'File not found',
            ], 404);
        }

        $image = $disk->get($path);
        $mimeType = $disk->mimeType($path) ?: 'image/jpeg';

        return Response::make($image, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($fileName) . '"',
        ]);
    }

    private function hasInvalidFileName(?string $fileName): bool
    {
        return blank($fileName) || Str::contains($fileName, ['..', '/', '\\']);
    }
}
