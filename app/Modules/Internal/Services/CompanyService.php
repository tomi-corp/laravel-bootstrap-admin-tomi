<?php

namespace App\Modules\Internal\Services;

use App\Models\ListPerusahaan;
use App\Models\Referral;
use App\Modules\Internal\Repositories\CompanyRepository;

class CompanyService
{
    private const REFERRAL_PREFIXES = [
        'tomi_dana',
        'tomi_dana_plus',
        'tomi_gawai',
        'tomi_gaji',
        'tomi_invoice',
    ];

    private const REFERRAL_FORM_FIELDS = [
        'national_id_number',
        'name',
        'birth_place',
        'birth_date',
        'gender',
        'blood_type',
        'address',
        'rt',
        'rw',
        'province_id',
        'city_id',
        'district_id',
        'village_id',
        'postal_code',
        'religion',
        'marital_status',
        'occupation',
        'nationality',
        'phone_number',
        'email',
        'bank_id',
        'bank_account_number',
        'bank_account_name',
        'is_active',
        'notes',
    ];

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
        $data = $this->syncReferralForms($data);

        if (($data['active_profesional_fee'] ?? null) !== 'active') {
            $data['tomi_dana_referral_1_id'] = null;
            $data['tomi_dana_referral_2_id'] = null;
        }

        return $data;
    }

    private function syncReferralForms(array $data): array
    {
        foreach (self::REFERRAL_PREFIXES as $prefix) {
            foreach ([1, 2] as $index) {
                $idKey = "{$prefix}_referral_{$index}_id";
                $nameKey = "{$prefix}_referral_{$index}_name";
                $nikKey = "{$prefix}_referral_{$index}_national_id_number";

                if (! empty($data[$idKey])) {
                    if (! empty($data[$nameKey])) {
                        Referral::where('id', $data[$idKey])->update(
                            $this->referralPayload($data, $prefix, $index)
                        );
                    }

                    $data = $this->unsetReferralFormData($data, $prefix, $index);
                    continue;
                }

                if (empty($data[$nameKey])) {
                    $data = $this->unsetReferralFormData($data, $prefix, $index);
                    continue;
                }

                $referralData = $this->referralPayload($data, $prefix, $index);

                $referral = ! empty($referralData['national_id_number'])
                    ? Referral::firstOrCreate(
                        ['national_id_number' => $referralData['national_id_number']],
                        $referralData
                    )
                    : Referral::create($referralData);

                $data[$idKey] = $referral->id;

                $data = $this->unsetReferralFormData($data, $prefix, $index);
            }
        }

        return $data;
    }

    private function referralPayload(array $data, string $prefix, int $index): array
    {
        $payload = [];

        foreach (self::REFERRAL_FORM_FIELDS as $field) {
            $payload[$field] = $data["{$prefix}_referral_{$index}_{$field}"] ?? null;
        }

        $payload['is_active'] = array_key_exists("{$prefix}_referral_{$index}_is_active", $data)
            ? (bool) $data["{$prefix}_referral_{$index}_is_active"]
            : true;

        return $payload;
    }

    private function unsetReferralFormData(array $data, string $prefix, int $index): array
    {
        foreach (self::REFERRAL_FORM_FIELDS as $field) {
            unset($data["{$prefix}_referral_{$index}_{$field}"]);
        }

        return $data;
    }
}
