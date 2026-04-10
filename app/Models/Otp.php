<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Otp extends Model
{
    protected $table = 'verif_emails'; // nama tabel custom

    protected $fillable = [
        'id_user',
        'code_rand',
        'expired_at',
    ];

    protected $dates = [
        'expired_at',
    ];

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
