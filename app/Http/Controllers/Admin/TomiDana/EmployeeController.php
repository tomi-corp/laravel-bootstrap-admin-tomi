<?php

namespace App\Http\Controllers\Admin\TomiDana;

use App\Http\Controllers\Controller;
use App\Imports\TomiDanaEmployeeImport;
use App\Models\list_karyawan;
use App\Models\ListPerusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class EmployeeController extends Controller
{
    public function employees(?int $companyId = null)
    {
        if ($companyId === null) {
            return view('admin.tomi-dana.employees.companies');
        }

        return view('admin.tomi-dana.employees.index', [
            'companyId' => $companyId,
            'company' => $companyId ? ListPerusahaan::find($companyId) : null,
        ]);
    }

    public function employeesTable()
    {
        $query = list_karyawan::with('perusahaan')->latest('id');

        if (request()->filled('company_id')) {
            $query->where('id_list_perusahaan', request()->integer('company_id'));
        }

        $keyword = trim((string) request()->input('search.value', ''));
        if ($keyword !== '') {
            $query->where(function ($searchQuery) use ($keyword) {
                $searchQuery->where('nama_karyawan', 'like', "%{$keyword}%")
                    ->orWhere('nik', 'like', "%{$keyword}%")
                    ->orWhere('nip', 'like', "%{$keyword}%")
                    ->orWhere('whatsapp', 'like', "%{$keyword}%")
                    ->orWhereHas('perusahaan', fn ($q) => $q->where('nama', 'like', "%{$keyword}%"));
            });
        }

        return DataTables::of($query->get())
            ->addIndexColumn()
            ->addColumn('nama_karyawan', fn ($row) => strtoupper($row->nama_karyawan ?? '-'))
            ->addColumn('perusahaan', fn ($row) => strtoupper($row->perusahaan?->nama ?? '-'))
            ->addColumn('status_label', fn ($row) => '<span class="badge badge-secondary">'.e(strtoupper((string) ($row->status ?? '-'))).'</span>')
            ->addColumn('action', fn ($row) => '<button type="button" class="btn btn-sm btn-warning mr-1" onclick="editEmployee('.$row->id.')"><i class="mdi mdi-pencil"></i></button><button type="button" class="btn btn-sm btn-danger" onclick="deleteEmployee('.$row->id.')"><i class="mdi mdi-trash-can"></i></button>')
            ->rawColumns(['status_label', 'action'])
            ->toJson();
    }

    public function employeeCompaniesTable()
    {
        $query = ListPerusahaan::query()
            ->select(['list_perusahaans.id', 'list_perusahaans.nama', 'list_perusahaans.status'])
            ->whereExists(function ($featureQuery) {
                $featureQuery->select(DB::raw(1))
                    ->from('company_features')
                    ->join('features', 'features.id', '=', 'company_features.feature_id')
                    ->whereColumn('company_features.company_id', 'list_perusahaans.id')
                    ->whereRaw("REPLACE(LOWER(features.name), ' ', '') = ?", ['tomidana'])
                    ->whereNull('company_features.deleted_at')
                    ->whereNull('features.deleted_at');
            })
            ->latest('list_perusahaans.id');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('status_label', fn ($row) => '<span class="badge badge-'.($row->status === 'active' ? 'success' : 'secondary').'">'.e(strtoupper((string) ($row->status ?? '-'))).'</span>')
            ->addColumn('action', fn ($row) => '<a href="'.route('admin.tomi-dana.employees.company', $row->id).'" class="btn btn-sm btn-info"><i class="mdi mdi-account-group-outline mr-1"></i> Lihat Karyawan</a>')
            ->rawColumns(['status_label', 'action'])
            ->toJson();
    }

    public function storeEmployee(Request $request)
    {
        $data = $this->validateEmployee($request, true);
        $employee = new list_karyawan;
        $employee->fill($data);
        $employee->save();

        return response()->json(['status' => 200, 'message' => 'Data karyawan berhasil ditambahkan.']);
    }

    public function employeeDetail(int $id)
    {
        return response()->json(['status' => 200, 'data' => list_karyawan::findOrFail($id)]);
    }

    public function updateEmployee(Request $request)
    {
        $employee = list_karyawan::findOrFail($request->integer('id'));
        $data = $this->validateEmployee($request, false);
        $employee->fill($data);
        $employee->save();

        return response()->json(['status' => 200, 'message' => 'Data karyawan berhasil diubah.']);
    }

    public function deleteEmployee(Request $request)
    {
        $request->validate(['id' => 'required|integer|exists:list_karyawans,id']);
        list_karyawan::findOrFail($request->integer('id'))->delete();

        return response()->json(['status' => 200, 'message' => 'Data karyawan berhasil dihapus.']);
    }

    public function importEmployees(Request $request)
    {
        $data = $request->validate([
            'id_list_perusahaan' => 'required|integer|exists:list_perusahaans,id',
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        Excel::import(new TomiDanaEmployeeImport((int) $data['id_list_perusahaan']), $request->file('file'));

        return response()->json(['status' => 200, 'message' => 'Data karyawan berhasil diimpor.']);
    }

    public function employeeTemplate()
    {
        return Storage::disk('local')->download(
            'template/template-import-karyawan.xlsx',
            'template-import-karyawan.xlsx'
        );
    }

    private function validateEmployee(Request $request, bool $includeCompany = true): array
    {
        $rules = [
            'nama_karyawan' => 'required|string|max:255',
            'tanggal_bergabung' => 'nullable|date',
            'nik' => 'nullable|string|max:100',
            'nip' => 'nullable|string|max:100',
            'whatsapp' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'divisi' => 'nullable|string|max:255',
            'departement' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:50',
            'status_sp' => 'nullable|string|max:50',
            'nomor_bank' => 'nullable|string|max:100',
            'max_plafon' => 'nullable|numeric',
        ];

        if ($includeCompany) {
            $rules['id_list_perusahaan'] = 'required|integer|exists:list_perusahaans,id';
        }

        return $request->validate($rules);
    }
}

