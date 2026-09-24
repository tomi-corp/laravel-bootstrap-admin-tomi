<?php

namespace App\Modules\Lender\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Lender\Models\Company;
use App\Modules\Lender\Models\LoanTenor;
use App\Modules\Lender\Requests\LoanTenorRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoanTenorController extends Controller
{
    public function index(Request $request): View
    {
        $selectedCompany = $this->selectedCompany($request);

        if (! $selectedCompany) {
            return view('lender.configurations.companies', [
                'companies' => $this->companiesWithTenorCount(),
            ]);
        }

        return view('lender.configurations.index', [
            'tenors' => LoanTenor::query()
                ->with('company:id,nama,id_lender')
                ->whereHas('company', fn ($query) => $query->where('id_lender', $this->lenderId()))
                ->where('id_perusahaan', $selectedCompany->id)
                ->orderBy('id_perusahaan')
                ->orderBy('tenor')
                ->get(),
            'selectedCompany' => $selectedCompany,
        ]);
    }

    public function create(Request $request): View
    {
        $selectedCompany = $this->selectedCompany($request);

        return view('lender.configurations.create', [
            'tenor' => new LoanTenor([
                'id_perusahaan' => $selectedCompany?->id,
                'asuransi' => 0,
                'provisi' => 0,
                'biaya_admin' => 0,
                'fee_tomi' => 0,
                'profesional_fee_1' => 0,
                'profesional_fee_2' => 0,
                'ksp' => 0,
            ]),
            'companies' => $this->companies(),
        ]);
    }

    public function store(LoanTenorRequest $request): RedirectResponse
    {
        LoanTenor::query()->create($request->validated());

        return redirect()
            ->route('lender.configurations.index', ['company_id' => $request->validated('id_perusahaan')])
            ->with('success', 'Konfigurasi tenor berhasil ditambahkan.');
    }

    public function edit(LoanTenor $configuration): View
    {
        $this->authorizeConfiguration($configuration);

        return view('lender.configurations.edit', [
            'tenor' => $configuration,
            'companies' => $this->companies(),
        ]);
    }

    public function update(LoanTenorRequest $request, LoanTenor $configuration): RedirectResponse
    {
        $this->authorizeConfiguration($configuration);
        $configuration->update($request->validated());

        return redirect()
            ->route('lender.configurations.index', ['company_id' => $request->validated('id_perusahaan')])
            ->with('success', 'Konfigurasi tenor berhasil diperbarui.');
    }

    public function destroy(LoanTenor $configuration): RedirectResponse
    {
        $this->authorizeConfiguration($configuration);
        $companyId = $configuration->id_perusahaan;
        $configuration->delete();

        return redirect()
            ->route('lender.configurations.index', ['company_id' => $companyId])
            ->with('success', 'Konfigurasi tenor berhasil dihapus.');
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

    private function companiesWithTenorCount()
    {
        return Company::query()
            ->where('id_lender', $this->lenderId())
            ->withCount('loanTenors')
            ->orderBy('nama')
            ->get(['id', 'nama', 'id_lender']);
    }

    private function authorizeConfiguration(LoanTenor $configuration): void
    {
        abort_unless(
            $configuration->company()->where('id_lender', $this->lenderId())->exists(),
            404
        );
    }

    private function lenderId(): int
    {
        return (int) auth('lender')->user()->id_lender;
    }
}
