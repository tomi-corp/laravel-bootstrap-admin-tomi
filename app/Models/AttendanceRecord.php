<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceRecord extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'employee_id' => 'integer',
            'company_id' => 'integer',
            'date_key' => 'date',
            'check_in_at' => 'datetime',
            'check_out_at' => 'datetime',
            'overtime_requested_at' => 'datetime',
            'overtime_date' => 'date',
            'overtime_hours' => 'decimal:2',
            'overtime_actual_hours' => 'decimal:2',
            'end_overtime_at' => 'datetime',
            'wfa_requested_at' => 'datetime',
            'wfa_date' => 'date',
            'leave_at' => 'datetime',
            'leave_date' => 'date',
            'leave_end_date' => 'date',
            'last_distance' => 'decimal:2',
            'last_accuracy' => 'decimal:2',
            'last_latitude' => 'decimal:8',
            'last_longitude' => 'decimal:8',
            'approved_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function employee()
    {
        return $this->belongsTo(list_karyawan::class, 'employee_id', 'id');
    }

    public function company()
    {
        return $this->belongsTo(ListPerusahaan::class, 'company_id', 'id');
    }
}
