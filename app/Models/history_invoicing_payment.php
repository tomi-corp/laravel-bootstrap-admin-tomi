<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class history_invoicing_payment extends Model
{
    public function perusahaan()
    {
        return $this->hasOne(ListPerusahaan::class, 'id_code_perusahaan', 'metadata_sku');
    }
}
