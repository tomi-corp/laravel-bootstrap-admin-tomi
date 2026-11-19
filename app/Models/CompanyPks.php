<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyPks extends Model
{
    use SoftDeletes;

    protected $table = 'company_pks';

    protected $fillable = [
        'company_id',
        'feature_id',
        'agreement_number',
        'agreement_date',
        'start_date',
        'end_date',
        'document_file',
        'created_by',
    ];

    protected $casts = [
        'agreement_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(ListPerusahaan::class, 'company_id');
    }

    public function feature()
    {
        return $this->belongsTo(Feature::class, 'feature_id');
    }
}
