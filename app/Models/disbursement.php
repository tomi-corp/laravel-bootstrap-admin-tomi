<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class disbursement extends Model
{
    protected function casts(): array
    {
        return [
            'transfer_date' => 'date',
        ];
    }

    public function pinjaman()
    {
        return $this->hasOne(pinjaman::class, 'id', 'pinjaman_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
