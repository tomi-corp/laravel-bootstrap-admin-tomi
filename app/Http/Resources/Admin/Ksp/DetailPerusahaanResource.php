<?php

namespace App\Http\Resources\Admin\Ksp;

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
            'alamat' => $this->alamat,
            'telp' => $this->telp,
            'nomor_pks' => $this->nomor_pks,
            'status' => $this->status,
        ];
    }
}
