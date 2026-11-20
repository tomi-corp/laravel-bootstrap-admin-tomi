<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Perusahaan + produk yang direferensikan seorang referral, dengan posisi Referal 1 / Referal 2.
 */
class CompanyFeatureReferral extends Model
{
    use SoftDeletes;

    public const POSITIONS = [1 => 'Referal 1', 2 => 'Referal 2'];

    protected $table = 'company_feature_referrals';

    protected $fillable = ['company_id', 'feature_id', 'referral_id', 'position'];

    public function company()
    {
        return $this->belongsTo(ListPerusahaan::class, 'company_id');
    }

    public function feature()
    {
        return $this->belongsTo(Feature::class);
    }

    public function referral()
    {
        return $this->belongsTo(Referral::class);
    }
}
