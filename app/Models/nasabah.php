<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class nasabah extends Model
{
    public function provinsi()
    {
        return $this->hasOne(provinsi::class, 'id', 'provinsi_id');
    }

    public function kabupaten()
    {
        return $this->hasOne(kota::class, 'id', 'kota_id');
    }

    public function kecamatan()
    {
        return $this->hasOne(kecamatan::class, 'id', 'kecamatan_id');
    }

    public function desa()
    {
        return $this->hasOne(kelurahan::class, 'id', 'kelurahan_id');
    }

    public function bank()
    {
        return $this->hasOne(bank::class, 'id', 'bank_id');
    }

    public function dati()
    {
        return $this->hasOne(dati::class, 'id', 'kode_dati_id');
    }
}
