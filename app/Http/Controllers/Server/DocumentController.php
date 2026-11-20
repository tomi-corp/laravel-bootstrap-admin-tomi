<?php

namespace App\Http\Controllers\Server;

use App\Http\Controllers\Concerns\ReadsUserDocuments;
use App\Http\Controllers\Controller;
use App\Models\PrivyDigitalIdSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    use ReadsUserDocuments;

    public function pk(Request $request)
    {
        $disk = Storage::disk('local');
        $fileName = $request->document;
        $path = $this->pkDocumentPath($fileName);

        if ($path === null) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid filename',
            ], 400);
        }

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
        $path = $this->resolveImagePath($request);

        if ($path === null) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid image request',
            ], 422);
        }

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
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
        ]);
    }

    private function resolveImagePath(Request $request): ?string
    {
        $sessionId = $request->input('privy_digital_id_session_id');
        $imageType = $request->input('image_type');

        if ($sessionId !== null || $imageType !== null) {
            if (!is_numeric($sessionId) || !in_array($imageType, ['ktp', 'selfie'], true)) {
                return null;
            }

            return $this->privySessionImagePath(PrivyDigitalIdSession::find($sessionId), $imageType);
        }

        return $this->userImagePath($request->input('image'));
    }
}
