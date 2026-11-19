<?php

namespace App\Http\Controllers\Internal\TomiDana;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Internal\Concerns\ScopesCompanies;
use App\Http\Requests\Admin\Ksp\UpdateKaryawanRequest;
use App\Http\Requests\Admin\Ksp\UploadListKaryawanRequest;
use App\Http\Resources\Admin\Bank\BankResource;
use App\Http\Resources\Admin\Ksp\ListKaryawanPendingResource;
use App\Imports\Internal\EmployeeImport;
use App\Models\bank;
use App\Models\list_karyawan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

/**
 * List karyawan perusahaan TomiDana: karyawan aktif, upload, dan persetujuan karyawan pending (2 layer).
 */
class EmployeeController extends Controller
{
    use ScopesCompanies;

    public function index(int $company)
    {
        $perusahaan = $this->findVisibleCompany($company)->load(['provinsi', 'kabupaten', 'kecamatan', 'desa']);
        $banks = BankResource::collection(bank::get());

        return view('internal.tomi-dana.employees.index', compact('perusahaan', 'banks'));
    }

    public function table(int $company)
    {
        $company = $this->findVisibleCompany($company);
        $employees = list_karyawan::where('id_list_perusahaan', $company->id)
            ->where('approve_satu', 'approve')
            ->where('approve_dua', 'approve')
            ->with('bank')
            ->get();

        return DataTables::of($employees)
            ->addIndexColumn()
            ->editColumn('nama_karyawan', fn ($row) => Str::upper($row->nama_karyawan))
            ->addColumn('max_plafon', fn ($row) => 'Rp. '.number_format((float) $row->max_plafon, 0, ',', '.'))
            ->addColumn('bank', fn ($row) => $row->bank?->code_bank ?? '-')
            ->addColumn('action', fn ($row) => '<button class="btn btn-sm btn-primary mr-2" onclick="edit('.$row->id.')"><i class="mdi mdi-pencil"></i></button>')
            ->rawColumns(['action'])
            ->toJson();
    }

    public function upload(UploadListKaryawanRequest $req, int $company)
    {
        $company = $this->findVisibleCompany($company);
        $import = new EmployeeImport($company);

        try {
            Excel::import($import, $req->file('file'));
        } catch (\Throwable $e) {
            Log::warning('Upload karyawan internal gagal', ['company_id' => $company->id, 'message' => $e->getMessage()]);

            return response()->json([
                'status' => 400,
                'message' => 'Upload gagal. Periksa duplikasi NIK/NIP, kode bank, dan format file.',
            ], 400);
        }

        $message = number_format($import->importedCount(), 0, ',', '.').' karyawan berhasil diupload dan menunggu persetujuan.';
        if ($import->skippedCount() > 0) {
            $message .= ' '.number_format($import->skippedCount(), 0, ',', '.').' baris dilewati karena code_perusahaan bukan '.$company->id_code_perusahaan.'.';
        }

        return response()->json([
            'status' => 200,
            'message' => $message,
        ], 200);
    }

    public function pendingCount(Request $req)
    {
        $company = $this->findVisibleCompany($req->id);

        return response()->json([
            'status' => 200,
            'pending' => $this->pendingQuery($company->id)->count(),
        ], 200);
    }

    public function pending(Request $req)
    {
        $company = $this->findVisibleCompany($req->id);

        return response()->json([
            'status' => 200,
            'data' => ListKaryawanPendingResource::collection($this->pendingQuery($company->id)->get()),
        ], 200);
    }

    /**
     * Admin menyetujui layer 1, master menyetujui layer 2.
     */
    public function approve(Request $req)
    {
        $company = $this->findVisibleCompany($req->id);
        $column = $this->approvalColumn();
        abort_unless($column, 403, 'Akun ini tidak berhak menyetujui karyawan.');

        list_karyawan::where('id_list_perusahaan', $company->id)->where($column, 'pending')->update([$column => 'approve']);

        return response()->json([
            'status' => 200,
            'message' => 'List karyawan berhasil disetujui.',
        ], 200);
    }

    public function show(Request $req)
    {
        $employee = $this->findApprovedEmployee($req->id);

        return response()->json([
            'status' => 200,
            'data' => [
                'nama' => $employee->nama_karyawan,
                'tanggal_bergabung' => $employee->tanggal_bergabung,
                'nik' => $employee->nik,
                'nip' => $employee->nip,
                'whatsapp' => $employee->whatsapp,
                'alamat' => $employee->alamat,
                'divisi' => $employee->divisi,
                'departement' => $employee->departement,
                'jabatan' => $employee->jabatan,
                'status' => $employee->status,
                'status_sp' => $employee->status_sp,
                'bank' => $employee->id_nama_bank,
                'nomor_bank' => $employee->nomor_bank,
                'max_plafon' => $employee->max_plafon,
            ],
        ], 200);
    }

    public function update(UpdateKaryawanRequest $req)
    {
        $employee = $this->findApprovedEmployee($req->id);

        $employee->nama_karyawan = $req->nama_karyawan;
        $employee->tanggal_bergabung = $req->tanggal_bergabung;
        $employee->nik = $req->nik;
        $employee->nip = $req->nip;
        $employee->whatsapp = $req->whatsapp;
        $employee->alamat = $req->alamat_lengkap;
        $employee->divisi = $req->divisi;
        $employee->departement = $req->departement;
        $employee->jabatan = $req->jabatan;
        $employee->status = $req->status;
        $employee->status_sp = $req->statusSp;
        $employee->id_nama_bank = $req->bank;
        $employee->nomor_bank = $req->nomor_bank;
        $employee->max_plafon = $req->max_plafon;
        $employee->save();

        return response()->json([
            'status' => 200,
            'message' => 'Data karyawan berhasil diperbarui.',
        ], 200);
    }

    /**
     * Kolom persetujuan sesuai tipe akun: admin = layer 1, master = layer 2.
     */
    private function approvalColumn(): ?string
    {
        return match (auth('admin')->user()?->type) {
            'admin' => 'approve_satu',
            'master' => 'approve_dua',
            default => null,
        };
    }

    private function pendingQuery(int $companyId): Builder
    {
        $query = list_karyawan::where('id_list_perusahaan', $companyId);
        $column = $this->approvalColumn();

        // Akun selain admin/master hanya melihat (tanpa hak approve) semua karyawan yang masih pending di salah satu layer.
        return $column
            ? $query->where($column, 'pending')
            : $query->where(fn ($q) => $q->where('approve_satu', 'pending')->orWhere('approve_dua', 'pending'));
    }

    private function findApprovedEmployee(int|string|null $id): list_karyawan
    {
        $employee = list_karyawan::where('approve_satu', 'approve')
            ->where('approve_dua', 'approve')
            ->findOrFail((int) $id);
        $this->authorizeCompanyId($employee->id_list_perusahaan);

        return $employee;
    }
}
