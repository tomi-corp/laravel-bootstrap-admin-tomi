<?php

namespace App\Http\Controllers\Internal;

use App\Exports\Internal\CompanyExport;
use App\Http\Controllers\Admin\KspController;
use App\Http\Requests\Admin\Ksp\AddPerusahaanRequest;
use App\Http\Requests\Admin\Ksp\EditPerusahaanRequest;
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
use Yajra\DataTables\Facades\DataTables;

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
            ->where('company_id', $company->id)
            ->with('feature:id,code,name')
            ->latest('agreement_date')
            ->get()
            ->map(fn (CompanyPks $pks) => [
                'id' => $pks->id,
                'fitur' => $pks->feature?->name ?? '-',
                'nomor_pks' => $pks->agreement_number,
                'tanggal_pks' => $pks->agreement_date?->format('d-m-Y'),
                'mulai_berlaku' => $pks->start_date?->format('d-m-Y'),
                'berakhir' => $pks->end_date?->format('d-m-Y'),
                'status' => $pks->end_date && $pks->end_date->lt(today()) ? 'BERAKHIR' : 'AKTIF',
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

    /**
     * Tambah perusahaan sekaligus PKS-nya (minimal satu, satu per fitur).
     * Data perusahaan tetap disimpan oleh KspController::addPerusahaan().
     */
    public function addPerusahaan(AddPerusahaanRequest $req)
    {
        $this->validatePks($req, required: true);

        return $this->withPks($req, function () use ($req) {
            $response = parent::addPerusahaan($req);
            $company = ListPerusahaan::where('email_perusahaan', $req->email_perusahaan)->latest('id')->firstOrFail();

            return [$response, $company->id];
        });
    }

    /**
     * Ubah perusahaan; baris PKS yang dikirim disimpan sebagai PKS baru (boleh kosong).
     */
    public function editPerusahaan(EditPerusahaanRequest $req)
    {
        $this->visibleCompanies()->findOrFail($req->id);
        $this->validatePks($req, required: false);

        return $this->withPks($req, fn () => [parent::editPerusahaan($req), (int) $req->id]);
    }

    /**
     * Validasi baris PKS di modal perusahaan (field pks[i][...]).
     * Fitur dalam satu kiriman tidak boleh sama; saat tambah perusahaan minimal satu baris.
     */
    private function validatePks(Request $req, bool $required): void
    {
        $req->validate([
            'pks' => [$required ? 'required' : 'nullable', 'array', $required ? 'min:1' : 'min:0'],
            'pks.*.feature_id' => ['required', 'integer', 'distinct', Rule::exists('features', 'id')->where('status', 'active')->whereNull('deleted_at')],
            'pks.*.nomor' => 'required|string|max:255',
            'pks.*.tanggal' => 'required|date',
            'pks.*.mulai_berlaku' => 'required|date',
            'pks.*.berakhir' => 'required|date|after_or_equal:pks.*.mulai_berlaku',
            'pks.*.dokumen' => 'required|file|mimes:pdf|max:5120',
        ], [
            'pks.required' => 'Minimal satu PKS wajib diisi.',
            'pks.min' => 'Minimal satu PKS wajib diisi.',
            'pks.*.feature_id.required' => 'Fitur wajib dipilih.',
            'pks.*.feature_id.distinct' => 'Fitur tidak boleh sama dengan baris PKS lain.',
            'pks.*.feature_id.exists' => 'Fitur tidak ditemukan atau tidak aktif.',
            'pks.*.nomor.required' => 'Nomor PKS wajib diisi.',
            'pks.*.tanggal.required' => 'Tanggal PKS wajib diisi.',
            'pks.*.mulai_berlaku.required' => 'Tanggal mulai berlaku wajib diisi.',
            'pks.*.berakhir.required' => 'Tanggal berakhir wajib diisi.',
            'pks.*.berakhir.after_or_equal' => 'Tanggal berakhir tidak boleh sebelum tanggal mulai berlaku.',
            'pks.*.dokumen.required' => 'Dokumen PKS wajib diunggah.',
            'pks.*.dokumen.mimes' => 'Dokumen PKS harus berformat PDF.',
            'pks.*.dokumen.max' => 'Ukuran dokumen PKS maksimal 5 MB.',
        ]);
    }

    /**
     * Simpan perusahaan dan PKS dalam satu transaksi. Dokumen dihapus lagi jika penyimpanan gagal.
     *
     * @param  callable(): array{0: \Illuminate\Http\JsonResponse, 1: int}  $saveCompany
     */
    private function withPks(Request $req, callable $saveCompany)
    {
        $storedFiles = [];

        try {
            return DB::transaction(function () use ($req, $saveCompany, &$storedFiles) {
                [$response, $companyId] = $saveCompany();

                foreach ((array) $req->input('pks', []) as $index => $row) {
                    $filename = 'PKS_'.$companyId.'_'.now()->format('YmdHis').'_'.Str::random(6).'.pdf';
                    $req->file("pks.{$index}.dokumen")->storeAs('pks_perusahaan', $filename, 'local');
                    $storedFiles[] = $filename;

                    CompanyPks::create([
                        'company_id' => $companyId,
                        'feature_id' => $row['feature_id'],
                        'agreement_number' => Str::upper(trim($row['nomor'])),
                        'agreement_date' => $row['tanggal'],
                        'start_date' => $row['mulai_berlaku'],
                        'end_date' => $row['berakhir'],
                        'document_file' => $filename,
                        'created_by' => auth('admin')->id(),
                    ]);
                }

                return $response;
            });
        } catch (\Throwable $e) {
            foreach ($storedFiles as $filename) {
                Storage::disk('local')->delete('pks_perusahaan/'.$filename);
            }

            throw $e;
        }
    }

    public function deletePks(Request $req)
    {
        $pks = CompanyPks::query()
            ->whereIn('company_id', $this->visibleCompanies()->select('id'))
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
            ->whereIn('company_id', $this->visibleCompanies()->select('id'))
            ->findOrFail($id);
        $path = 'pks_perusahaan/'.$pks->document_file;

        abort_unless(Storage::disk('local')->exists($path), 404, 'Dokumen PKS tidak ditemukan.');

        return Storage::disk('local')->response($path, 'PKS_'.Str::slug($pks->agreement_number).'.pdf', [
            'Content-Type' => 'application/pdf',
        ], 'inline');
    }

    /**
     * Halaman daftar perusahaan per produk (mis. TomiDana): hanya perusahaan yang punya PKS untuk fitur tersebut.
     */
    public function featureIndex(string $featureCode, string $view)
    {
        return view($view, [
            'feature' => Feature::query()->where('code', $featureCode)->firstOrFail(),
        ]);
    }

    public function featureTable(string $featureCode)
    {
        $feature = Feature::query()->where('code', $featureCode)->firstOrFail();
        $pksOfFeature = fn ($query) => $query->where('feature_id', $feature->id);

        $query = $this->visibleCompanies()
            ->select('list_perusahaans.*')
            ->whereHas('pks', $pksOfFeature)
            ->with(['lender:id,nama', 'pks' => fn ($query) => $pksOfFeature($query)->latest('end_date')]);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('lender', fn ($row) => Str::upper($row->lender?->nama ?? '-'))
            ->addColumn('pks_number', fn ($row) => Str::upper($row->pks->first()?->agreement_number ?? '-'))
            ->addColumn('pks_period', function ($row) {
                $pks = $row->pks->first();

                return $pks ? $pks->start_date?->format('d-m-Y').' s/d '.$pks->end_date?->format('d-m-Y') : '-';
            })
            ->addColumn('pks_status', function ($row) {
                $pks = $row->pks->first();

                return $pks && $pks->end_date?->lt(today())
                    ? '<span class="badge badge-secondary">BERAKHIR</span>'
                    : '<span class="badge badge-success">AKTIF</span>';
            })
            ->editColumn('status', fn ($row) => $row->status === 'active'
                ? '<span class="badge badge-success">AKTIF</span>'
                : '<span class="badge badge-warning">TIDAK AKTIF</span>')
            ->editColumn('nama', fn ($row) => Str::upper($row->nama))
            ->editColumn('pic_perusahaan', fn ($row) => Str::upper($row->pic_perusahaan))
            ->editColumn('pic_internal', fn ($row) => Str::upper($row->pic_internal))
            ->editColumn('email_perusahaan', fn ($row) => Str::lower((string) $row->email_perusahaan))
            ->filterColumn('lender', fn ($query, $keyword) => $query->whereHas('lender', fn ($q) => $q->where('nama', 'like', "%{$keyword}%")))
            ->filterColumn('pks_number', fn ($query, $keyword) => $query->whereHas('pks', fn ($q) => $pksOfFeature($q)->where('agreement_number', 'like', "%{$keyword}%")))
            ->rawColumns(['status', 'pks_status'])
            ->make(true);
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
