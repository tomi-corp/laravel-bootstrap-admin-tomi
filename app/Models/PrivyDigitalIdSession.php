<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrivyDigitalIdSession extends Model
{
    protected $fillable = [
        'user_id',
        'reference_number',
        'privy_session_id',
        'exchange_code',
        'session_url',
        'status',
        'expires_at',
        'exchanged_at',
    ];
}
