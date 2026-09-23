<?php

namespace App\Modules\Lender\Controllers;

use App\Http\Controllers\Controller;
use App\Models\bank;
use App\Modules\Lender\Models\Disbursement;
use App\Modules\Lender\Models\Loan;
use App\Modules\Lender\Requests\DisbursementRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DisbursementController extends Controller
{
    public function index(): View
    {
        return view('lender.disbursements.index', [
            'loans' => $this->undisbursedLoanQuery()
                ->with(['user', 'company', 'employee.bank', 'kyc'])
                ->get(['id', 'user_id', 'id_perusahaan', 'id_karyawan', 'id_pinjaman', 'nominal', 'tenor', 'jumlah_yang_diterima', 'document_name', 'id_kyc', 'status']),
        ]);
    }

    public function create(Request $request): View
    {
        $disbursement = new Disbursement;
        $disbursement->pinjaman_id = $request->integer('pinjaman_id') ?: null;

        return view('lender.disbursements.create', $this->formData($disbursement));
    }

    public function store(DisbursementRequest $request): RedirectResponse
    {
        Disbursement::query()->create($this->withSystemData($this->withAdmin($request->validated()), $request));

        return redirect()->route('lender.disbursements.index')->with('success', 'Disbursement berhasil ditambahkan.');
    }

    public function show(Disbursement $disbursement): View
    {
        return view('lender.disbursements.show', [
            'disbursement' => $disbursement->load(['loan.kyc', 'admin', 'bank']),
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
        $disbursement->loadMissing('loan.kyc');

        $loanQuery = $this->undisbursedLoanQuery($disbursement)
            ->with('kyc')
            ->latest()
            ->limit(500);

        return [
            'disbursement' => $disbursement,
            'loans' => $loanQuery->get(['id', 'id_pinjaman', 'nominal', 'document_name', 'id_kyc']),
            'banks' => bank::query()->orderBy('code_bank')->get(['id', 'code_bank', 'description']),
        ];
    }

    private function withAdmin(array $data): array
    {
        $data['ksp_admin_id'] = auth('lender')->id();

        return $data;
    }

    private function withSystemData(array $data, DisbursementRequest $request): array
    {
        $loan = Loan::query()->with('employee')->findOrFail($data['pinjaman_id']);

        $data['bank_id'] = $loan->employee?->id_nama_bank;
        $data['dana_yang_dicairkan'] = $loan->jumlah_yang_diterima ?? $loan->nominal;
        $data['atas_nama'] = $loan->employee?->nama_rekening ?? $loan->user?->name ?? '-';
        $data['nomor_rekening'] = $loan->employee?->nomor_bank ?? '-';

        if ($request->hasFile('transfer_proof')) {
            $file = $request->file('transfer_proof');
            $fileName = Str::slug($loan->id_pinjaman).'-'.now()->format('YmdHis').'.'.$file->getClientOriginalExtension();
            $data['transfer_proof'] = $file->storeAs('assets/disbursements', $fileName, 'local');
        }

        return $data;
    }

    private function undisbursedLoanQuery(?Disbursement $disbursement = null): Builder
    {
        return Loan::query()
            ->whereNotIn('id', Disbursement::query()
                ->when($disbursement?->exists, fn ($query) => $query->whereKeyNot($disbursement->id))
                ->select('pinjaman_id')
            )
            ->latest();
    }
}
