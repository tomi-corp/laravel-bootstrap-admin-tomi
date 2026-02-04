<?php

namespace App\Http\Controllers\Bta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
        return view('bta.dashboard.index');
    }
}
