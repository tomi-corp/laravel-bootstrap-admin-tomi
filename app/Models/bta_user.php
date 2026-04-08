<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class bta_user extends Authenticatable
{
    use SoftDeletes;

    // nama tabel (opsional, Laravel otomatis plural dari model)
    protected $table = 'bta_users';

    // fields yang bisa diisi mass assignment
    protected $fillable = [
        'username',
        'email',
        'password',
        'pin',
        'type',
    ];

    // fields yang disembunyikan saat serialisasi
    protected $hidden = [
        'password',
        'pin',
    ];
}
