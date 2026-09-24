<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LenderOfficer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'lender_id',
        'position',
        'first_name',
        'last_name',
        'nik',
        'place_of_birth',
        'date_of_birth',
        'gender',
        'ktp_address',
        'rt',
        'rw',
        'province_id',
        'city_id',
        'district_id',
        'village_id',
        'postal_code',
        'phone_number',
        'email',
        'ktp_file_path',
        'appointment_reference_number',
        'started_at',
        'ended_at',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'started_at' => 'date',
        'ended_at' => 'date',
    ];

    public function lender(): BelongsTo
    {
        return $this->belongsTo(lender::class, 'lender_id');
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(provinsi::class, 'province_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(kota::class, 'city_id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(kecamatan::class, 'district_id');
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(kelurahan::class, 'village_id');
    }
}
