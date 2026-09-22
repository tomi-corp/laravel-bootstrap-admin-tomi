<?php

namespace App\Modules\Lender\Controllers;

use App\Http\Controllers\Controller;
use App\Models\bank;
use App\Modules\Lender\Models\Disbursement;
use App\Modules\Lender\Models\Loan;
use App\Modules\Lender\Requests\DisbursementRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DisbursementCrudController extends Controller
{
    public function index(): View
    {
        return view('lender.disbursements.index', [
            'disbursements' => Disbursement::query()->with(['loan', 'admin', 'bank'])->latest()->get(),
        ]);
    }

    public function create(): View
    {
        return view('lender.disbursements.create', $this->formData(new Disbursement));
    }

    public function store(DisbursementRequest $request): RedirectResponse
    {
        Disbursement::query()->create($this->withAdmin($request->validated()));

        return redirect()->route('lender.disbursements.index')->with('success', 'Disbursement berhasil ditambahkan.');
    }

    public function show(Disbursement $disbursement): View
    {
        return view('lender.disbursements.show', [
            'disbursement' => $disbursement->load(['loan', 'admin', 'bank']),
        ]);
    }

    public function edit(Disbursement $disbursement): View
    {
        return view('lender.disbursements.edit', $this->formData($disbursement));
    }

    public function update(DisbursementRequest $request, Disbursement $disbursement): RedirectResponse
    {
        $disbursement->update($this->withAdmin($request->validated()));

        return redirect()->route('lender.disbursements.index')->with('success', 'Disbursement berhasil diperbarui.');
    }

    public function destroy(Disbursement $disbursement): RedirectResponse
    {
        $disbursement->delete();

        return redirect()->route('lender.disbursements.index')->with('success', 'Disbursement berhasil dihapus.');
    }

    private function formData(Disbursement $disbursement): array
    {
        return [
            'disbursement' => $disbursement,
            'loans' => Loan::query()->latest()->limit(500)->get(['id', 'id_pinjaman', 'nominal']),
            'banks' => bank::query()->orderBy('code_bank')->get(['id', 'code_bank', 'description']),
        ];
    }

    private function withAdmin(array $data): array
    {
        $data['ksp_admin_id'] = auth('lender')->id();

        return $data;
    }
}
