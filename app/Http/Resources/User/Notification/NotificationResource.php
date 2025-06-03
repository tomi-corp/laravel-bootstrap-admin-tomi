<?php

namespace App\Http\Resources\User\Notification;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
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
            'description' => $this->title,
            'type' => $this->type,
            'read' => $this->belum,
            'waktu' => Carbon::parse($this->created_at)->diffForHumans()
        ];
    }
}
