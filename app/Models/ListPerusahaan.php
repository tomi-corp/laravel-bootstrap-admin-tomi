<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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

    public function picInternalAdmin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
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

    public function pks()
    {
        return $this->hasMany(CompanyPks::class, 'company_id', 'id');
    }

    /** Referral perusahaan (maksimal dua: Referal 1 dan Referal 2). */
    public function referrals()
    {
        return $this->hasMany(CompanyReferral::class, 'company_id', 'id')->orderBy('position');
    }

    public function pinjamanBerjalan()
    {
        return $this->hasMany(pinjaman::class, 'id_perusahaan', 'id')
            ->where('status_otp', 'success')
            ->where('status', 'approve');
    }
}
