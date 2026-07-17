<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class user_presence extends Model
{
    protected $table = 'user_presences';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'company_id' => 'integer',
            'user_id' => 'integer',
            'employee_id' => 'integer',
            'presence_date' => 'date',
            'clock_in_at' => 'datetime',
            'clock_out_at' => 'datetime',
            'check_in_at' => 'datetime',
            'check_out_at' => 'datetime',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(ListPerusahaan::class, 'company_id', 'id');
    }

    public function employee()
    {
        return $this->belongsTo(list_karyawan::class, 'employee_id', 'id');
    }
}
