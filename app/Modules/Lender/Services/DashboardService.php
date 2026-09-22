<?php

namespace App\Modules\Lender\Services;

use App\Modules\Lender\Repositories\LenderRepository;

class DashboardService
{
    public function __construct(
        private readonly LenderRepository $lenders,
    ) {
    }

    public function summary(): array
    {
        return [
            'active_lenders' => $this->lenders->countActive(),
            'inactive_lenders' => $this->lenders->countInactive(),
            'total_lenders' => $this->lenders->countAll(),
        ];
    }
}
