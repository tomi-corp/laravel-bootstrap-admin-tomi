<?php

namespace App\Http\Resources\Admin\Ksp;

use App\Models\kecamatan;
use App\Models\kelurahan;
use App\Models\kota;
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
        $list_kota = kota::where('provinsi', $this->provinsi->provinsi)->get();
        // kecamatan
        $list_kecamatan = kecamatan::where('kabupaten', $this->kabupaten->kabupaten)->get();
        // list_desa
        $list_desa = kelurahan::where('kecamatan', $this->kecamatan->kecamatan)->get();

        return [
            'pic_perusahaan' => $this->pic_perusahaan,
            'pic_internal' => $this->picInternalAdmin?->username ?? $this->pic_internal,
            'admin_id' => $this->admin_id,
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
            'desa' => $this->desa->desa.'-'.$this->desa->kodepos,
            'payment_date' => $this->payment_date,
        ];
    }
}
