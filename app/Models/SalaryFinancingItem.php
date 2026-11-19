<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalaryFinancingItem extends Model
{
    use SoftDeletes;

    protected $table = 'salary_financing_items';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'salary_financing_id' => 'integer',
            'application_date' => 'date',
            'income_amount' => 'decimal:2',
            'payment_date' => 'date',
        ];
    }
}
