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
        // kabupaten / kota
        $list_kota = kota::where('provinsi',  $this->provinsi->provinsi)->get();
        // kecamatan
        $list_kecamatan = kecamatan::where('kabupaten', $this->kabupaten->kabupaten)->get();
        // list_desa
        $list_desa = kelurahan::where('kecamatan', $this->kecamatan->kecamatan)->get();


        return [
            'pic_perusahaan' => $this->pic_perusahaan,
            'pic_internal' => $this->pic_internal,
            'nama' => $this->nama,
            'alamat_lengkap' => $this->alamat_lengkap,
            'telp' => $this->telp,
            'nomor_pks' => $this->nomor_pks,
            'status' => $this->status,
            'provinsi' => $this->provinsi->provinsi,
            'list_kabupaten' => WilayahKabupatenResource::collection($list_kota),
            'kabupaten' => $this->kabupaten->kabupaten,
            'list_kecamatan' => WilayahKecamatanResource::collection($list_kecamatan),
            'kecamatan' => $this->kecamatan->kecamatan,
            'list_desa' => WilayahDesaResource::collection($list_desa),
            'desa' => $this->desa->desa . '-' . $this->desa->kodepos,
            'payment_date' => $this->payment_date,
            'virtual_account_channel_code' => $this->virtual_account_channel_code,
        ];
    }
}
