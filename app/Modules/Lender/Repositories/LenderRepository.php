<?php

namespace App\Modules\Lender\Repositories;

use App\Models\lender;
use Illuminate\Database\Eloquent\Builder;

class LenderRepository
{
    public function query(): Builder
    {
        return lender::query();
    }

    public function countAll(): int
    {
        return $this->query()->count();
    }

    public function countActive(): int
    {
        return $this->query()->where('status', 'active')->count();
    }

    public function countInactive(): int
    {
        return $this->query()->where('status', 'nonactive')->count();
    }
}
