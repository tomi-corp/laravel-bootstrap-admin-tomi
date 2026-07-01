<?php

namespace App\Http\Resources\User\History;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "id_perusahaan" => $this->id_perusahaan,
            "nama_perusahaan" => $this->perusahaan?->nama,
            "kode_perusahaan" => $this->perusahaan?->id_code_perusahaan,
            "nominal" => "Rp  " . number_format($this->nominal),
            "produk" => $this->produk,
            "status" => $this->status,
            "datetime" => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            "type" => $this->type,
            "path" => $this->path,
            "ttd_berkas" => $this->ttd,
            "img" =>  asset('assets/images/uploaded') . "/" . $this->img,
        ];
    }
}
