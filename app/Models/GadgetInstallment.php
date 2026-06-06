<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GadgetInstallment extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'tenor_months' => 'integer',
            'interest_rate' => 'decimal:2',
            'start_date' => 'date',
            'total_amount' => 'decimal:2',
            'monthly_installment' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
