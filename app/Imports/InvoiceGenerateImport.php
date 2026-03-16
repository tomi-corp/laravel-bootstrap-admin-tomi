<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Collection;

class InvoiceGenerateImport implements ToCollection, WithHeadingRow, WithCalculatedFormulas, SkipsEmptyRows
{
    public $summary = [
        'nama_perusahaan' => '', // Tambahkan ini
        'jumlah_peminjam' => 0,
        'total_pinjaman' => 0,
        'total_dibayarkan' => 0,
        'total_fee' => 0,
    ];

    public function collection(Collection $rows)
    {
        // 1. Ambil Nama Perusahaan dari baris pertama saja
        $firstRow = $rows->first();
        if ($firstRow) {
            // Sesuaikan 'tempat_bekerja' dengan heading di Excel Anda (lowercase & underscore)
            $this->summary['nama_perusahaan'] = $firstRow['tempat_bekerja'] ?? 'Tidak Diketahui';
        }

        // 2. Filter baris yang valid untuk perhitungan summary
        $validRows = $rows->filter(function ($row) {
            return !empty($row['nama']) && isset($row['jumlah_pinjaman']);
        });

        foreach ($validRows as $row) {
            $jumlahPinjaman   = $this->cleanNumber($row['jumlah_pinjaman']);
            $jumlahDibayarkan = $this->cleanNumber($row['jumlah_yang_dibayarkan']);
            $feeTomi          = $this->cleanNumber($row['fee_tomi']);

            $this->summary['jumlah_peminjam']++;
            $this->summary['total_pinjaman'] += $jumlahPinjaman;
            $this->summary['total_dibayarkan'] += $jumlahDibayarkan;
            $this->summary['total_fee'] += $feeTomi;
        }
    }

    private function cleanNumber($value)
    {
        if (is_null($value)) return 0;
        // Hapus koma jika ada (format ribuan), lalu ubah ke float
        $clean = str_replace(',', '', $value);
        return (float) $clean;
    }

    public function getSummary()
    {
        return $this->summary;
    }
}
