<?php

namespace App\Http\Resources\User\History;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HistoryPpobResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this['transaction_id'],
            "nominal" => "Rp  " . number_format($this['price']),
            "produk" => $this['produk'],
            "status" => $this['status'],
            "datetime" => Carbon::parse($this['created_at'])->format('Y-m-d H:i:s'),
            "type" => $this['type'],
            "path" => $this['path'],
            "payment_method" => $this['payment_method'],
            "img" =>  asset('assets/images/uploaded') . "/" . $this['img'],
        ];
    }
}
