<?php

namespace App\Models;

use App\Modules\Lender\Models\Repayment;
use Illuminate\Database\Eloquent\Model;

class invoicing_pinjaman extends Model
{
    public function pinjaman()
    {
        return $this->hasMany(pinjaman::class, 'id_perusahaan', 'id_list_perusahaan');
    }

    public function perusahaan()
    {
        return $this->hasOne(ListPerusahaan::class, 'id', 'id_list_perusahaan');
    }

    public function repayment()
    {
        return $this->hasOne(Repayment::class, 'id_invoicing', 'id');
    }
}
