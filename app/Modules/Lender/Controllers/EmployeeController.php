<?php

namespace App\Modules\Lender\Controllers;

use App\Exports\KaryawanTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\KaryawanPendingImport;
use App\Models\amount_pinjaman;
use App\Models\bank;
use App\Modules\Lender\Models\Company;
use App\Modules\Lender\Models\Employee;
use App\Modules\Lender\Requests\EmployeeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EmployeeController extends Controller
{
    public function index(): View
    {
        return view('lender.employees.index', [
            'employees' => Employee::query()
                ->with(['company:id,nama,id_lender', 'bank:id,code_bank'])
                ->whereHas('company', fn ($query) => $query->where('id_lender', $this->lenderId()))
                ->orderBy('id_list_perusahaan')
                ->orderBy('nama_karyawan')
                ->get(),
            'companies' => $this->companies(),
        ]);
    }

    public function create(): View
    {
        return view('lender.employees.create', [
            'employee' => new Employee([
                'status' => 'tetap',
                'status_sp' => '0',
                'approve_satu' => 'approve',
                'approve_dua' => 'approve',
            ]),
            'companies' => $this->companies(),
            'banks' => $this->banks(),
        ]);
    }

    public function store(EmployeeRequest $request): RedirectResponse
    {
        Employee::query()->create($request->validated());

        return redirect()
            ->route('lender.employees.index')
            ->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function show(Employee $employee): View
    {
        $this->authorizeEmployee($employee);

        return view('lender.employees.show', [
            'employee' => $employee->load(['company:id,nama,id_lender', 'bank:id,code_bank']),
        ]);
    }

    public function edit(Employee $employee): View
    {
        $this->authorizeEmployee($employee);

        return view('lender.employees.edit', [
            'employee' => $employee,
            'companies' => $this->companies(),
            'banks' => $this->banks(),
        ]);
    }

    public function update(EmployeeRequest $request, Employee $employee): RedirectResponse
    {
        $this->authorizeEmployee($employee);
        $employee->update($request->validated());

        return redirect()
            ->route('lender.employees.index')
            ->with('success', 'Karyawan berhasil diperbarui.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $this->authorizeEmployee($employee);
        $employee->delete();

        return redirect()
            ->route('lender.employees.index')
            ->with('success', 'Karyawan berhasil dihapus.');
    }

    public function downloadTemplate(Request $request): BinaryFileResponse
    {
        $company = $this->companyFromRequest($request);

        return Excel::download(
            new KaryawanTemplateExport($company?->id_code_perusahaan),
            'template-import-karyawan.xlsx'
        );
    }

    public function import(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'id_perusahaan' => ['nullable', 'integer'],
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        $company = $this->companyFromRequest($request);

        try {
            DB::beginTransaction();

            $import = new KaryawanPendingImport($company?->id, $this->lenderId());
            Excel::import($import, $request->file('file'));

            if (! empty($import->skippedRows)) {
                DB::rollBack();

                $messages = collect($import->skippedRows)
                    ->map(function ($row) {
                        $rowNumber = $row['row'] ?? '-';
                        $nik = $row['nik'] ?? '-';
                        $reason = $row['reason'] ?? 'Data tidak valid';

                        return "Baris {$rowNumber} (NIK: {$nik}): {$reason}";
                    })
                    ->take(10)
                    ->implode('<br>');

                return back()->withInput()->with('error', $messages);
            }

            if ($import->savedRows === 0) {
                DB::rollBack();

                return back()->withInput()->with('error', 'Tidak ada data karyawan yang tersimpan. Cek isi file Excel.');
            }

            foreach ($import->processedPerusahaanIds as $idPerusahaan) {
                $this->syncCompanyLoanMaximum((int) $idPerusahaan);
            }

            DB::commit();

            return redirect()
                ->route('lender.employees.index')
                ->with('success', 'Berhasil upload '.$import->savedRows.' data karyawan dan update maximal pinjaman.');
        } catch (\Throwable $th) {
            DB::rollBack();

            Log::error('Lender karyawan upload failed', [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Gagal upload data karyawan. Periksa format Excel dan pastikan setiap kolom sesuai template.');
        }
    }

    private function companies()
    {
        return Company::query()
            ->where('id_lender', $this->lenderId())
            ->orderBy('nama')
            ->get(['id', 'nama', 'id_lender']);
    }

    private function banks()
    {
        return bank::query()
            ->orderBy('code_bank')
            ->get(['id', 'code_bank']);
    }

    private function authorizeEmployee(Employee $employee): void
    {
        abort_unless(
            $employee->company()->where('id_lender', $this->lenderId())->exists(),
            404
        );
    }

    private function lenderId(): int
    {
        return (int) auth('lender')->user()->id_lender;
    }

    private function companyFromRequest(Request $request): ?Company
    {
        if (! $request->filled('id_perusahaan')) {
            return null;
        }

        return Company::query()
            ->where('id_lender', $this->lenderId())
            ->whereKey($request->integer('id_perusahaan'))
            ->firstOrFail();
    }

    private function syncCompanyLoanMaximum(int $companyId): void
    {
        $maxPlafon = Employee::query()
            ->where('id_list_perusahaan', $companyId)
            ->pluck('max_plafon')
            ->map(fn ($value) => $this->normalizePlafon($value))
            ->max() ?? 0;

        amount_pinjaman::query()
            ->where('id_perusahaan', $companyId)
            ->update([
                'maximal' => $maxPlafon,
                'updated_at' => now(),
            ]);
    }

    private function normalizePlafon($value): int
    {
        if ($value === null || $value === '') {
            return 0;
        }

        $value = trim((string) $value);
        $value = str_replace(['Rp', 'rp', 'IDR', 'idr', ' '], '', $value);

        if (preg_match('/^\d+\.\d+$/', $value)) {
            return (int) floor((float) $value);
        }

        if (str_contains($value, ',')) {
            $value = explode(',', $value)[0];
        }

        $value = str_replace('.', '', $value);

        return (int) preg_replace('/[^0-9]/', '', $value);
    }
}
