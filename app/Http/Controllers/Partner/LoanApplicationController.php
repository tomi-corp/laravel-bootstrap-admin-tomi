<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;

class LoanApplicationController extends Controller
{
    public function index()
    {
        return view('partner.company.loan-application.index');
    }
}
