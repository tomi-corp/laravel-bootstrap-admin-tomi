<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Admin\KspController;
use App\Models\Admin;
use App\Models\provinsi;
use App\Services\Insight\InsightService;

class CompanyController extends KspController
{
    public function index(InsightService $insight)
    {
        return view('internal.companies.index', [
            'provinces' => provinsi::query()->orderBy('provinsi')->get(),
            'internalAdmins' => Admin::query()
                ->whereHas('role.instansi', fn ($query) => $query->where('code', 'internal'))
                ->whereHas('role', fn ($query) => $query->whereIn('code', ['admin', 'super_admin']))
                ->orderBy('username')
                ->get(['id', 'username', 'email']),
        ]);
    }
}
