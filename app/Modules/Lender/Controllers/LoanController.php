<?php

namespace App\Modules\Lender\Controllers;

use App\Http\Controllers\Controller;
use App\Models\kyc_user;
use App\Models\lender;
use App\Models\list_karyawan;
use App\Models\User;
use App\Modules\Lender\Models\Company;
use App\Modules\Lender\Models\Loan;
use App\Modules\Lender\Requests\LoanRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LoanController extends Controller
{
    public function index(): View
    {
        return view('lender.loans.index', [
            'loans' => Loan::query()->with(['user', 'company', 'lender'])->latest()->get(),
        ]);
    }

    public function create(): View
    {
        return view('lender.loans.create', $this->formData(new Loan));
    }

    public function store(LoanRequest $request): RedirectResponse
    {
        Loan::query()->create($this->withDefaults($request->validated()));

        return redirect()->route('lender.loans.index')->with('success', 'Pinjaman berhasil ditambahkan.');
    }

    public function show(Loan $loan): View
    {
        return view('lender.loans.show', [
            'loan' => $loan->load(['user', 'company', 'employee', 'lender', 'kyc']),
        ]);
    }

    public function edit(Loan $loan): View
    {
        return view('lender.loans.edit', $this->formData($loan));
    }

    public function update(LoanRequest $request, Loan $loan): RedirectResponse
    {
        $loan->update($this->withDefaults($request->validated()));

        return redirect()->route('lender.loans.index')->with('success', 'Pinjaman berhasil diperbarui.');
    }

    public function destroy(Loan $loan): RedirectResponse
    {
        $loan->delete();

        return redirect()->route('lender.loans.index')->with('success', 'Pinjaman berhasil dihapus.');
    }

    private function formData(Loan $loan): array
    {
        return [
            'loan' => $loan,
            'users' => User::query()->orderBy('name')->limit(500)->get(['id', 'name', 'email']),
            'companies' => Company::query()->orderBy('nama')->get(['id', 'nama']),
            'employees' => list_karyawan::query()->orderBy('nama_karyawan')->limit(500)->get(['id', 'nama_karyawan', 'nik']),
            'lenders' => lender::query()->orderBy('nama')->get(['id', 'nama']),
            'kycs' => kyc_user::query()->latest()->limit(500)->get(['id', 'user_id']),
        ];
    }

    private function withDefaults(array $data): array
    {
        $defaults = [
            'asuransi' => 0,
            'provisi' => 0,
            'biaya_admin' => 0,
            'iuran_anggota' => 0,
            'total_biaya_admin' => 0,
            'biaya_admin_ksp' => 0,
            'dpp_adm_ksp' => 0,
            'ppn_adm_ksp' => 0,
            'biaya_admin_pandu' => 0,
            'dpp_adm_pandu' => 0,
            'ppn_adm_pandu' => 0,
            'pendapatan_bunga' => 0,
            'dpp_bunga' => 0,
            'ppn_bunga' => 0,
            'biaya_layanan' => 0,
            'total_angsuran_perbulan' => 0,
            'jumlah_yang_diterima' => 0,
            'jumlah_asuransi' => 0,
            'jumlah_provisi' => 0,
            'profesional_fee_1' => 0,
            'profesional_fee_2' => 0,
            'jumlah_profesional_1' => 0,
            'jumlah_profesional_2' => 0,
            'ksp_fee' => 0,
            'cost_tomi' => null,
            'cost_ksp' => null,
            'status' => 'on_proses',
            'ttd' => 'n',
            'status_otp' => 'pending',
            'document_privy' => 'n',
        ];

        return array_merge($defaults, $data);
    }
}
