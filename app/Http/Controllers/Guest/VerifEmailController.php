<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\verif_email;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VerifEmailController extends Controller
{
    public function verifEmail($code)
    {
        $verifEmail = verif_email::where('code_rand', $code)->first();
        if (isset($verifEmail)) {
            if ($verifEmail->expired < Carbon::now()) {
            } else {
                abort(404);
            }
        } else {
            abort(404);
        }
    }
}
