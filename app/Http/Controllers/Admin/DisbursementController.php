<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DisbursementRequest;
use App\Models\disbursement;
use App\Models\invoicing_pinjaman;
use App\Models\notification;
use App\Models\pinjaman;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DisbursementController extends Controller
{
    public function index(): View
    {
        return view('admin.disbursements.index', [
            'loans' => pinjaman::query()
                ->where('status', 'pending')
                ->where('document_privy', 'y')
                ->with(['user', 'perusahaan', 'data_karyawan.bank', 'disbursement'])
                ->latest()
                ->get(),
        ]);
    }

    public function store(DisbursementRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $loan = pinjaman::query()
                ->where('status', 'pending')
                ->where('document_privy', 'y')
                ->with(['data_karyawan.bank', 'perusahaan', 'angsuran', 'user'])
                ->findOrFail($request->validated('pinjaman_id'));

            disbursement::query()->create($this->disbursementData($loan, $request));

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

                    $invoice->no_invoice = 'INV-'.$period->format('M').'-'.($loan->perusahaan?->id_code_perusahaan ?? $loan->id_perusahaan).'-'.$invoice->id;
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

        return redirect()->route('admin.disbursements.index')->with('success', 'Pinjaman berhasil dicairkan.');
    }

    private function disbursementData(pinjaman $loan, DisbursementRequest $request): array
    {
        $file = $request->file('transfer_proof');
        $fileName = Str::slug($loan->id_pinjaman ?: 'disbursement').'-'.now()->format('YmdHis').'.'.$file->getClientOriginalExtension();

        return [
            'pinjaman_id' => $loan->id,
            'ksp_admin_id' => auth('admin')->id(),
            'bank_id' => $loan->data_karyawan?->id_nama_bank,
            'dana_yang_dicairkan' => $loan->jumlah_yang_diterima ?? $loan->nominal,
            'atas_nama' => $loan->data_karyawan?->nama_rekening ?? $loan->user?->name ?? '-',
            'nomor_rekening' => $loan->data_karyawan?->nomor_bank ?? '-',
            'transfer_date' => $request->validated('transfer_date'),
            'transfer_proof' => $file->storeAs('disbursements/bukti-transfer', $fileName, 'public'),
        ];
    }
}
