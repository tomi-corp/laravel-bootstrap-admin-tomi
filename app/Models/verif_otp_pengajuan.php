<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class verif_otp_pengajuan extends Model
{
    public function pinjaman()
    {
        return $this->hasOne(pinjaman::class, 'id', 'pinjaman_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
