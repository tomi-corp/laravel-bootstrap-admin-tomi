<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyPks extends Model
{
    use SoftDeletes;

    protected $table = 'company_pks';

    protected $fillable = [
        'id_perusahaan',
        'feature_id',
        'nomor_pks',
        'tanggal_pks',
        'mulai_berlaku',
        'berakhir',
        'file_dokumen',
        'created_by',
    ];

    protected $casts = [
        'tanggal_pks' => 'date',
        'mulai_berlaku' => 'date',
        'berakhir' => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(ListPerusahaan::class, 'id_perusahaan');
    }

    public function feature()
    {
        return $this->belongsTo(Feature::class, 'feature_id');
    }
}
