<?php

namespace App\Http\Resources\Admin\MobileMenu;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowMenuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'icon' => asset('assets/images/uploaded') . "/" . $this->icon,
            'path' => $this->path,
            'status' => $this->status,
            'lastUpdate' => Carbon::parse($this->updated_at)->format('Y-m-d H:i:s'),
        ];
    }
}
