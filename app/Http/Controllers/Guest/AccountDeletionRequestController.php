<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\AccountDeletionRequest;
use Illuminate\Http\Request;

class AccountDeletionRequestController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20'],
            'deletion_reason' => ['required', 'string'],
        ]);

        $deletionRequest = AccountDeletionRequest::create($validated);

        return response()->json([
            'status' => 201,
            'message' => 'Pengajuan penghapusan akun berhasil dikirim.',
            'data' => [
                'id' => $deletionRequest->id,
            ],
        ], 201);
    }
}
