<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use SoftDeletes;

    public function instansi()
    {
        return $this->belongsTo(Instansi::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function lender()
    {
        return $this->belongsTo(lender::class);
    }

    public function company()
    {
        return $this->belongsTo(ListPerusahaan::class, 'company_id');
    }

    /** Data referral milik akun ini (referral adalah turunan admin, lewat referrals.admin_id). */
    public function referral()
    {
        return $this->hasOne(Referral::class, 'admin_id');
    }
}
