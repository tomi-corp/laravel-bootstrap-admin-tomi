<?php

namespace App\Modules\Lender\Services;

use App\Modules\Lender\Models\Company;
use App\Modules\Lender\Repositories\CompanyRepository;

class CompanyService
{
    public function __construct(
        private readonly CompanyRepository $companies,
        private readonly CompanyCodeGenerator $codeGenerator,
    ) {
    }

    public function create(array $data): Company
    {
        $data = $this->prepareDefaults($data);
        $data['unique_code'] = $data['unique_code'] ?? $this->codeGenerator->uniqueCode();
        $data['id_code_perusahaan'] = $data['id_code_perusahaan'] ?? $this->codeGenerator->companyCode();

        return $this->companies->create($data);
    }

    public function update(Company $company, array $data): bool
    {
        return $this->companies->update($company, $this->prepareDefaults($data));
    }

    public function delete(Company $company): ?bool
    {
        return $this->companies->delete($company);
    }

    private function prepareDefaults(array $data): array
    {
        $data = array_merge([
            'pic_internal' => 'Lender',
            'payment_date' => 25,
            'h_min_jatuh_tempo' => 0,
            'status' => 'active',
            'active_profesional_fee' => 'nonactive',
            'active_set_pinjaman' => 'nonactive',
            'active_tanggal' => 0,
        ], $data);

        return $data;
    }
}
