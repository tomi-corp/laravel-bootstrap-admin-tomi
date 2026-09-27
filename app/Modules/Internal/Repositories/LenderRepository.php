<?php

namespace App\Modules\Internal\Repositories;

use App\Models\bank;
use App\Models\lender;
use App\Models\provinsi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class LenderRepository
{
    public function countActive(): int
    {
        return lender::where('status', 'active')->count();
    }

    public function countInactive(): int
    {
        return lender::where('status', 'nonactive')->count();
    }

    public function provinces(): Collection
    {
        return provinsi::orderBy('provinsi')->get(['id', 'provinsi']);
    }

    public function banks(): Collection
    {
        return bank::orderBy('code_bank')->get(['id', 'code_bank']);
    }

    public function tableQuery(): Builder
    {
        return lender::query()
            ->with(['provinsi', 'kabupaten', 'kecamatan', 'desa', 'bank'])
            ->select('lenders.*');
    }

    public function create(array $data): lender
    {
        return lender::create($data);
    }

    public function update(lender $lender, array $data): bool
    {
        return $lender->update($data);
    }

    public function delete(lender $lender): ?bool
    {
        return $lender->delete();
    }

    public function lenderCodeExists(string $code): bool
    {
        return lender::where('id_code_lender', $code)->exists();
    }
}
