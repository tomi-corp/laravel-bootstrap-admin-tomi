<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\bank;
use App\Models\provinsi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LenderPageController extends Controller
{
    public function index()
    {
        return view('admin.lender.index', [
            'data' => [
                'bank' => bank::get(),
                'provinsis' => provinsi::get(),
                'features' => Schema::hasTable('features')
                    ? DB::table('features')->whereNull('deleted_at')->orderBy('id')->get(['id', 'name'])
                    : collect(),
            ],
        ]);
    }
}
