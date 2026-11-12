<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LenderFeature extends Model
{
    use SoftDeletes;

    protected $table = 'lender_features';

    protected $fillable = [
        'lender_id',
        'feature_id',
        'pks_number',
        'pks_date',
        'pks_expired_at',
        'is_active',
    ];

    protected $casts = [
        'pks_date' => 'date:Y-m-d',
        'pks_expired_at' => 'date:Y-m-d',
        'is_active' => 'boolean',
    ];
}
