<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Ringkasan Excel generate invoice per perusahaan (satu file bisa berisi banyak perusahaan).
 * Header format KspDDT (NAMA PERUSAHAAN, TOTAL DIBAYARKAN, TOTAL FEE) maupun format
 * Generate Invoice backoffice (TEMPAT BEKERJA, JUMLAH YANG DIBAYARKAN, FEE TOMI) diterima.
 */
class GenerateInvoiceImport implements ToCollection, WithHeadingRow, WithCalculatedFormulas, SkipsEmptyRows
{
    public const COMPANY_KEYS = ['nama_perusahaan', 'tempat_bekerja', 'perusahaan', 'company'];
    public const LOAN_KEYS = ['jumlah_pinjaman', 'pinjaman', 'plafond'];
    public const PAID_KEYS = ['total_dibayarkan', 'jumlah_yang_dibayarkan', 'dibayarkan'];
    public const FEE_KEYS = ['total_fee', 'fee_tomi', 'fee'];

    protected array $summaries = [];

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $data = $row->toArray();
            $namaPerusahaan = trim((string) self::pick($data, self::COMPANY_KEYS, ''));

            if ($namaPerusahaan === '') {
                continue;
            }

            $this->summaries[$namaPerusahaan] ??= [
                'nama_perusahaan' => $namaPerusahaan,
                'jumlah_peminjam' => 0,
                'total_pinjaman' => 0,
                'total_dibayarkan' => 0,
                'total_fee' => 0,
            ];

            $this->summaries[$namaPerusahaan]['jumlah_peminjam']++;
            $this->summaries[$namaPerusahaan]['total_pinjaman'] += self::toNumber(self::pick($data, self::LOAN_KEYS));
            $this->summaries[$namaPerusahaan]['total_dibayarkan'] += self::toNumber(self::pick($data, self::PAID_KEYS));
            $this->summaries[$namaPerusahaan]['total_fee'] += self::toNumber(self::pick($data, self::FEE_KEYS));
        }
    }

    public function getSummaries(): array
    {
        return array_values($this->summaries);
    }

    /** Nilai pertama yang terisi dari beberapa kemungkinan nama header. */
    public static function pick(array $row, array $keys, $default = 0)
    {
        foreach ($keys as $key) {
            if (isset($row[$key]) && $row[$key] !== '') {
                return $row[$key];
            }
        }

        return $default;
    }

    /** Konversi angka/rupiah ("Rp 1.000.000", "2,070,000") ke numerik. */
    public static function toNumber($value): float|int
    {
        if (is_null($value) || $value === '') {
            return 0;
        }

        if (is_numeric($value)) {
            return $value + 0;
        }

        $value = str_ireplace('rp', '', (string) $value);
        $value = str_replace([' ', "\xc2\xa0"], '', $value);

        // "1.000.000,50" (format Indonesia) vs "1,000,000.50" (format Inggris)
        if (preg_match('/,\d{1,2}$/', $value)) {
            $value = str_replace(',', '.', str_replace('.', '', $value));
        } else {
            $value = str_replace(',', '', $value);
            if (substr_count($value, '.') > 1 || preg_match('/\.\d{3}$/', $value)) {
                $value = str_replace('.', '', $value);
            }
        }

        $value = preg_replace('/[^0-9.\-]/', '', $value);

        return is_numeric($value) ? $value + 0 : 0;
    }
}
