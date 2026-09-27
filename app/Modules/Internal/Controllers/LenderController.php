<?php

namespace App\Modules\Internal\Controllers;

use App\Http\Controllers\Controller;
use App\Models\lender;
use App\Modules\Internal\Repositories\LenderRepository;
use App\Modules\Internal\Repositories\RegionRepository;
use App\Modules\Internal\Requests\LenderRequest;
use App\Modules\Internal\Services\LenderService;
use App\Modules\Internal\Services\LenderTableService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LenderController extends Controller
{
    public function __construct(
        private readonly LenderRepository $lenders,
        private readonly RegionRepository $regions,
        private readonly LenderService $lenderService,
        private readonly LenderTableService $lenderTable,
    ) {
    }

    public function index()
    {
        return view('internal.lenders.index', [
            'activeLenders' => $this->lenders->countActive(),
            'inactiveLenders' => $this->lenders->countInactive(),
            'provinces' => $this->lenders->provinces(),
            'banks' => $this->lenders->banks(),
        ]);
    }

    public function table(): JsonResponse
    {
        return $this->lenderTable->data();
    }

    public function store(LenderRequest $request): JsonResponse
    {
        $this->lenderService->create($request->validated());

        return response()->json([
            'message' => 'Lender internal berhasil ditambahkan',
        ]);
    }

    public function show(lender $lender): JsonResponse
    {
        return response()->json([
            'data' => $lender->load(['bank', 'provinsi', 'kabupaten', 'kecamatan', 'desa']),
            'cities' => $this->regions->citiesByProvinceName(optional($lender->provinsi)->provinsi),
            'districts' => $this->regions->districtsByCityName(optional($lender->kabupaten)->kabupaten),
            'villages' => $this->regions->villagesByDistrictName(optional($lender->kecamatan)->kecamatan),
        ]);
    }

    public function update(LenderRequest $request, lender $lender): JsonResponse
    {
        $this->lenderService->update($lender, $request->validated());

        return response()->json([
            'message' => 'Lender internal berhasil diperbarui',
        ]);
    }

    public function destroy(lender $lender): JsonResponse
    {
        $this->lenderService->delete($lender);

        return response()->json([
            'message' => 'Lender internal berhasil dihapus',
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
