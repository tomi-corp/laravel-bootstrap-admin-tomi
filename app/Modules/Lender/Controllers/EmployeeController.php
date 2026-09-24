<?php

namespace App\Modules\Lender\Controllers;

use App\Http\Controllers\Controller;
use App\Models\bank;
use App\Modules\Lender\Models\Company;
use App\Modules\Lender\Models\Employee;
use App\Modules\Lender\Requests\EmployeeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

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
}
