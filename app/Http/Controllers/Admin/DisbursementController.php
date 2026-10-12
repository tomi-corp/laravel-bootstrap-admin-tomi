<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DisbursementRequest;
use App\Models\bank;
use App\Models\disbursement;
use App\Models\pinjaman;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DisbursementController extends Controller
{
    public function index(): View
    {
        return view('admin.disbursements.index', [
            'disbursements' => disbursement::query()
                ->with(['pinjaman.user', 'pinjaman.perusahaan', 'admin', 'bank'])
                ->latest()
                ->get(),
            'pinjamanList' => pinjaman::query()
                ->with(['user', 'perusahaan'])
                ->latest()
                ->limit(1000)
                ->get(['id', 'id_pinjaman', 'user_id', 'id_perusahaan', 'nominal', 'jumlah_yang_diterima']),
            'banks' => bank::query()
                ->orderBy('code_bank')
                ->get(['id', 'code_bank', 'description']),
        ]);
    }

    public function store(DisbursementRequest $request): JsonResponse
    {
        $disbursement = disbursement::query()->create($this->validatedData($request));

        return response()->json([
            'message' => 'Pencairan dana berhasil ditambahkan.',
            'data' => ['id' => $disbursement->id],
        ]);
    }

    public function show(disbursement $disbursement): JsonResponse
    {
        $disbursement->load(['pinjaman.user', 'pinjaman.perusahaan', 'admin', 'bank']);

        return response()->json([
            'data' => [
                'id' => $disbursement->id,
                'pinjaman_id' => $disbursement->pinjaman_id,
                'ksp_admin_id' => $disbursement->ksp_admin_id,
                'bank_id' => $disbursement->bank_id,
                'dana_yang_dicairkan' => $disbursement->dana_yang_dicairkan,
                'atas_nama' => $disbursement->atas_nama,
                'nomor_rekening' => $disbursement->nomor_rekening,
                'transfer_date' => optional($disbursement->transfer_date)->format('Y-m-d'),
                'transfer_proof' => $disbursement->transfer_proof,
                'transfer_proof_url' => $disbursement->transfer_proof ? Storage::url($disbursement->transfer_proof) : null,
                'pinjaman' => $disbursement->pinjaman,
                'admin' => $disbursement->admin,
                'bank' => $disbursement->bank,
            ],
        ]);
    }

    public function update(DisbursementRequest $request, disbursement $disbursement): JsonResponse
    {
        $disbursement->update($this->validatedData($request, $disbursement));

        return response()->json([
            'message' => 'Pencairan dana berhasil diperbarui.',
            'data' => ['id' => $disbursement->id],
        ]);
    }

    public function destroy(disbursement $disbursement): JsonResponse
    {
        $disbursement->delete();

        return response()->json([
            'message' => 'Pencairan dana berhasil dihapus.',
        ]);
    }

    private function validatedData(DisbursementRequest $request, ?disbursement $disbursement = null): array
    {
        $data = $request->validated();
        $data['ksp_admin_id'] = auth('admin')->id();

        if ($request->hasFile('transfer_proof')) {
            if ($disbursement?->transfer_proof) {
                Storage::disk('public')->delete($disbursement->transfer_proof);
            }

            $loan = pinjaman::query()->find($data['pinjaman_id']);
            $baseName = Str::slug($loan?->id_pinjaman ?: 'disbursement');
            $file = $request->file('transfer_proof');
            $fileName = $baseName.'-'.now()->format('YmdHis').'.'.$file->getClientOriginalExtension();
            $data['transfer_proof'] = $file->storeAs('disbursements/bukti-transfer', $fileName, 'public');
        } else {
            unset($data['transfer_proof']);
        }

        return $data;
    }
}
