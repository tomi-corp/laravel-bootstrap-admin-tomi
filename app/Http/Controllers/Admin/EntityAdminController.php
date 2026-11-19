<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\View\View;

class EntityAdminController extends Controller
{
    public function internal(): View
    {
        return $this->render('internal', 'Internal Admins', 'Administrators assigned to the internal organization.');
    }

    public function lender(): View
    {
        return $this->render('lender', 'Lender Admins', 'Administrators assigned to lender organizations.');
    }

    public function referral(): View
    {
        return $this->render('referral', 'Referral Admins', 'Administrators assigned to referral partners.');
    }

    public function company(): View
    {
        return $this->render('company', 'Company Admins', 'Administrators assigned to partner companies.');
    }

    private function render(string $organization, string $title, string $description): View
    {
        $admins = Admin::query()
            ->with(['instansi', 'role', 'lender', 'company', 'referral'])
            ->whereHas('instansi', fn ($query) => $query->where('code', $organization === 'perusahaan' ? 'perusahaan' : $organization))
            ->orderByDesc('id')
            ->get();

        return view("admin.entity-admin.{$organization}.index", compact('admins', 'title', 'description'));
    }
}
