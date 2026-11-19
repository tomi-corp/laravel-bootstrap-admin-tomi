<?php

namespace App\Http\Controllers\Internal;

use App\Exports\Internal\LenderExport;
use App\Imports\Internal\LenderImport;
use App\Models\bank;
use App\Models\lender;
use App\Models\lender_update;
use App\Models\provinsi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LenderController extends \App\Http\Controllers\Admin\LenderController
{
    public function index()
    {
        return view('internal.lenders.index', [
            'banks' => bank::query()->orderBy('code_bank')->get(),
            'provinces' => provinsi::query()->orderBy('provinsi')->get(),
        ]);
    }

    public function exportLender()
    {
        return Excel::download(new LenderExport, 'DATA_LENDER_'.now()->format('Ymd_His').'.xlsx');
    }

    public function importTemplate()
    {
        return Excel::download(new LenderExport(templateOnly: true), 'TEMPLATE_IMPORT_LENDER.xlsx');
    }

    public function importLender(Request $req)
    {
        $req->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ], [
            'file.required' => 'File import wajib dipilih.',
            'file.mimes' => 'File import harus berformat xlsx, xls, atau csv.',
            'file.max' => 'Ukuran file import maksimal 5 MB.',
        ]);

        $import = new LenderImport(auth('admin')->user()->id);
        Excel::import($import, $req->file('file'));

        if ($import->errors()) {
            return response()->json([
                'status' => 422,
                'message' => 'Import dibatalkan, periksa data berikut.',
                'errors' => $import->errors(),
            ], 422);
        }

        if ($import->importedCount() === 0) {
            return response()->json([
                'status' => 422,
                'message' => 'File import tidak berisi data.',
            ], 422);
        }

        return response()->json([
            'status' => 200,
            'message' => number_format($import->importedCount(), 0, ',', '.').' lender berhasil diimport. Lengkapi file PKS dan logo melalui menu ubah.',
        ], 200);
    }

    /**
     * Ajukan permintaan hapus untuk lender yang dipilih. Penghapusan tetap melalui approval seperti hapus satuan.
     */
    public function deleteSelectedLender(Request $req)
    {
        $req->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer',
        ], [
            'ids.required' => 'Pilih minimal satu lender.',
            'ids.min' => 'Pilih minimal satu lender.',
        ]);

        $selectedIds = collect($req->ids)->map(fn ($id) => (int) $id)->unique();
        $adminId = auth('admin')->user()->id;

        $lockedIds = lender_update::query()
            ->whereIn('lender_id', $selectedIds)
            ->where(fn ($query) => $query->where('status_pertama', 'pending')->orWhere('status_kedua', 'pending'))
            ->pluck('lender_id')
            ->unique();

        $lenders = lender::query()
            ->whereIn('id', $selectedIds)
            ->whereNull('deleted_at')
            ->whereNotIn('id', $lockedIds)
            ->get();

        if ($lenders->isEmpty()) {
            return response()->json([
                'status' => 400,
                'message' => 'Tidak ada lender yang bisa diajukan untuk dihapus.',
            ], 400);
        }

        DB::transaction(function () use ($lenders, $adminId) {
            foreach ($lenders as $lender) {
                // Format log disamakan dengan deleteLender() agar alur approval tetap sama.
                $oldData = $lender->getOriginal();
                $lender->deleted_at = now();
                $changedData = $lender->getDirty();

                $lenderUpdate = new lender_update;
                $lenderUpdate->lender_id = $lender->id;
                $lenderUpdate->new_data = json_encode([
                    'old_data' => array_intersect_key($oldData, $changedData),
                    'new_data' => $changedData,
                ]);
                $lenderUpdate->requester_id = $adminId;
                $lenderUpdate->status_pertama = 'pending';
                $lenderUpdate->status_kedua = 'pending';
                $lenderUpdate->save();
            }
        });

        $skipped = $lockedIds->count();
        $message = number_format($lenders->count(), 0, ',', '.').' permintaan hapus lender berhasil dikirim, harap menunggu approval.';
        if ($skipped > 0) {
            $message .= ' '.number_format($skipped, 0, ',', '.').' lender dilewati karena masih pending approval.';
        }

        return response()->json([
            'status' => 200,
            'message' => $message,
        ], 200);
    }
}
