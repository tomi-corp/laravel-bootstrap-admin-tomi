<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class pinjaman extends Model
{
    public function pembayaran_pinjaman()
    {
        return $this->hasMany(pembayaran_pinjaman::class, 'pinjaman_id', 'id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
