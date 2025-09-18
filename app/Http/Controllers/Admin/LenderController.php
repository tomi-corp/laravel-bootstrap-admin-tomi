<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\provinsi;
use Illuminate\Http\Request;

class LenderController extends Controller
{
    public function index()
    {
        $provinsis = provinsi::get();
        return view('admin.ksp.lender.index', compact('provinsis'));
    }
}
