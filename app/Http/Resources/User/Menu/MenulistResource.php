<?php

namespace App\Http\Resources\User\Menu;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenulistResource extends JsonResource
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
        ];
    }
}
