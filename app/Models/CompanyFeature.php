<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyFeature extends Model
{
    use SoftDeletes;

    protected $table = 'company_features';

    protected $fillable = [
        'company_id',
        'feature_id',
        'pks_number',
        'pks_date',
        'pks_expired_at',
        'is_active',
    ];

    protected $casts = [
        'pks_date' => 'date',
        'pks_expired_at' => 'date',
        'is_active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(ListPerusahaan::class, 'company_id');
    }
}
