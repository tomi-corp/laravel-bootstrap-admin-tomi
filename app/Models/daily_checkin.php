<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class daily_checkin extends Model
{
    public function historyDailyCheckin()
    {
        return $this->hasMany(daily_checkin_history::class, 'daily_checkin_id', 'id');
    }
}
