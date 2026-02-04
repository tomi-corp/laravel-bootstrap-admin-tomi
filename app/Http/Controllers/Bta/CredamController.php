<?php

namespace App\Http\Controllers\Bta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CredamController extends Controller
{
    public function credamIndex()
    {
        return view('bta.credam.index');
    }
}
