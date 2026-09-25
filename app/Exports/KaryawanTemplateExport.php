<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class KaryawanTemplateExport implements FromArray, ShouldAutoSize, WithEvents, WithHeadings
{
    public function __construct(private ?string $codePerusahaan = null)
    {
    }

    public function headings(): array
    {
        return [
            'code_perusahaan',
            'nama_karyawan',
            'nik',
            'nip',
            'no_whatsapp',
            'alamat_ktp',
            'divisi',
            'jabatan',
            'departemen',
            'tanggal_bergabung',
            'status',
            'status_sp',
            'nama_bank',
            'nama_rekening',
            'nomor_bank',
            'penghasilan',
        ];
    }

    public function array(): array
    {
        return [
            [
                $this->codePerusahaan ?: '#TomiLender-01012026-0001',
                'Budi Santoso',
                '3174010101900001',
                'EMP001',
                '081234567890',
                'Jl. Contoh No. 1',
                'Operasional',
                'Staff',
                'Finance',
                now()->format('Y-m-d'),
                'tetap',
                '0',
                'BCA',
                'Budi Santoso',
                '1234567890',
                5000000,
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('A1:P1')->getFont()->setBold(true);
                $sheet->getStyle('A1:P1')->getFill()
                    ->setFillType('solid')
                    ->getStartColor()
                    ->setARGB('FFE9ECEF');
                $sheet->freezePane('A2');
                $sheet->getStyle('C2:D1000')->getNumberFormat()->setFormatCode('@');
                $sheet->getStyle('E2:E1000')->getNumberFormat()->setFormatCode('@');
                $sheet->getStyle('O2:O1000')->getNumberFormat()->setFormatCode('@');
                $sheet->getStyle('P2:P1000')->getNumberFormat()->setFormatCode('#,##0');
            },
        ];
    }
}
