<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class pinjaman extends Model
{
    use SoftDeletes;
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function data_karyawan()
    {
        return $this->hasOne(list_data_ksp::class, 'id', 'id_list_data_ksp');
    }

    public function jatuh_tempo()
    {
        return $this->hasOne(jatuh_tempo::class, 'id', 'id_pinjaman')
            ->orderBy('created_at', 'desc');
    }
}
