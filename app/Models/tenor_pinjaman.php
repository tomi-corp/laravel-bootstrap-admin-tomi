<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tenor_pinjaman extends Model
{
    public function amount_pinjaman()
    {
        return $this->hasOne(amount_pinjaman::class, 'id_perusahaan', 'id_perusahaan');
    }

    public function perusahaan()
    {
        return $this->hasOne(ListPerusahaan::class, 'id', 'id_perusahaan');
    }
}
