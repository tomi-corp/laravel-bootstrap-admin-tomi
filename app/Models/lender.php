<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class lender extends Model
{
    public function provinsi()
    {
        return $this->hasOne(provinsi::class, 'id', 'id_provinsi');
    }

    public function kabupaten()
    {
        return $this->hasOne(kota::class, 'id', 'id_kabupaten');
    }

    public function kecamatan()
    {
        return $this->hasOne(kecamatan::class, 'id', 'id_kecamatan');
    }

    public function desa()
    {
        return $this->hasOne(kelurahan::class, 'id', 'id_desa');
    }

    public function pendingApproval()
    {
        return $this->hasOne(lender_update::class, 'lender_id', 'id')
            ->where('status_pertama', 'pending')
            ->orWhere('status_kedua', 'pending');
    }

    public function bank()
    {
        return $this->hasOne(bank::class, 'id', 'id_bank');
    }
}
