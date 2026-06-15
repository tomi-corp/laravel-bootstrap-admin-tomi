<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;

class EmployeeController extends Controller
{
    public function index()
    {
        return view('partner.company.employee.index');
    }
}
