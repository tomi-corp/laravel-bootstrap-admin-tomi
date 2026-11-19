<?php

namespace App\Http\Controllers\Internal;

use App\Models\bank;
use App\Models\provinsi;

class LenderController extends \App\Http\Controllers\Admin\LenderController
{
    public function index()
    {
        return view('internal.lenders.index', [
            'banks' => bank::query()->orderBy('code_bank')->get(),
            'provinces' => provinsi::query()->orderBy('provinsi')->get(),
        ]);
    }
}
