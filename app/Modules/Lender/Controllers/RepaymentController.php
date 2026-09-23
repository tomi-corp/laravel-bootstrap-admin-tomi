<?php

namespace App\Modules\Lender\Controllers;

use App\Http\Controllers\Controller;
use App\Models\invoicing_pinjaman;
use App\Modules\Lender\Models\Repayment;
use App\Modules\Lender\Requests\RepaymentRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RepaymentController extends Controller
{
    public function index(): View
    {
        return view('lender.repayments.index', [
            'repayments' => Repayment::query()->with(['invoice', 'admin'])->latest()->get(),
        ]);
    }

    public function create(): View
    {
        return view('lender.repayments.create', $this->formData(new Repayment));
    }

    public function store(RepaymentRequest $request): RedirectResponse
    {
        Repayment::query()->create($this->withAdmin($request->validated()));

        return redirect()->route('lender.repayments.index')->with('success', 'Repayment berhasil ditambahkan.');
    }

    public function show(Repayment $repayment): View
    {
        return view('lender.repayments.show', [
            'repayment' => $repayment->load(['invoice', 'admin']),
        ]);
    }

    public function edit(Repayment $repayment): View
    {
        return view('lender.repayments.edit', $this->formData($repayment));
    }

    public function update(RepaymentRequest $request, Repayment $repayment): RedirectResponse
    {
        $repayment->update($this->withAdmin($request->validated()));

        return redirect()->route('lender.repayments.index')->with('success', 'Repayment berhasil diperbarui.');
    }

    public function destroy(Repayment $repayment): RedirectResponse
    {
        $repayment->delete();

        return redirect()->route('lender.repayments.index')->with('success', 'Repayment berhasil dihapus.');
    }

    private function formData(Repayment $repayment): array
    {
        return [
            'repayment' => $repayment,
            'invoices' => invoicing_pinjaman::query()->latest()->limit(500)->get(['id', 'no_invoice']),
        ];
    }

    private function withAdmin(array $data): array
    {
        $data['ksp_admin_id'] = auth('lender')->id();

        return $data;
    }
}
