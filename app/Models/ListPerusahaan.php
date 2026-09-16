<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ListPerusahaan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'unique_code',
        'id_lender',
        'id_code_perusahaan',
        'pic_perusahaan',
        'pic_internal',
        'nama',
        'logo',
        'email_perusahaan',
        'id_provinsi',
        'id_kabupaten',
        'id_kecamatan',
        'id_desa',
        'alamat_lengkap',
        'bank',
        'no_rekening',
        'atas_nama_rekening',
        'nama_direktur_utama',
        'telp',
        'nomor_pks',
        'payment_date',
        'h_min_jatuh_tempo',
        'status',
        'active_profesional_fee',
        'active_set_pinjaman',
        'active_tanggal',
        'max_plafon',
    ];

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

    public function karyawanAll()
    {
        return $this->hasMany(list_karyawan::class, 'id_list_perusahaan', 'id');
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
        $adminType = auth('admin')->user()?->type;

        if ($adminType === 'admin') {
            return $this->hasMany(list_karyawan::class, 'id_list_perusahaan', 'id')
                ->where('approve_satu', 'pending');
        }

        if ($adminType === 'master') {
            return $this->hasMany(list_karyawan::class, 'id_list_perusahaan', 'id')
                ->where('approve_dua', 'pending');
        }

        return $this->hasMany(list_karyawan::class, 'id_list_perusahaan', 'id')
            ->where(function ($query) {
                $query->where('approve_satu', 'pending')
                    ->orWhere('approve_dua', 'pending');
            });
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
