<?php

namespace App\Http\Resources\User\DailyCheckin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DailyCheckinResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $status = "disabled";
        if ($this->historyDailyCheckin->first() !== null) {
            if (Carbon::parse($this->historyDailyCheckin->first()->created_at)->format("Y-m-d") == Carbon::now()->format('Y-m-d')) {
                $status = "sudah";
            } elseif (Carbon::parse($this->historyDailyCheckin->first()->created_at)->format("Y-m-d") < Carbon::now()->format('Y-m-d')) {
                $status = "sudah";
            }
        }

        if ($this->hari_ke == $this->newkey) {
            $status = "absen";
        }

        return [
            'id' => $this->id,
            'hari_ke' => $this->hari_ke,
            'reward' => $this->reward,
            'status' => $status
        ];
    }
}
