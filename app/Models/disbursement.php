<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class disbursement extends Model
{
    use SoftDeletes;

    protected $table = 'disbursements';

    protected $fillable = [
        'pinjaman_id',
        'ksp_admin_id',
        'bank_id',
        'dana_yang_dicairkan',
        'atas_nama',
        'nomor_rekening',
        'transfer_date',
        'transfer_proof',
    ];

    protected $casts = [
        'transfer_date' => 'date',
        'dana_yang_dicairkan' => 'float',
    ];

    public function pinjaman(): BelongsTo
    {
        return $this->belongsTo(pinjaman::class, 'pinjaman_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(ksp_admin::class, 'ksp_admin_id');
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(bank::class, 'bank_id');
    }
}
