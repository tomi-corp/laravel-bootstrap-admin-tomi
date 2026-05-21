<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seller extends Authenticatable
{
    protected $fillable = [
        'store_name',
        'store_slug',
        'owner_name',
        'email',
        'phone',
        'password',
        'description',
        'logo_url',
        'banner_url',
        'address',
        'city',
        'province',
        'postal_code',
        'is_active',
        'is_verified',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
