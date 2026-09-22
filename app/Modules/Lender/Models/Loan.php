<?php

namespace App\Modules\Lender\Models;

use App\Models\kyc_user;
use App\Models\lender;
use App\Models\list_karyawan;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loan extends Model
{
    use SoftDeletes;

    protected $table = 'pinjamen';

    protected $fillable = [
        'user_id',
        'id_perusahaan',
        'id_karyawan',
        'id_lender',
        'id_kyc',
        'id_pinjaman',
        'nominal',
        'tenor',
        'rate',
        'asuransi',
        'provisi',
        'biaya_admin',
        'iuran_anggota',
        'total_biaya_admin',
        'biaya_admin_ksp',
        'dpp_adm_ksp',
        'ppn_adm_ksp',
        'biaya_admin_pandu',
        'dpp_adm_pandu',
        'ppn_adm_pandu',
        'pendapatan_bunga',
        'dpp_bunga',
        'ppn_bunga',
        'biaya_layanan',
        'total_angsuran_perbulan',
        'jumlah_yang_diterima',
        'jumlah_asuransi',
        'jumlah_provisi',
        'profesional_fee_1',
        'profesional_fee_2',
        'jumlah_profesional_1',
        'jumlah_profesional_2',
        'ksp_fee',
        'cost_tomi',
        'cost_ksp',
        'description_cost_tomi',
        'description_cost_ksp',
        'status',
        'referrence_number',
        'document_token',
        'document_name',
        'ttd',
        'status_otp',
        'note',
        'document_privy',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'id_perusahaan');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(list_karyawan::class, 'id_karyawan');
    }

    public function lender(): BelongsTo
    {
        return $this->belongsTo(lender::class, 'id_lender');
    }

    public function kyc(): BelongsTo
    {
        return $this->belongsTo(kyc_user::class, 'id_kyc');
    }
}
