<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;

class CompanyController extends Controller
{
    public function index()
    {
        return view('partner.company.index');
    }

    public function configuration()
    {
        return view('partner.company.configuration.index');
    }
}
