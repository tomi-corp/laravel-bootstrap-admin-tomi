<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\InvoiceEmail;
use App\Models\invoicing_pinjaman;
use App\Models\pinjaman;
use App\Models\User;
use App\Models\xenditApi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;



class DashboardController extends Controller
{
    public function dashboard()
    {
        // register
        $registerBulanIni = User::whereMonth('created_at', Carbon::now()->month)
            ->count();
        $registerBulanKemarin = User::whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->count();

        $persentase =  ($registerBulanIni - $registerBulanKemarin) * $registerBulanKemarin * 100 / 100;
        $register = [
            'persentase' => $persentase,
            'registerBulanIni' => $registerBulanIni,
        ];
        // transaction
        return view('admin.dashboard.index', compact('register'));
    }

    public function utils()
    {
        $register = User::orderBy('created_at', Carbon::now())->take(10)->get();
        $pinjaman = pinjaman::orderBy('created_at', Carbon::now())->take(10)->get();

        $merged = $register->merge($pinjaman);
        dd($merged);
        // return response()->json([
        //     'status' => 200,
        //     'lastRegister' => ,
        // ], 200);
    }
}
