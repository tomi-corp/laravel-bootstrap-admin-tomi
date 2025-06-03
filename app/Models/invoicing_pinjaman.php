<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class invoicing_pinjaman extends Model
{
    public function pinjaman()
    {
        return $this->hasOne(pinjaman::class, 'id', 'id_pinjaman');
    }

    public function perusahaan()
    {
        return $this->hasOne(ListPerusahaan::class, 'id', 'id_list_perusahaan');
    }
}
