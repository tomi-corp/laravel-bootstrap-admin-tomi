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
        'province_id',
        'city_id',
        'district_id',
        'village_id',
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

    public function province()
    {
        return $this->belongsTo(provinsi::class, 'province_id', 'id');
    }

    public function city()
    {
        return $this->belongsTo(kota::class, 'city_id', 'id');
    }

    public function district()
    {
        return $this->belongsTo(kecamatan::class, 'district_id', 'id');
    }

    public function village()
    {
        return $this->belongsTo(kelurahan::class, 'village_id', 'id');
    }
}
