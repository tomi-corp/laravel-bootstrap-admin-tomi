<?php

namespace App\Http\Resources\Admin\Ksp;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TenorPinjamanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'tenor' => $this->tenor,
            'rate' => $this->rate,
            'biaya_admin' => $this->biaya_admin,
            'iuran_anggota' => $this->iuran_anggota,
        ];
    }
}
