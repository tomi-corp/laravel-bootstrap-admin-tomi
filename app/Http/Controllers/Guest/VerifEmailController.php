<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\verif_email;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VerifEmailController extends Controller
{
    public function verifEmail($code)
    {
        $verifEmail = verif_email::where('code_rand', $code)->first();
        if (isset($verifEmail)) {
            if ($verifEmail->expired > Carbon::now()) {
                $user = User::where('email', $verifEmail->email)->first();
                $user->email_verified_at = Carbon::now();
                $user->save();
                $verifEmail->delete();
                return response()->json([
                    'status' => 200,
                    'message' => 'Berhasil verifikasi email',
                ], 200);
            } else {
                abort(404);
            }
        } else {
            abort(404);
        }
    }
}
