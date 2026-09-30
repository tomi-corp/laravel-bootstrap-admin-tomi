<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class repayment extends Model
{
    use SoftDeletes;

    protected $table = 'repayments';

    protected $fillable = [
        'id_invoicing',
        'ksp_admin_id',
        'amount_total',
        'tanggal_pembayaran',
        'payment_proof_date',
        'payment_proof_file',
    ];

    protected $casts = [
        'amount_total' => 'float',
        'tanggal_pembayaran' => 'datetime',
        'payment_proof_date' => 'date',
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
