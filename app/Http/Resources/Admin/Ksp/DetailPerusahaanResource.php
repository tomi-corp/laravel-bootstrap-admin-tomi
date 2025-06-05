<?php

namespace App\Http\Resources\Admin\Ksp;

use App\Http\Resources\Admin\Ksp\WilayahDesaResource;
use App\Http\Resources\Admin\Ksp\WilayahKabupatenResource;
use App\Http\Resources\Admin\Ksp\WilayahKecamatanResource;
use App\Models\kecamatan;
use App\Models\kelurahan;
use App\Models\kota;
use App\Models\provinsi;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailPerusahaanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'pic_perusahaan' => $this->pic_perusahaan,
            'pic_internal' => $this->pic_internal,
            'nama' => $this->nama,
            'alamat_lengkap' => $this->alamat_lengkap,
            'telp' => $this->telp,
            'nomor_pks' => $this->nomor_pks,
            'status' => $this->status,
            'provinsi' => $this->provinsi->provinsi,
            'kabupaten' => $this->kabupaten->kabupaten,
            'kecamatan' => $this->kecamatan->kecamatan,
            'desa' => $this->desa->desa,
            'payment_date' => $this->payment_date,
        ];
    }
}
