<?php

namespace App\Http\Controllers\Internal;

use App\Exports\Internal\CompanyExport;
use App\Http\Controllers\Admin\KspController;
use App\Imports\Internal\CompanyImport;
use App\Models\Admin;
use App\Models\CompanyPks;
use App\Models\Feature;
use App\Models\list_karyawan;
use App\Models\ListPerusahaan;
use App\Models\pinjaman;
use App\Models\provinsi;
use App\Services\Insight\InsightService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
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
            'features' => Feature::query()->active()->orderBy('name')->get(['id', 'code', 'name']),
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

    public function pksList(Request $req)
    {
        $company = $this->visibleCompanies()->findOrFail($req->id);

        $items = CompanyPks::query()
            ->where('id_perusahaan', $company->id)
            ->with('feature:id,code,name')
            ->latest('tanggal_pks')
            ->get()
            ->map(fn (CompanyPks $pks) => [
                'id' => $pks->id,
                'fitur' => $pks->feature?->name ?? '-',
                'nomor_pks' => $pks->nomor_pks,
                'tanggal_pks' => $pks->tanggal_pks?->format('d-m-Y'),
                'mulai_berlaku' => $pks->mulai_berlaku?->format('d-m-Y'),
                'berakhir' => $pks->berakhir?->format('d-m-Y'),
                'status' => $pks->berakhir && $pks->berakhir->lt(today()) ? 'BERAKHIR' : 'AKTIF',
                'file_url' => route('internal.companies.pks.file', $pks->id),
            ]);

        return response()->json([
            'status' => 200,
            'data' => [
                'company' => $company->nama,
                'items' => $items,
            ],
        ], 200);
    }

    public function addPks(Request $req)
    {
        $req->validate([
            'id_perusahaan' => 'required|integer',
            'feature_id' => ['required', 'integer', Rule::exists('features', 'id')->where('status', 'active')->whereNull('deleted_at')],
            'nomor_pks' => 'required|string|max:255',
            'tanggal_pks' => 'required|date',
            'mulai_berlaku' => 'required|date',
            'berakhir' => 'required|date|after_or_equal:mulai_berlaku',
            'file_dokumen' => 'required|file|mimes:pdf|max:5120',
        ], [
            'feature_id.required' => 'Fitur wajib dipilih.',
            'feature_id.exists' => 'Fitur tidak ditemukan atau tidak aktif.',
            'nomor_pks.required' => 'Nomor PKS wajib diisi.',
            'tanggal_pks.required' => 'Tanggal PKS wajib diisi.',
            'mulai_berlaku.required' => 'Tanggal mulai berlaku wajib diisi.',
            'berakhir.required' => 'Tanggal berakhir wajib diisi.',
            'berakhir.after_or_equal' => 'Tanggal berakhir tidak boleh sebelum tanggal mulai berlaku.',
            'file_dokumen.required' => 'Dokumen PKS wajib diunggah.',
            'file_dokumen.mimes' => 'Dokumen PKS harus berformat PDF.',
            'file_dokumen.max' => 'Ukuran dokumen PKS maksimal 5 MB.',
        ]);

        $company = $this->visibleCompanies()->findOrFail($req->id_perusahaan);
        $filename = 'PKS_'.$company->id.'_'.now()->format('YmdHis').'_'.Str::random(6).'.pdf';
        $req->file('file_dokumen')->storeAs('pks_perusahaan', $filename, 'local');

        CompanyPks::create([
            'id_perusahaan' => $company->id,
            'feature_id' => $req->feature_id,
            'nomor_pks' => Str::upper(trim($req->nomor_pks)),
            'tanggal_pks' => $req->tanggal_pks,
            'mulai_berlaku' => $req->mulai_berlaku,
            'berakhir' => $req->berakhir,
            'file_dokumen' => $filename,
            'created_by' => auth('admin')->id(),
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'PKS berhasil ditambahkan.',
        ], 200);
    }

    public function deletePks(Request $req)
    {
        $pks = CompanyPks::query()
            ->whereIn('id_perusahaan', $this->visibleCompanies()->select('id'))
            ->findOrFail($req->id);
        $pks->delete();

        return response()->json([
            'status' => 200,
            'message' => 'PKS berhasil dihapus.',
        ], 200);
    }

    public function pksFile(int $id)
    {
        $pks = CompanyPks::query()
            ->whereIn('id_perusahaan', $this->visibleCompanies()->select('id'))
            ->findOrFail($id);
        $path = 'pks_perusahaan/'.$pks->file_dokumen;

        abort_unless(Storage::disk('local')->exists($path), 404, 'Dokumen PKS tidak ditemukan.');

        return Storage::disk('local')->response($path, 'PKS_'.Str::slug($pks->nomor_pks).'.pdf', [
            'Content-Type' => 'application/pdf',
        ], 'inline');
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
