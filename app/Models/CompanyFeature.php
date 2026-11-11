<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyFeature extends Model
{
    protected $table = 'company_features';

    protected $fillable = [
        'company_id',
        'feature_id',
        'nomor_pks',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(ListPerusahaan::class, 'company_id');
    }
}
