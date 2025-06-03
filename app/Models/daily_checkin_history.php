<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class daily_checkin_history extends Model
{
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    public function dailyCheckin()
    {
        return $this->hasOne(daily_checkin::class, 'id', 'daily_checkin_id');
    }
}
