<?php

namespace App\Http\Resources\Admin\Lender\Invocing;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoicingUpdateDetailResource extends JsonResource
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
            'nama' => $this->history_invoicing?->lender?->nama ?? '-',
            'id_code_lender' => $this->history_invoicing?->no_invoice ?? '-',
            'status_pertama' => $this->status_pertama,
            'status_kedua' => $this->status_kedua,
        ];
    }
}
