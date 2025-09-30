<?php

namespace App\Http\Resources\Admin\Lender;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LenderDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'new_data' => $this->new_data,
            'nama' => $this->lender->nama,
            'id_code_lender' => $this->lender->id_code_lender,
            'status_pertama' => $this->status_pertama,
            'status_kedua' => $this->status_kedua,
        ];
    }
}
