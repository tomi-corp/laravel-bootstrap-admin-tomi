<?php

namespace App\Modules\Internal\Repositories;

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

        return $this->citiesByProvinceName($province->provinsi);
    }

    public function districtsByCityId(int $cityId): Collection
    {
        $city = kota::findOrFail($cityId);

        return $this->districtsByCityName($city->kabupaten);
    }

    public function villagesByDistrictId(int $districtId): Collection
    {
        $district = kecamatan::findOrFail($districtId);

        return $this->villagesByDistrictName($district->kecamatan);
    }

    public function citiesByProvinceName(?string $province): Collection
    {
        return kota::where('provinsi', $province)
            ->orderBy('kabupaten')
            ->get(['id', 'kabupaten']);
    }

    public function districtsByCityName(?string $city): Collection
    {
        return kecamatan::where('kabupaten', $city)
            ->orderBy('kecamatan')
            ->get(['id', 'kecamatan']);
    }

    public function villagesByDistrictName(?string $district): Collection
    {
        return kelurahan::where('kecamatan', $district)
            ->orderBy('desa')
            ->get(['id', 'desa', 'kodepos']);
    }
}
