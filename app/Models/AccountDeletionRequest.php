<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountDeletionRequest extends Model
{
    use SoftDeletes;

    protected $table = 'account_deletion_requests';

    protected $fillable = [
        'full_name',
        'email',
        'phone_number',
        'deletion_reason',
    ];
}
