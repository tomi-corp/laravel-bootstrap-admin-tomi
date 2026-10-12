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
        'event',
        'privy_id',
        'processed_by',
        'attempts',
        'error_code',
        'error_message',
        'callback_state',
        'callback_token',
        'callback_shared_data',
        'expires_at',
        'exchanged_at',
        'exchange_received_at',
        'callback_received_at',
        'processing_started_at',
        'verified_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'exchanged_at' => 'datetime',
        'exchange_received_at' => 'datetime',
        'callback_received_at' => 'datetime',
        'processing_started_at' => 'datetime',
        'verified_at' => 'datetime',
        'attempts' => 'integer',
        'callback_shared_data' => 'array',
    ];
}
