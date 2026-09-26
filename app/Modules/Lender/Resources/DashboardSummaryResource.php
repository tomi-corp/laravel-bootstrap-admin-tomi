<?php

namespace App\Modules\Lender\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'total_companies' => $this->resource['total_companies'] ?? 0,
            'total_employees' => $this->resource['total_employees'] ?? 0,
            'total_loans' => $this->resource['total_loans'] ?? 0,
            'pending_loans' => $this->resource['pending_loans'] ?? 0,
            'approved_loans' => $this->resource['approved_loans'] ?? 0,
            'done_loans' => $this->resource['done_loans'] ?? 0,
        ];
    }
}
