<?php

namespace App\Http\Controllers\Bta;

use App\Http\Controllers\Controller;
use App\Models\Otp;

class OtpController extends Controller
{
    public function index()
    {
        $otps = Otp::with('user')->latest()->get();

        return view('bta.otp.index', compact('otps'));
    }
}
