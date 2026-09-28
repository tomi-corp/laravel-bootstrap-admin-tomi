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
        $codePerusahaan = $this->codePerusahaan ?: '#TOMILENDER-'.now()->format('dmY').'-0001';
        $names = [
            'Ayu Purnama',
            'Bayu Maulana',
            'Citra Lestari',
            'Dedi Saputra',
            'Eka Pratama',
            'Fitri Handayani',
            'Gilang Ramadhan',
            'Hana Safitri',
            'Indra Wijaya',
            'Joko Santoso',
        ];
        $divisions = ['Operasional', 'Keuangan', 'Sales', 'Human Capital', 'Teknologi'];
        $jobs = ['Staff', 'Senior Staff', 'Supervisor', 'Officer', 'Analyst'];
        $departments = ['Operations', 'Finance', 'Sales', 'HR', 'IT'];
        $banks = ['BCA', 'Mandiri', 'BRI', 'BNI', 'CIMB Niaga'];
        $rows = [];

        for ($i = 1; $i <= 100; $i++) {
            $index = ($i - 1) % count($names);
            $name = $names[$index];

            $rows[] = [
                $codePerusahaan,
                $name,
                '3174'.str_pad((string) $i, 12, '0', STR_PAD_LEFT),
                'EMP'.str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                '0812'.str_pad((string) (600000000 + ($i * 7919)), 8, '0', STR_PAD_LEFT),
                'Jl. Dummy Karyawan No. '.$i.', Jakarta',
                $divisions[$index % count($divisions)],
                $jobs[$index % count($jobs)],
                $departments[$index % count($departments)],
                now()->subDays($i * 17)->format('Y-m-d'),
                $i % 17 === 0 ? 'percobaan' : 'tetap',
                (string) ($i % 4),
                $banks[$index % count($banks)],
                $name,
                '100'.str_pad((string) ($i * 7919), 7, '0', STR_PAD_LEFT),
                5000000 + (($i % 10) * 1000000),
            ];
        }

        return $rows;
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
