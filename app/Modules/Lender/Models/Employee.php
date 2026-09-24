<?php

namespace App\Modules\Lender\Models;

use App\Models\bank;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

    protected $table = 'list_karyawans';

    protected $fillable = [
        'id_list_perusahaan',
        'nik',
        'nip',
        'whatsapp',
        'nama_karyawan',
        'alamat',
        'divisi',
        'jabatan',
        'max_plafon',
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

    protected $casts = [
        'tanggal_bergabung' => 'date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'id_list_perusahaan');
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(bank::class, 'id_nama_bank');
    }
}
