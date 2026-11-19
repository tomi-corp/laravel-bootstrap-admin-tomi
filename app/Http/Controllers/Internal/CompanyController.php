<?php

namespace App\Http\Controllers\Internal;

use App\Exports\Internal\CompanyExport;
use App\Http\Controllers\Admin\KspController;
use App\Imports\Internal\CompanyImport;
use App\Models\Admin;
use App\Models\list_karyawan;
use App\Models\ListPerusahaan;
use App\Models\pinjaman;
use App\Models\provinsi;
use App\Services\Insight\InsightService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class CompanyController extends KspController
{
    public function index(InsightService $insight)
    {
        return view('internal.companies.index', [
            'provinces' => provinsi::query()->orderBy('provinsi')->get(),
            'internalAdmins' => Admin::query()
                ->whereHas('role.instansi', fn ($query) => $query->where('code', 'internal'))
                ->whereHas('role', fn ($query) => $query->whereIn('code', ['admin', 'super_admin']))
                ->orderBy('username')
                ->get(['id', 'username', 'email']),
        ]);
    }

    public function exportCompany()
    {
        return Excel::download(new CompanyExport(query: $this->visibleCompanies()), 'DATA_PERUSAHAAN_'.now()->format('Ymd_His').'.xlsx');
    }

    public function importTemplate()
    {
        return Excel::download(new CompanyExport(templateOnly: true), 'TEMPLATE_IMPORT_PERUSAHAAN.xlsx');
    }

    public function importCompany(Request $req)
    {
        $req->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ], [
            'file.required' => 'File import wajib dipilih.',
            'file.mimes' => 'File import harus berformat xlsx, xls, atau csv.',
            'file.max' => 'Ukuran file import maksimal 5 MB.',
        ]);

        $import = new CompanyImport;
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
            'message' => number_format($import->importedCount(), 0, ',', '.').' perusahaan berhasil diimport.',
        ], 200);
    }

    /**
     * Hapus perusahaan terpilih beserta data karyawan dan pinjamannya, sama seperti deletePerusahaan().
     */
    public function deleteSelectedCompany(Request $req)
    {
        $req->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer',
        ], [
            'ids.required' => 'Pilih minimal satu perusahaan.',
            'ids.min' => 'Pilih minimal satu perusahaan.',
        ]);

        $ids = $this->visibleCompanies()
            ->whereIn('id', collect($req->ids)->map(fn ($id) => (int) $id)->unique())
            ->pluck('id');

        if ($ids->isEmpty()) {
            return response()->json([
                'status' => 400,
                'message' => 'Tidak ada perusahaan yang bisa dihapus.',
            ], 400);
        }

        DB::transaction(function () use ($ids) {
            pinjaman::whereIn('id_perusahaan', $ids)->delete();
            list_karyawan::whereIn('id_list_perusahaan', $ids)->delete();
            ListPerusahaan::whereIn('id', $ids)->delete();
        });

        return response()->json([
            'status' => 200,
            'message' => number_format($ids->count(), 0, ',', '.').' perusahaan beserta data karyawan dan pinjaman berhasil dihapus.',
        ], 200);
    }

    /**
     * Perusahaan yang boleh dilihat admin, mengikuti filter PIC di listPerusahaanTable().
     */
    private function visibleCompanies(): Builder
    {
        $admin = auth()->guard('admin')->user();
        $query = ListPerusahaan::query();

        if ($admin && $admin->type === 'pic') {
            empty($admin->company_id)
                ? $query->whereRaw('1 = 0')
                : $query->where('id', $admin->company_id);
        }

        return $query;
    }
}
