<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Referral extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'national_id_number',
        'name',
        'birth_place',
        'birth_date',
        'gender',
        'blood_type',
        'address',
        'rt',
        'rw',
        'village',
        'district',
        'city',
        'province',
        'postal_code',
        'religion',
        'marital_status',
        'occupation',
        'nationality',
        'id_card_file',
        'phone_number',
        'email',
        'bank_id',
        'bank_account_number',
        'bank_account_name',
        'bank_book_file',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function bank()
    {
        return $this->belongsTo(bank::class, 'bank_id', 'id');
    }
}
