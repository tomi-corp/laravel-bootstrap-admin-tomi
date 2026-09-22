<?php

namespace App\Modules\Lender\Repositories;

use App\Models\kecamatan;
use App\Models\kelurahan;
use App\Models\kota;
use App\Models\provinsi;
use Illuminate\Database\Eloquent\Collection;

class RegionRepository
{
    public function citiesByProvinceId(int $provinceId): Collection
    {
        $province = provinsi::findOrFail($provinceId);

        return kota::query()
            ->where('provinsi', $province->provinsi)
            ->orderBy('kabupaten')
            ->get(['id', 'kabupaten']);
    }

    public function districtsByCityId(int $cityId): Collection
    {
        $city = kota::findOrFail($cityId);

        return kecamatan::query()
            ->where('kabupaten', $city->kabupaten)
            ->orderBy('kecamatan')
            ->get(['id', 'kecamatan']);
    }

    public function villagesByDistrictId(int $districtId): Collection
    {
        $district = kecamatan::findOrFail($districtId);

        return kelurahan::query()
            ->where('kecamatan', $district->kecamatan)
            ->orderBy('desa')
            ->get(['id', 'desa', 'kodepos']);
    }
}
