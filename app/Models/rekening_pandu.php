<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class rekening_pandu extends Model
{
    public function bank()
    {
        return $this->hasOne(bank::class, 'id', 'bank_id');
    }
}
