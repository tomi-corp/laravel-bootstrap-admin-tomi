<?php

namespace App\Modules\Lender\Models;

use App\Models\bank;
use App\Models\ksp_admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Disbursement extends Model
{
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

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class, 'pinjaman_id');
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
