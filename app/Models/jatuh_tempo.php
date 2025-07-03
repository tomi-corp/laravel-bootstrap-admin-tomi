<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class jatuh_tempo extends Model
{
    public function pinjaman()
    {
        return $this->hasOne(pinjaman::class, 'id', 'id_pinjaman');
    }
}
