<?php

namespace App\Modules\Lender\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanTenor extends Model
{
    protected $table = 'tenor_pinjamen';

    protected $fillable = [
        'id_perusahaan',
        'tenor',
        'rate',
        'asuransi',
        'provisi',
        'biaya_admin',
        'fee_tomi',
        'biaya_layanan',
        'minimal_nominal',
        'maximal_nominal',
        'profesional_fee_1',
        'profesional_fee_2',
        'ksp',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'id_perusahaan');
    }
}
