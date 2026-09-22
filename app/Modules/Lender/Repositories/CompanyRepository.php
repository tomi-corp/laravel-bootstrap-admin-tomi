<?php

namespace App\Modules\Lender\Repositories;

use App\Models\lender;
use App\Models\provinsi;
use App\Modules\Lender\Models\Company;
use Illuminate\Database\Eloquent\Collection;

class CompanyRepository
{
    public function all(): Collection
    {
        return Company::query()
            ->with(['lender', 'provinsi', 'kabupaten'])
            ->latest()
            ->get();
    }

    public function lenders(): Collection
    {
        return lender::query()->orderBy('nama')->get(['id', 'nama']);
    }

    public function create(array $data): Company
    {
        return Company::create($data);
    }

    public function update(Company $company, array $data): bool
    {
        return $company->update($data);
    }

    public function delete(Company $company): ?bool
    {
        return $company->delete();
    }

    public function idCodeExists(string $code): bool
    {
        return Company::query()->where('id_code_perusahaan', $code)->exists();
    }

    public function uniqueCodeExists(string $code): bool
    {
        return Company::query()->where('unique_code', $code)->exists();
    }

    public function provinces(): Collection
    {
        return provinsi::query()->orderBy('provinsi')->get(['id', 'provinsi']);
    }
}
