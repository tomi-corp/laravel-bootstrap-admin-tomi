<?php

namespace App\Modules\Lender\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'active_lenders' => $this->resource['active_lenders'] ?? 0,
            'inactive_lenders' => $this->resource['inactive_lenders'] ?? 0,
            'total_lenders' => $this->resource['total_lenders'] ?? 0,
        ];
    }
}
