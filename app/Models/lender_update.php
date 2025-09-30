<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class lender_update extends Model
{
    protected $casts = [
        'new_data' => 'array',
    ];

    public function lender()
    {
        return $this->hasOne(lender::class, 'id', 'lender_id');
    }

    public function requester()
    {
        return $this->hasOne(Admin::class, 'id', 'requester_id');
    }

    public function first_approver()
    {
        return $this->hasOne(Admin::class, 'id', 'first_approver_id');
    }

    public function second_approver()
    {
        return $this->hasOne(Admin::class, 'id', 'second_approver_id');
    }
}
