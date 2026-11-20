<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Referral: turunan akun admin (instansi Referral) beserta data KTP dan rekening bank.
 */
class Referral extends Model
{
    use SoftDeletes;

    protected $table = 'referrals';

    protected $casts = [
        'birth_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function bank()
    {
        return $this->belongsTo(bank::class);
    }

    public function companyReferrals()
    {
        return $this->hasMany(CompanyReferral::class);
    }
}
