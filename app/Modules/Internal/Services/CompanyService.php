<?php

namespace App\Modules\Internal\Services;

use App\Models\ListPerusahaan;
use App\Modules\Internal\Repositories\CompanyRepository;

class CompanyService
{
    public function __construct(
        private readonly CompanyRepository $companies,
        private readonly CompanyCodeGenerator $codeGenerator,
    ) {
    }

    public function create(array $data): ListPerusahaan
    {
        $data['unique_code'] = $this->codeGenerator->uniqueCode();
        $data['id_code_perusahaan'] = $this->codeGenerator->companyCode();
        $data = $this->normalizeFeatureData($data);

        return $this->companies->create($data);
    }

    public function update(ListPerusahaan $company, array $data): bool
    {
        $data = $this->normalizeFeatureData($data);

        return $this->companies->update($company, $data);
    }

    public function delete(ListPerusahaan $company): ?bool
    {
        return $this->companies->delete($company);
    }

    private function normalizeNullableNumber(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (string) $value;
    }

    private function normalizeFeatureData(array $data): array
    {
        $data['max_plafon'] = $this->normalizeNullableNumber($data['max_plafon'] ?? null);

        if (($data['active_profesional_fee'] ?? null) !== 'active') {
            $data['tomi_dana_referral_1_id'] = null;
            $data['tomi_dana_referral_2_id'] = null;
        }

        return $data;
    }
}
