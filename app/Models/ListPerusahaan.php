<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ListPerusahaan extends Model
{

    use SoftDeletes;

    public function karyawan()
    {
        return $this->hasMany(list_karyawan::class, 'id_list_perusahaan', 'id')
            ->where('approve_satu', 'approve')
            ->where('approve_dua', 'approve');
    }

    public function karyawanSingle()
    {
        return $this->hasOne(list_karyawan::class, 'id_list_perusahaan', 'id')
            ->where('approve_satu', 'approve')
            ->where('approve_dua', 'approve');
    }

    public function pinjaman()
    {
        return $this->hasMany(pinjaman::class, 'id_perusahaan', 'id')
            ->where('status_otp', 'success');
    }

    public function lender()
    {
        return $this->hasOne(lender::class, 'id', 'id_lender');
    }

    public function karyawanPending()
    {
        $admin = auth('admin')->user()->type;
        if ($admin == "admin") {
            return $this->hasMany(list_karyawan::class, 'id_list_perusahaan', 'id')
                ->where('approve_satu', 'pending');
        } elseif ($admin == "master") {
            return $this->hasMany(list_karyawan::class, 'id_list_perusahaan', 'id')
                ->where('approve_dua', 'pending');
        }
    }


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

    public function amount_pinjaman()
    {
        return $this->hasOne(amount_pinjaman::class, 'id_perusahaan', 'id');
    }

    public function pinjamanBerjalan()
    {
        return $this->hasMany(pinjaman::class, 'id_perusahaan', 'id')
            ->where('status_otp', 'success')
            ->where('status', 'approve');
    }
}
