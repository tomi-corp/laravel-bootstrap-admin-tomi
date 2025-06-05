<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class list_data_ksp extends Model
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
        'approve_satu',
        'approve_dua',
    ];

    public function perusahaan()
    {
        return $this->hasOne(ListPerusahaan::class, 'id', 'id_list_perusahaan');
    }
}
