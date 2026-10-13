<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CompanyRequest;
use App\Models\Admin;
use App\Models\bank;
use App\Models\ListPerusahaan;
use App\Models\Referral;
use App\Modules\Internal\Repositories\CompanyRepository;
use App\Modules\Internal\Repositories\RegionRepository;
use App\Modules\Internal\Services\CompanyService;
use App\Modules\Internal\Services\CompanyTableService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function __construct(
        private readonly CompanyRepository $companies,
        private readonly RegionRepository $regions,
        private readonly CompanyService $companyService,
        private readonly CompanyTableService $companyTable,
    ) {
    }

    public function index()
    {
        return view('admin.companies.index', [
            'activeCompanies' => $this->companies->countActive(),
            'inactiveCompanies' => $this->companies->countInactive(),
            'lenders' => $this->companies->lenders(),
            'provinces' => $this->companies->provinces(),
            'banks' => bank::orderBy('code_bank')->get(['id', 'description']),
            'referrals' => Referral::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'national_id_number']),
        ]);
    }

    public function table(): JsonResponse
    {
        return $this->companyTable->data();
    }

    public function store(CompanyRequest $request): JsonResponse
    {
        $company = $this->companyService->create($request->validated());

        return response()->json([
            'message' => 'Perusahaan berhasil ditambahkan',
            'data' => [
                'id' => $company->id,
            ],
        ]);
    }

    public function show(ListPerusahaan $company): JsonResponse
    {
        $company->load([
            'lender',
            'provinsi',
            'kabupaten',
            'kecamatan',
            'desa',
            'tomiDanaReferral1',
            'tomiDanaReferral2',
            'tomiDanaPlusReferral1',
            'tomiDanaPlusReferral2',
            'tomiGawaiReferral1',
            'tomiGawaiReferral2',
            'tomiGajiReferral1',
            'tomiGajiReferral2',
            'tomiInvoiceReferral1',
            'tomiInvoiceReferral2',
        ]);
        $company->configuration_url = route('admin.ksp.konfigurasi', encryptSecure($company->id));

        return response()->json([
            'data' => $company,
            'pic_forms' => $this->picForms($company),
            'referral_forms' => $this->referralForms($company),
            'cities' => $this->regions->citiesByProvinceName(optional($company->provinsi)->provinsi),
            'districts' => $this->regions->districtsByCityName(optional($company->kabupaten)->kabupaten),
            'villages' => $this->regions->villagesByDistrictName(optional($company->kecamatan)->kecamatan),
        ]);
    }

    private function picForms(ListPerusahaan $company): array
    {
        return collect([
            'pic_perusahaan' => $company->pic_perusahaan,
            'pic_internal' => $company->pic_internal,
        ])->map(function (?string $name, string $prefix) use ($company) {
            $admin = Admin::query()
                ->where('company_id', $company->id)
                ->where('username', "{$prefix}_{$company->id}")
                ->first();

            return [
                'name' => $name,
                'national_id_number' => $admin?->nik,
                'birth_place' => $admin?->birth_place,
                'birth_date' => $admin?->birth_date ? substr((string) $admin->birth_date, 0, 10) : null,
                'gender' => $admin?->gender,
                'blood_type' => $admin?->blood_type,
                'address' => $admin?->address,
                'rt' => $admin?->rt,
                'rw' => $admin?->rw,
                'province_id' => $admin?->wilayah_provinsi_net_id,
                'city_id' => $admin?->wilayah_kabupaten_net_id,
                'district_id' => $admin?->wilayah_kecamatan_net_id,
                'village_id' => $admin?->wilayah_desa_net_id,
                'postal_code' => $admin?->postal_code,
                'religion' => $this->formReligion($admin?->religion),
                'marital_status' => $admin?->marital_status,
                'occupation' => $admin?->occupation,
                'nationality' => $admin?->nationality,
                'phone_number' => $admin?->phone,
                'email' => $admin?->email,
            ];
        })->all();
    }

    private function formReligion(?string $religion): ?string
    {
        return match ($religion) {
            'kristen' => 'christian',
            'katolik' => 'catholic',
            'buddha' => 'buddhist',
            'konghucu' => 'confucian',
            default => $religion,
        };
    }

    private function referralForms(ListPerusahaan $company): array
    {
        return collect([
            'tomi_dana' => ['tomiDanaReferral1', 'tomiDanaReferral2'],
            'tomi_dana_plus' => ['tomiDanaPlusReferral1', 'tomiDanaPlusReferral2'],
            'tomi_gawai' => ['tomiGawaiReferral1', 'tomiGawaiReferral2'],
            'tomi_gaji' => ['tomiGajiReferral1', 'tomiGajiReferral2'],
            'tomi_invoice' => ['tomiInvoiceReferral1', 'tomiInvoiceReferral2'],
        ])->map(function (array $relations) use ($company) {
            return collect($relations)->map(function (string $relation) use ($company) {
                $referral = $company->{$relation};

                return $referral ? [
                    'id' => $referral->id,
                    'national_id_number' => $referral->national_id_number,
                    'name' => $referral->name,
                    'birth_place' => $referral->birth_place,
                    'birth_date' => optional($referral->birth_date)->format('Y-m-d'),
                    'gender' => $referral->gender,
                    'blood_type' => $referral->blood_type,
                    'address' => $referral->address,
                    'rt' => $referral->rt,
                    'rw' => $referral->rw,
                    'province_id' => $referral->province_id,
                    'city_id' => $referral->city_id,
                    'district_id' => $referral->district_id,
                    'village_id' => $referral->village_id,
                    'postal_code' => $referral->postal_code,
                    'religion' => $referral->religion,
                    'marital_status' => $referral->marital_status,
                    'occupation' => $referral->occupation,
                    'nationality' => $referral->nationality,
                    'phone_number' => $referral->phone_number,
                    'email' => $referral->email,
                    'bank_id' => $referral->bank_id,
                    'bank_account_number' => $referral->bank_account_number,
                    'bank_account_name' => $referral->bank_account_name,
                    'is_active' => $referral->is_active ? '1' : '0',
                    'notes' => $referral->notes,
                ] : null;
            })->values()->all();
        })->all();
    }

    public function update(CompanyRequest $request, ListPerusahaan $company): JsonResponse
    {
        $this->companyService->update($company, $request->validated());

        return response()->json([
            'message' => 'Perusahaan berhasil diperbarui',
            'data' => [
                'id' => $company->id,
            ],
        ]);
    }

    public function destroy(ListPerusahaan $company): JsonResponse
    {
        $this->companyService->delete($company);

        return response()->json([
            'message' => 'Perusahaan berhasil dihapus',
        ]);
    }

    public function cities(Request $request): JsonResponse
    {
        return response()->json($this->regions->citiesByProvinceId($request->integer('province_id')));
    }

    public function districts(Request $request): JsonResponse
    {
        return response()->json($this->regions->districtsByCityId($request->integer('city_id')));
    }

    public function villages(Request $request): JsonResponse
    {
        return response()->json($this->regions->villagesByDistrictId($request->integer('district_id')));
    }
}
