<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;


class InvoiceGenerateDataMentah implements ToCollection, WithHeadingRow, WithCalculatedFormulas, SkipsEmptyRows
{
    public $summary = [
        'nama_perusahaan' => '',
        'jumlah_peminjam' => 0,
        'total_pinjaman' => 0,
        'total_dibayarkan' => 0,
        'total_fee' => 0,
    ];

    // 1. Tambah property untuk menampung items
    public $items;

    public function collection(Collection $rows)
    {
        // --- LOGIC SUMMARY (Existing) ---
        $firstRow = $rows->first();
        if ($firstRow) {
            $this->summary['nama_perusahaan'] = $firstRow['tempat_bekerja'] ?? 'Tidak Diketahui';
        }

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

        // --- SIMPAN ITEMS KE PROPERTY ---
        // Filter baris untuk ditampilkan (sesuai logika lama: skip header & cek kolom ke-3)
        // Karena WithHeadingRow, akses pakai key string (misal: 'nama' atau key ke-3)
        $this->items = $rows->filter(function ($row) {
            // Pastikan row tidak kosong. 
            // Ganti 'nama' dengan key heading kolom ke-3 yang ingin dicek.
            if (isset($row['tanggal_pengajuan']) && is_numeric($row['tanggal_pengajuan']) && $row['tanggal_pengajuan'] > 1) {
                // Konversi ke objek DateTime
                $dateObj = ExcelDate::excelToDateTimeObject($row['tanggal_pengajuan']);
                // Format menjadi string Y-m-d H:i:s (sesuaikan kebutuhan)
                $row['tanggal_pengajuan'] = $dateObj->format('Y-m-d');
            }
            return !empty($row['nama']);
        })->values(); // Re-index collection agar urut dari 0
    }

    private function cleanNumber($value)
    {
        if (is_null($value)) return 0;
        $clean = str_replace(',', '', $value);
        return (float) $clean;
    }

    public function getSummary()
    {
        return $this->summary;
    }

    // 2. Tambah Getter untuk mengambil items di Controller
    public function getItems()
    {
        return $this->items;
    }
}
