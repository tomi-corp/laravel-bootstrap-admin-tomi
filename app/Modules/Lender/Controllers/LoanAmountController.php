<?php

namespace App\Modules\Lender\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Lender\Models\Company;
use App\Modules\Lender\Models\LoanAmount;
use App\Modules\Lender\Requests\LoanAmountRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoanAmountController extends Controller
{
    public function index(Request $request): View
    {
        $selectedCompany = $this->selectedCompany($request);

        if (! $selectedCompany) {
            return view('lender.loan-amounts.companies', [
                'companies' => $this->companiesWithAmountStatus(),
            ]);
        }

        return view('lender.loan-amounts.index', [
            'amounts' => LoanAmount::query()
                ->with('company:id,nama,id_lender')
                ->whereHas('company', fn ($query) => $query->where('id_lender', $this->lenderId()))
                ->where('id_perusahaan', $selectedCompany->id)
                ->orderBy('id_perusahaan')
                ->get(),
            'selectedCompany' => $selectedCompany,
        ]);
    }

    public function create(Request $request): View
    {
        $selectedCompany = $this->selectedCompany($request);

        return view('lender.loan-amounts.create', [
            'amount' => new LoanAmount([
                'id_perusahaan' => $selectedCompany?->id,
                'iuran_anggota' => 0,
                'minimal' => 0,
                'maximal' => 0,
                'maximal_persentase' => 0,
            ]),
            'companies' => $this->companies(),
        ]);
    }

    public function store(LoanAmountRequest $request): RedirectResponse
    {
        LoanAmount::query()->create($request->validated());

        return redirect()
            ->route('lender.loan-amounts.index', ['company_id' => $request->validated('id_perusahaan')])
            ->with('success', 'Nominal pinjaman berhasil ditambahkan.');
    }

    public function edit(LoanAmount $loanAmount): View
    {
        $this->authorizeLoanAmount($loanAmount);

        return view('lender.loan-amounts.edit', [
            'amount' => $loanAmount,
            'companies' => $this->companies(),
        ]);
    }

    public function update(LoanAmountRequest $request, LoanAmount $loanAmount): RedirectResponse
    {
        $this->authorizeLoanAmount($loanAmount);
        $loanAmount->update($request->validated());

        return redirect()
            ->route('lender.loan-amounts.index', ['company_id' => $request->validated('id_perusahaan')])
            ->with('success', 'Nominal pinjaman berhasil diperbarui.');
    }

    public function destroy(LoanAmount $loanAmount): RedirectResponse
    {
        $this->authorizeLoanAmount($loanAmount);
        $companyId = $loanAmount->id_perusahaan;
        $loanAmount->delete();

        return redirect()
            ->route('lender.loan-amounts.index', ['company_id' => $companyId])
            ->with('success', 'Nominal pinjaman berhasil dihapus.');
    }

    private function selectedCompany(Request $request): ?Company
    {
        if (! $request->filled('company_id')) {
            return null;
        }

        return Company::query()
            ->where('id_lender', $this->lenderId())
            ->findOrFail($request->integer('company_id'));
    }

    private function companies()
    {
        return Company::query()
            ->where('id_lender', $this->lenderId())
            ->orderBy('nama')
            ->get(['id', 'nama', 'id_lender']);
    }

    private function companiesWithAmountStatus()
    {
        return Company::query()
            ->where('id_lender', $this->lenderId())
            ->withExists('loanAmount')
            ->orderBy('nama')
            ->get(['id', 'nama', 'id_lender']);
    }

    private function authorizeLoanAmount(LoanAmount $loanAmount): void
    {
        abort_unless(
            $loanAmount->company()->where('id_lender', $this->lenderId())->exists(),
            404
        );
    }

    private function lenderId(): int
    {
        return (int) auth('lender')->user()->id_lender;
    }
}
