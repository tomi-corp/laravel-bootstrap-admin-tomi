<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class InvoiceFeePageController extends Controller
{
    public function index()
    {
        return view('admin.invoice-fee.index', [
            'defaultId' => null,
        ]);
    }

    public function show($id)
    {
        return view('admin.invoice-fee.index', [
            'defaultId' => $id,
        ]);
    }
}
