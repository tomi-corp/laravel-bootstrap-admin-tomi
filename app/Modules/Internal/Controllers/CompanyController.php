<?php

namespace App\Modules\Internal\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ListPerusahaan;
use App\Modules\Internal\Repositories\CompanyRepository;
use App\Modules\Internal\Repositories\RegionRepository;
use App\Modules\Internal\Requests\CompanyRequest;
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
        return view('internal.companies.index', [
            'activeCompanies' => $this->companies->countActive(),
            'inactiveCompanies' => $this->companies->countInactive(),
            'lenders' => $this->companies->lenders(),
            'provinces' => $this->companies->provinces(),
        ]);
    }

    public function table(): JsonResponse
    {
        return $this->companyTable->data();
    }

    public function store(CompanyRequest $request): JsonResponse
    {
        $this->companyService->create($request->validated());

        return response()->json([
            'message' => 'Perusahaan internal berhasil ditambahkan',
        ]);
    }

    public function show(ListPerusahaan $company): JsonResponse
    {
        return response()->json([
            'data' => $company->load(['lender', 'provinsi', 'kabupaten', 'kecamatan', 'desa']),
            'cities' => $this->regions->citiesByProvinceName(optional($company->provinsi)->provinsi),
            'districts' => $this->regions->districtsByCityName(optional($company->kabupaten)->kabupaten),
            'villages' => $this->regions->villagesByDistrictName(optional($company->kecamatan)->kecamatan),
        ]);
    }

    public function update(CompanyRequest $request, ListPerusahaan $company): JsonResponse
    {
        $this->companyService->update($company, $request->validated());

        return response()->json([
            'message' => 'Perusahaan internal berhasil diperbarui',
        ]);
    }

    public function destroy(ListPerusahaan $company): JsonResponse
    {
        $this->companyService->delete($company);

        return response()->json([
            'message' => 'Perusahaan internal berhasil dihapus',
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
