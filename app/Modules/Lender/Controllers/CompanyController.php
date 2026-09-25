<?php

namespace App\Modules\Lender\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Lender\Models\Company;
use App\Modules\Lender\Repositories\CompanyRepository;
use App\Modules\Lender\Repositories\RegionRepository;
use App\Modules\Lender\Requests\CompanyRequest;
use App\Modules\Lender\Services\CompanyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function __construct(
        private readonly CompanyRepository $companies,
        private readonly RegionRepository $regions,
        private readonly CompanyService $companyService,
    ) {
    }

    public function index(): View
    {
        return view('lender.companies.index', [
            'companies' => $this->companies->allForLender($this->lenderId()),
        ]);
    }

    public function create(): View
    {
        return view('lender.companies.create', [
            'company' => new Company,
            'lenders' => $this->companies->lenderOptionsFor($this->lenderId()),
            'provinces' => $this->companies->provinces(),
            'cities' => collect(),
            'districts' => collect(),
            'villages' => collect(),
        ]);
    }

    public function store(CompanyRequest $request): RedirectResponse
    {
        $this->companyService->create($this->withCurrentLender($request->validated()));

        return redirect()
            ->route('lender.companies.index')
            ->with('success', 'Perusahaan berhasil ditambahkan.');
    }

    public function edit(Company $company): View
    {
        $this->authorizeCompany($company);

        return view('lender.companies.edit', [
            'company' => $company,
            'lenders' => $this->companies->lenderOptionsFor($this->lenderId()),
            'provinces' => $this->companies->provinces(),
            'cities' => $company->id_provinsi ? $this->regions->citiesByProvinceId((int) $company->id_provinsi) : collect(),
            'districts' => $company->id_kabupaten ? $this->regions->districtsByCityId((int) $company->id_kabupaten) : collect(),
            'villages' => $company->id_kecamatan ? $this->regions->villagesByDistrictId((int) $company->id_kecamatan) : collect(),
        ]);
    }

    public function show(Company $company): View
    {
        $this->authorizeCompany($company);

        return view('lender.companies.show', [
            'company' => $company->load(['lender', 'provinsi', 'kabupaten', 'kecamatan', 'desa']),
        ]);
    }

    public function update(CompanyRequest $request, Company $company): RedirectResponse
    {
        $this->authorizeCompany($company);

        $this->companyService->update($company, $this->withCurrentLender($request->validated()));

        return redirect()
            ->route('lender.companies.index')
            ->with('success', 'Perusahaan berhasil diperbarui.');
    }

    public function destroy(Company $company): RedirectResponse
    {
        $this->authorizeCompany($company);

        $this->companyService->delete($company);

        return redirect()
            ->route('lender.companies.index')
            ->with('success', 'Perusahaan berhasil dihapus.');
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

    private function lenderId(): int
    {
        return (int) auth('lender')->user()->id_lender;
    }

    private function withCurrentLender(array $data): array
    {
        $data['id_lender'] = $this->lenderId();

        return $data;
    }

    private function authorizeCompany(Company $company): void
    {
        abort_unless((int) $company->id_lender === $this->lenderId(), 404);
    }
}
