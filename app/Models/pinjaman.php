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
        return $this->hasOne(list_karyawan::class, 'id', 'id_karyawan');
    }

    public function jatuh_tempo()
    {
        return $this->hasMany(jatuh_tempo::class, 'id_pinjaman', 'id')
            ->orderBy('created_at', 'desc');
    }

    public function jatuh_tempoSesuaiInvoice()
    {
        return $this->hasOne(jatuh_tempo::class, 'id_pinjaman', 'id')
            ->orderBy('created_at', 'desc');
    }

    public function angsuran()
    {
        return $this->hasMany(jatuh_tempo::class, 'id_pinjaman', 'id');
    }

    public function perusahaan()
    {
        return $this->hasOne(ListPerusahaan::class, 'id', 'id_perusahaan');
    }

    public function history_invoicing_lenders()
    {
        return $this->hasOne(pinjaman::class, 'id_list_perusahaan', 'id_list_perusahaan');
    }
}
