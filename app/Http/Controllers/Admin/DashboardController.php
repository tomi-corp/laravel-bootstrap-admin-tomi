<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $registerBulanIni = User::whereMonth('created_at', Carbon::now()->month)->count();
        $registerBulanKemarin = User::whereMonth('created_at', Carbon::now()->subMonth()->month)->count();

        // dd($registerBulanKemarin);
        return view('admin.dashboard.index');
    }
}
