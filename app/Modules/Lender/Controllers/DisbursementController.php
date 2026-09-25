<?php

namespace App\Modules\Lender\Controllers;

use App\Http\Controllers\Controller;
use App\Models\bank;
use App\Models\invoicing_pinjaman;
use App\Models\notification;
use App\Modules\Lender\Models\Disbursement;
use App\Modules\Lender\Models\Loan;
use App\Modules\Lender\Requests\DisbursementRequest;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DisbursementController extends Controller
{
    public function index(): View
    {
        return view('lender.disbursements.index', [
            'loans' => $this->undisbursedLoanQuery()
                ->with(['user', 'company', 'employee.bank', 'kyc'])
                ->get(['id', 'user_id', 'id_perusahaan', 'id_karyawan', 'id_lender', 'id_pinjaman', 'nominal', 'tenor', 'jumlah_yang_diterima', 'document_name', 'id_kyc', 'status']),
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
        DB::transaction(function () use ($request) {
            $loan = Loan::query()
                ->where('id_lender', auth('lender')->user()?->id_lender)
                ->with(['employee.bank', 'company', 'angsuran'])
                ->where('status', 'pending')
                ->where('document_privy', 'y')
                ->findOrFail($request->validated('pinjaman_id'));

            Disbursement::query()->create(
                $this->disbursementData($loan, $request)
            );

            foreach ($loan->angsuran as $jatuhTempo) {
                $period = Carbon::parse($jatuhTempo->jatuh_tempo);

                $invoice = invoicing_pinjaman::query()
                    ->whereYear('period', $period->year)
                    ->whereMonth('period', $period->month)
                    ->where('id_list_perusahaan', $loan->id_perusahaan)
                    ->first();

                if (! $invoice) {
                    $invoice = new invoicing_pinjaman();
                    $invoice->id_list_perusahaan = $loan->id_perusahaan;
                    $invoice->period = $period->toDateString();
                    $invoice->status = 'belum_lunas';
                    $invoice->save();

                    $invoice->no_invoice = 'INV-'.$period->format('M').'-'.($loan->company?->id_code_perusahaan ?? $loan->id_perusahaan).'-'.$invoice->id;
                }

                $invoice->status = 'belum_lunas';
                $invoice->save();
            }

            $loan->status = 'approve';
            $loan->save();

            $notification = new notification();
            $notification->user_id = $loan->user_id;
            $notification->title = 'Pengajuan Anda Telah Diperbarui';
            $notification->description = 'Selamat! Status pengajuan Anda telah berhasil dikirimkan ke rekening anda!';
            $notification->type = 'pinjaman_payroll';
            $notification->read = 'belum';
            $notification->save();
        });

        return redirect()->route('lender.disbursements.index')->with('success', 'Pinjaman berhasil dicairkan.');
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
            'loans' => $loanQuery->get(['id', 'id_lender', 'id_pinjaman', 'nominal', 'document_name', 'id_kyc']),
            'banks' => bank::query()->orderBy('code_bank')->get(['id', 'code_bank', 'description']),
        ];
    }

    private function withAdmin(array $data): array
    {
        $data['ksp_admin_id'] = auth('lender')->id();

        return $data;
    }

    private function disbursementData(Loan $loan, DisbursementRequest $request): array
    {
        $file = $request->file('transfer_proof');
        $fileName = Str::slug($loan->id_pinjaman).'-'.now()->format('YmdHis').'.'.$file->getClientOriginalExtension();

        return [
            'pinjaman_id' => $loan->id,
            'ksp_admin_id' => auth('lender')->id(),
            'bank_id' => $loan->employee?->id_nama_bank,
            'dana_yang_dicairkan' => $loan->jumlah_yang_diterima ?? $loan->nominal,
            'atas_nama' => $loan->employee?->nama_rekening ?? $loan->user?->name ?? '-',
            'nomor_rekening' => $loan->employee?->nomor_bank ?? '-',
            'transfer_date' => $request->validated('transfer_date'),
            'transfer_proof' => $file->storeAs('disbursements/bukti-transfer', $fileName, 'public'),
        ];
    }

    private function undisbursedLoanQuery(?Disbursement $disbursement = null): Builder
    {
        return Loan::query()
            ->where('id_lender', auth('lender')->user()?->id_lender)
            ->where('status', 'pending')
            ->where('document_privy', 'y')
            ->latest();
    }
}
