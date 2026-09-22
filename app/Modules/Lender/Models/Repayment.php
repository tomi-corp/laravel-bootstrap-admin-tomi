<?php

namespace App\Modules\Lender\Models;

use App\Models\invoicing_pinjaman;
use App\Models\ksp_admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Repayment extends Model
{
    protected $table = 'repayments';

    protected $fillable = [
        'id_invoicing',
        'ksp_admin_id',
        'amount_total',
        'tanggal_pembayaran',
        'payment_proof_date',
        'payment_proof_file',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(invoicing_pinjaman::class, 'id_invoicing');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(ksp_admin::class, 'ksp_admin_id');
    }
}
