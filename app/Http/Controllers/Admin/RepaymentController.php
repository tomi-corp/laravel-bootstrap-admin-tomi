<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RepaymentRequest;
use App\Models\invoicing_pinjaman;
use App\Models\repayment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RepaymentController extends Controller
{
    public function index(): View
    {
        return view('admin.repayments.index', [
            'repayments' => repayment::query()
                ->with(['invoice.perusahaan', 'admin'])
                ->latest()
                ->get(),
            'invoices' => invoicing_pinjaman::query()
                ->with('perusahaan')
                ->latest()
                ->limit(1000)
                ->get(['id', 'no_invoice', 'period', 'status', 'id_list_perusahaan']),
        ]);
    }

    public function store(RepaymentRequest $request): JsonResponse
    {
        $repayment = repayment::query()->create($this->validatedData($request));

        return response()->json([
            'message' => 'Pembayaran berhasil ditambahkan.',
            'data' => ['id' => $repayment->id],
        ]);
    }

    public function show(repayment $repayment): JsonResponse
    {
        $repayment->load(['invoice.perusahaan', 'admin']);

        return response()->json([
            'data' => [
                'id' => $repayment->id,
                'id_invoicing' => $repayment->id_invoicing,
                'ksp_admin_id' => $repayment->ksp_admin_id,
                'amount_total' => $repayment->amount_total,
                'tanggal_pembayaran' => optional($repayment->tanggal_pembayaran)->format('Y-m-d\TH:i'),
                'payment_proof_date' => optional($repayment->payment_proof_date)->format('Y-m-d'),
                'payment_proof_file' => $repayment->payment_proof_file,
                'payment_proof_url' => $repayment->payment_proof_file ? Storage::url($repayment->payment_proof_file) : null,
                'invoice' => $repayment->invoice,
                'admin' => $repayment->admin,
            ],
        ]);
    }

    public function update(RepaymentRequest $request, repayment $repayment): JsonResponse
    {
        $repayment->update($this->validatedData($request, $repayment));

        return response()->json([
            'message' => 'Pembayaran berhasil diperbarui.',
            'data' => ['id' => $repayment->id],
        ]);
    }

    public function destroy(repayment $repayment): JsonResponse
    {
        $repayment->delete();

        return response()->json([
            'message' => 'Pembayaran berhasil dihapus.',
        ]);
    }

    private function validatedData(RepaymentRequest $request, ?repayment $repayment = null): array
    {
        $data = $request->validated();
        $data['ksp_admin_id'] = auth('admin')->id();

        if ($request->hasFile('payment_proof_file')) {
            if ($repayment?->payment_proof_file) {
                Storage::disk('public')->delete($repayment->payment_proof_file);
            }

            $invoice = invoicing_pinjaman::query()->find($data['id_invoicing']);
            $baseName = Str::slug($invoice?->no_invoice ?: 'repayment-'.$data['id_invoicing']);
            $file = $request->file('payment_proof_file');
            $fileName = $baseName.'-'.now()->format('YmdHis').'.'.$file->getClientOriginalExtension();
            $data['payment_proof_file'] = $file->storeAs('repayments/bukti-pembayaran', $fileName, 'public');
        } else {
            unset($data['payment_proof_file']);
        }

        return $data;
    }
}
