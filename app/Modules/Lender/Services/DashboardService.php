<?php

namespace App\Modules\Lender\Services;

use App\Modules\Lender\Models\Company;
use App\Modules\Lender\Models\Employee;
use App\Modules\Lender\Models\Loan;
use Illuminate\Support\Collection;

class DashboardService
{
    public function summary(int $lenderId): array
    {
        $companies = Company::query()
            ->where('id_lender', $lenderId)
            ->withCount([
                'employees',
                'loans',
            ])
            ->orderBy('nama')
            ->get();

        $companyIds = $companies->pluck('id');

        $loanStatus = Loan::query()
            ->where('id_lender', $lenderId)
            ->selectRaw("COALESCE(status, 'unknown') as status, COUNT(*) as total")
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'total_companies' => $companies->count(),
            'total_employees' => Employee::query()
                ->whereIn('id_list_perusahaan', $companyIds)
                ->count(),
            'total_loans' => Loan::query()
                ->where('id_lender', $lenderId)
                ->count(),
            'pending_loans' => (int) ($loanStatus['pending'] ?? 0),
            'approved_loans' => (int) ($loanStatus['approve'] ?? 0),
            'done_loans' => (int) ($loanStatus['done'] ?? 0),
            'company_chart' => $this->companyChart($companies),
            'loan_status_chart' => $this->loanStatusChart($loanStatus),
            'histories' => $this->histories($lenderId, $companyIds),
        ];
    }

    private function companyChart(Collection $companies): array
    {
        $topCompanies = $companies
            ->sortByDesc(fn (Company $company) => $company->employees_count + $company->loans_count)
            ->take(8)
            ->values();

        return [
            'labels' => $topCompanies->pluck('nama')->values(),
            'employees' => $topCompanies->pluck('employees_count')->values(),
            'loans' => $topCompanies->pluck('loans_count')->values(),
        ];
    }

    private function loanStatusChart(Collection $loanStatus): array
    {
        $labels = [
            'pending' => 'Pending',
            'approve' => 'Approve',
            'done' => 'Done',
        ];

        return collect($labels)
            ->map(fn (string $label, string $status) => [
                'label' => $label,
                'total' => (int) ($loanStatus[$status] ?? 0),
            ])
            ->values()
            ->all();
    }

    private function histories(int $lenderId, Collection $companyIds): Collection
    {
        $employees = Employee::query()
            ->whereIn('id_list_perusahaan', $companyIds)
            ->with('company:id,nama')
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn (Employee $employee) => [
                'type' => 'Pendaftaran',
                'name' => $employee->nama_karyawan,
                'company' => $employee->company?->nama ?? '-',
                'status' => trim(($employee->approve_satu ?? '-') . ' / ' . ($employee->approve_dua ?? '-')),
                'date' => $employee->created_at,
            ]);

        $loans = Loan::query()
            ->where('id_lender', $lenderId)
            ->with(['company:id,nama', 'employee:id,nama_karyawan', 'user:id,name'])
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn (Loan $loan) => [
                'type' => 'Pengajuan',
                'name' => $loan->employee?->nama_karyawan ?? $loan->user?->name ?? $loan->id_pinjaman ?? '-',
                'company' => $loan->company?->nama ?? '-',
                'status' => $loan->status ?? '-',
                'date' => $loan->created_at,
            ]);

        return $employees
            ->concat($loans)
            ->sortByDesc('date')
            ->take(10)
            ->values();
    }
}
