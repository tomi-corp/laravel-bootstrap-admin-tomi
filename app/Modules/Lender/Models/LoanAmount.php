<?php

namespace App\Modules\Lender\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanAmount extends Model
{
    protected $table = 'amount_pinjamen';

    protected $fillable = [
        'id_perusahaan',
        'iuran_anggota',
        'minimal',
        'maximal',
        'maximal_persentase',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'id_perusahaan');
    }
}
