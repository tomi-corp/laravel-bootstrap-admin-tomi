<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GadgetInstallment extends Model
{
    protected $table = 'gadget_installments';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'seller_id' => 'integer',
            'product_id' => 'integer',
            'variant_id' => 'integer',
            'gadget_tenor_id' => 'integer',

            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'product_price' => 'decimal:2',
            'down_payment' => 'decimal:2',
            'tenor_months' => 'integer',
            'interest_rate' => 'decimal:2',
            'admin_fee' => 'decimal:2',
            'total_interest' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'monthly_installment' => 'decimal:2',

            'start_date' => 'date',
            'transfer_date' => 'date',
            'is_transferred' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function tenor(): BelongsTo
    {
        return $this->belongsTo(GadgetTenor::class, 'gadget_tenor_id');
    }

    public function gadgetTenor(): BelongsTo
    {
        return $this->belongsTo(GadgetTenor::class, 'gadget_tenor_id');
    }
}
