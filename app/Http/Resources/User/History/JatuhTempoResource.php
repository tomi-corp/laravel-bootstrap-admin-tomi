<?php

namespace App\Http\Resources\User\History;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JatuhTempoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'pembayaran' => "ke-" . $this->pembayaran,
            'jatuh_tempo' => Carbon::parse($this->jatuh_tempo)->format('d M Y'),
            'status' => ($this->status == "lunas") ? "Lunas" : ((Carbon::parse($this->jatuh_tempo) < Carbon::now()) ? "Dalam Proses" : "Belum Lunas"),
        ];
    }
}
