<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class user_presence extends Model
{
    use SoftDeletes;

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
            'clock_in_latitude' => 'decimal:7',
            'clock_in_longitude' => 'decimal:7',
            'clock_out_latitude' => 'decimal:7',
            'clock_out_longitude' => 'decimal:7',
            'approved_by' => 'integer',
            'approved_at' => 'datetime',
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
