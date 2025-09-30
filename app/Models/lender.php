<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class lender extends Model
{
    protected $fillable = [
        'id_code_lender',
        'pic_lender',
        'pic_internal',
        'nama',
        'email_lender',
        'id_provinsi',
        'id_kabupaten',
        'id_kecamatan',
        'id_desa',
        'alamat_lengkap',
        'telp',
        'nomor_pks',
        'file_pks',
        'last_update_file_pks',
        'id_bank',
        'nama_rekening',
        'no_rekening',
        'status',
        'created_by',
        'updated_by',
        'logo',
        'color',
    ];

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

    public function pinjaman()
    {
        return $this->hasMany(pinjaman::class, 'id_lender', 'id');
    }
}
