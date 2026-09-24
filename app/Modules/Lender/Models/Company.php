<?php

namespace App\Modules\Lender\Models;

use App\Models\ListPerusahaan;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Company extends ListPerusahaan
{
    protected $table = 'list_perusahaans';

    public function loanTenors(): HasMany
    {
        return $this->hasMany(LoanTenor::class, 'id_perusahaan');
    }

    public function loanAmount(): HasOne
    {
        return $this->hasOne(LoanAmount::class, 'id_perusahaan');
    }
}
