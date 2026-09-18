<?php

namespace App\Modules\Internal\Repositories;

use App\Models\lender;
use App\Models\ListPerusahaan;
use App\Models\provinsi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class CompanyRepository
{
    public function countActive(): int
    {
        return ListPerusahaan::where('status', 'active')->count();
    }

    public function countInactive(): int
    {
        return ListPerusahaan::where('status', 'nonactive')->count();
    }

    public function lenders(): Collection
    {
        return lender::orderBy('nama')->get(['id', 'nama']);
    }

    public function provinces(): Collection
    {
        return provinsi::orderBy('provinsi')->get(['id', 'provinsi']);
    }

    public function tableQuery(): Builder
    {
        return ListPerusahaan::query()
            ->with(['lender', 'provinsi', 'kabupaten', 'kecamatan', 'desa'])
            ->select('list_perusahaans.*');
    }

    public function create(array $data): ListPerusahaan
    {
        return ListPerusahaan::create($data);
    }

    public function update(ListPerusahaan $company, array $data): bool
    {
        return $company->update($data);
    }

    public function delete(ListPerusahaan $company): ?bool
    {
        return $company->delete();
    }

    public function idCodeExists(string $code): bool
    {
        return ListPerusahaan::where('id_code_perusahaan', $code)->exists();
    }

    public function uniqueCodeExists(string $code): bool
    {
        return ListPerusahaan::where('unique_code', $code)->exists();
    }
}
