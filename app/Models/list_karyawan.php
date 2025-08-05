<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class list_karyawan extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'id_list_perusahaan',
        'nik',
        'nik',
        'nip',
        'whatsapp',
        'nama_karyawan',
        'whatsapp',
        'divisi',
        'alamat',
        'jabatan',
        'penghasilan',
        'departement',
        'tanggal_bergabung',
        'status',
        'status_sp',
        'id_nama_bank',
        'nama_rekening',
        'nomor_bank',
        'approve_satu',
        'approve_dua',
    ];

    public function perusahaan()
    {
        return $this->hasOne(ListPerusahaan::class, 'id', 'id_list_perusahaan');
    }

    public function bank()
    {
        return $this->hasOne(bank::class, 'id', 'id_nama_bank');
    }
}
