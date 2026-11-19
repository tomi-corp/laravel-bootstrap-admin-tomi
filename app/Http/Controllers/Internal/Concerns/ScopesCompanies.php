<?php

namespace App\Http\Controllers\Internal\Concerns;

use App\Models\ListPerusahaan;
use Illuminate\Database\Eloquent\Builder;

trait ScopesCompanies
{
    /**
     * Perusahaan yang boleh diakses admin yang login.
     * Admin bertipe PIC hanya boleh mengakses perusahaannya sendiri (company_id di tabel admins).
     */
    protected function visibleCompanies(): Builder
    {
        $admin = auth('admin')->user();
        $query = ListPerusahaan::query();

        if ($admin && $admin->type === 'pic') {
            empty($admin->company_id)
                ? $query->whereRaw('1 = 0')
                : $query->where('list_perusahaans.id', $admin->company_id);
        }

        return $query;
    }

    /**
     * Ambil perusahaan yang boleh diakses atau 404.
     */
    protected function findVisibleCompany(int|string|null $id): ListPerusahaan
    {
        return $this->visibleCompanies()->findOrFail((int) $id);
    }

    /**
     * Pastikan ID perusahaan (dari data turunan seperti tenor, karyawan, pinjaman) boleh diakses.
     */
    protected function authorizeCompanyId(int|string|null $id): void
    {
        abort_unless($id && $this->visibleCompanies()->whereKey((int) $id)->exists(), 404);
    }
}
