<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class ksp_admin extends Authenticatable
{
    protected $table = 'ksp_admins';

    protected $fillable = [
        'id_lender',
        'nama',
        'username',
        'remember_token',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function lender()
    {
        return $this->belongsTo(lender::class, 'id_lender', 'id');
    }
}
