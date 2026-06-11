<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GadgetTenor extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'company_id' => 'integer',
            'tenor_months' => 'integer',
            'interest_rate' => 'decimal:2',
            'insurance_fee' => 'decimal:2',
            'provision_fee' => 'decimal:2',
            'admin_fee' => 'decimal:2',
            'tomi_fee' => 'decimal:2',
            'service_fee' => 'decimal:2',
            'minimum_amount' => 'decimal:2',
            'maximum_amount' => 'decimal:2',
            'professional_fee_1' => 'decimal:2',
            'professional_fee_2' => 'decimal:2',
            'ksp_fee' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function ListPerusahaan(): BelongsTo
    {
        return $this->belongsTo(ListPerusahaan::class);
    }
}
