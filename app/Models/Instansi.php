<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Instansi extends Model
{
    use SoftDeletes;

    protected $fillable = ['code', 'name', 'description'];

    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }
}
