<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class history_invoicing_lender extends Model
{
    protected $fillable = [
        'id_lender',
        'no_invoice',
        'period',
        'status',
        'update_by',
        'tanggal_pembayaran',
    ];

    public function lender()
    {
        return $this->hasOne(lender::class, 'id', 'id_lender');
    }

    public function pendingApproval()
    {
        return $this->hasOne(history_invoicing_lender_update::class, 'invoicing_lender_id', 'id')
            ->where('status_pertama', 'pending')
            ->orWhere('status_kedua', 'pending');
    }
}
