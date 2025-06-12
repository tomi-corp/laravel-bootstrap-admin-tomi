<?php

namespace App\Http\Resources\Admin\Ksp;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WilayahKecamatanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'kecamatan' => $this->kecamatan
        ];
    }
}
