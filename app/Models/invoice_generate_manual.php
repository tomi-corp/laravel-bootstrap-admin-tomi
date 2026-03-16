<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class invoice_generate_manual extends Model
{
    use HasFactory;

    protected $table = 'invoice_generate_manuals';

    protected $fillable = [
        'nama_perusahaan',
        'filename',
        'jumlah_peminjam',
        'total_pinjaman',
        'total_dibayarkan',
        'total_fee',
    ];

    protected $casts = [
        'total_pinjaman' => 'decimal:2',
        'total_dibayarkan' => 'decimal:2',
        'total_fee' => 'decimal:2',
    ];
}
