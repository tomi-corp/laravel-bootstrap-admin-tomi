<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class user_presence extends Model
{
    protected $table = 'user_presences';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'presence_date' => 'date',
            'check_in_at' => 'datetime',
            'check_out_at' => 'datetime',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
