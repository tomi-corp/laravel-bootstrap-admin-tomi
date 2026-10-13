<?php

namespace App\Modules\Internal\Services;

use App\Models\Admin;
use App\Models\ListPerusahaan;
use App\Models\Project;
use App\Models\Referral;
use App\Modules\Internal\Repositories\CompanyRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

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

    private const PIC_PREFIXES = [
        'pic_perusahaan' => 'PIC Perusahaan',
        'pic_internal' => 'PIC Internal',
    ];

    private const PIC_FORM_FIELDS = [
        'national_id_number',
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
        'password',
    ];

    public function __construct(
        private readonly CompanyRepository $companies,
        private readonly CompanyCodeGenerator $codeGenerator,
    ) {
    }

    public function create(array $data): ListPerusahaan
    {
        return DB::transaction(function () use ($data) {
            $data['unique_code'] = $this->codeGenerator->uniqueCode();
            $data['id_code_perusahaan'] = $this->codeGenerator->companyCode();
            $data = $this->normalizeFeatureData($data);

            $company = $this->companies->create($this->withoutPicFormData($data));
            $this->syncPicAdmins($company, $data);

            return $company;
        });
    }

    public function update(ListPerusahaan $company, array $data): bool
    {
        return DB::transaction(function () use ($company, $data) {
            $data = $this->normalizeFeatureData($data);
            $updated = $this->companies->update($company, $this->withoutPicFormData($data));
            $this->syncPicAdmins($company->refresh(), $data);

            return $updated;
        });
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

    private function withoutPicFormData(array $data): array
    {
        foreach (array_keys(self::PIC_PREFIXES) as $prefix) {
            foreach (self::PIC_FORM_FIELDS as $field) {
                unset($data["{$prefix}_{$field}"]);
            }
        }

        return $data;
    }

    private function syncPicAdmins(ListPerusahaan $company, array $data): void
    {
        foreach (self::PIC_PREFIXES as $prefix => $label) {
            if (empty($data[$prefix])) {
                continue;
            }

            Admin::withoutEvents(function () use ($company, $data, $prefix, $label) {
                $admin = Admin::query()
                    ->where('company_id', $company->id)
                    ->where('username', $this->picUsername($prefix, $company->id))
                    ->first();

                if (! $admin && ! empty($data["{$prefix}_email"])) {
                    $admin = Admin::query()
                        ->where('company_id', $company->id)
                        ->where('email', $data["{$prefix}_email"])
                        ->first();
                }

                $admin ??= new Admin();

                foreach ($this->filterAdminColumns($this->picAdminPayload($company, $data, $prefix, $label, $admin->exists)) as $field => $value) {
                    $admin->{$field} = $value;
                }

                $admin->save();
            });
        }
    }

    private function picAdminPayload(ListPerusahaan $company, array $data, string $prefix, string $label, bool $exists): array
    {
        [$firstName, $lastName] = $this->splitName($data[$prefix] ?? '');

        $payload = [
            'project_id' => $this->companyProjectId(),
            'company_id' => $company->id,
            'nik' => $data["{$prefix}_national_id_number"] ?? null,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'birth_place' => $data["{$prefix}_birth_place"] ?? null,
            'birth_date' => $data["{$prefix}_birth_date"] ?? null,
            'gender' => $data["{$prefix}_gender"] ?? null,
            'blood_type' => $data["{$prefix}_blood_type"] ?? null,
            'address' => $data["{$prefix}_address"] ?? null,
            'rt' => $data["{$prefix}_rt"] ?? null,
            'rw' => $data["{$prefix}_rw"] ?? null,
            'wilayah_provinsi_net_id' => $data["{$prefix}_province_id"] ?? null,
            'wilayah_kabupaten_net_id' => $data["{$prefix}_city_id"] ?? null,
            'wilayah_kecamatan_net_id' => $data["{$prefix}_district_id"] ?? null,
            'wilayah_desa_net_id' => $data["{$prefix}_village_id"] ?? null,
            'postal_code' => $data["{$prefix}_postal_code"] ?? null,
            'religion' => $this->adminReligion($data["{$prefix}_religion"] ?? null),
            'marital_status' => $data["{$prefix}_marital_status"] ?? null,
            'occupation' => $data["{$prefix}_occupation"] ?: $label,
            'nationality' => $data["{$prefix}_nationality"] ?? 'WNI',
            'phone' => $data["{$prefix}_phone_number"] ?? null,
            'username' => $this->picUsername($prefix, $company->id),
            'email' => $data["{$prefix}_email"] ?: $this->picFallbackEmail($prefix, $company),
        ];

        if (Schema::hasColumn('admins', 'type')) {
            $payload['type'] = 'pic';
        }

        if (Schema::hasColumn('admins', 'role')) {
            $payload['role'] = 'pic';
        }

        if (Schema::hasColumn('admins', 'role_id')) {
            $payload['role_id'] = $this->picRoleId($payload['project_id']);
        }

        if (! $exists || ! empty($data["{$prefix}_password"])) {
            $payload['password'] = Hash::make(
                $data["{$prefix}_password"] ?: Str::random(16)
            );
        }

        return $payload;
    }

    private function splitName(string $name): array
    {
        $name = trim($name);

        if ($name === '') {
            return [null, null];
        }

        $parts = preg_split('/\s+/', $name, 2);

        return [$parts[0], $parts[1] ?? null];
    }

    private function picUsername(string $prefix, int $companyId): string
    {
        return $prefix . '_' . $companyId;
    }

    private function picFallbackEmail(string $prefix, ListPerusahaan $company): string
    {
        return $prefix . '_' . $company->id . '@tomi.local';
    }

    private function companyProjectId(): ?int
    {
        return Project::query()
            ->where('code', 'company')
            ->orWhere('name', 'Perusahaan')
            ->value('id');
    }

    private function picRoleId(?int $projectId): ?int
    {
        if (! $projectId || ! class_exists(\App\Models\Role::class)) {
            return null;
        }

        return \App\Models\Role::query()
            ->where('project_id', $projectId)
            ->whereRaw('LOWER(name) = ?', ['pic'])
            ->value('id');
    }

    private function adminReligion(?string $religion): ?string
    {
        return match ($religion) {
            'christian' => 'kristen',
            'catholic' => 'katolik',
            'buddhist' => 'buddha',
            'confucian' => 'konghucu',
            default => $religion,
        };
    }

    private function filterAdminColumns(array $payload): array
    {
        $columns = Schema::getColumnListing('admins');

        return array_intersect_key($payload, array_flip($columns));
    }
}
