<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectronicInstallment extends Model
{
    use HasFactory;

    protected $table = 'electronic_installments';

    protected $fillable = [
        'name',
        'price',
        'description',
        'image',
        'tenor',
        'interest',
    ];

    protected $casts = [
        'price'    => 'decimal:2',
        'interest' => 'decimal:2',
        'tenor'    => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Accessor Cicilan Bulanan
    |--------------------------------------------------------------------------
    */
    public function getMonthlyInstallmentAttribute()
    {
        $total = $this->price + ($this->price * ($this->interest / 100) * $this->tenor);

        return $total / $this->tenor;
    }

    /*
    |--------------------------------------------------------------------------
    | Accessor Total Pembayaran
    |--------------------------------------------------------------------------
    */
    public function getTotalPaymentAttribute()
    {
        return $this->price + (
            $this->price * ($this->interest / 100) * $this->tenor
        );
    }
}
