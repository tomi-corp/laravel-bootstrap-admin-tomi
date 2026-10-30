<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TomiDanaEmployeeTemplateExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'nama_karyawan',
            'tanggal_bergabung',
            'nik',
            'nip',
            'whatsapp',
            'alamat',
            'divisi',
            'departemen',
            'jabatan',
            'status',
            'status_sp',
            'nama_rekening',
            'nomor_bank',
            'max_plafon',
        ];
    }

    public function array(): array
    {
        return [[
            'Contoh Nama',
            '2026-01-31',
            '1234567890',
            'NIP001',
            '628123456789',
            'Alamat karyawan',
            'Finance',
            'Human Resources',
            'Staff',
            'active',
            'pending',
            'Nama Pemilik Rekening',
            '1234567890',
            10000000,
        ]];
    }
}
