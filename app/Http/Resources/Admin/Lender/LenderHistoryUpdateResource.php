<?php

namespace App\Http\Resources\Admin\Lender;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LenderHistoryUpdateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $oldData = $this->new_data['old_data'] ?? [];
        $newData = $this->new_data['new_data'] ?? [];

        $keys = array_unique(array_merge(
            array_keys($oldData),
            array_keys($newData)
        ));

        $formattedData = [];

        foreach ($keys as $key) {
            $formattedData[$key] = [
                'old_data' => $oldData[$key] ?? '-',
                'new_data' => $newData[$key] ?? '-',
            ];
        }

        return [
            'datetime' => $this->created_at,
            'data' => $formattedData,
        ];
    }
}
