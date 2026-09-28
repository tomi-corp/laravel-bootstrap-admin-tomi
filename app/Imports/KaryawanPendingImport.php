<?php

namespace App\Imports;

use App\Models\amount_pinjaman;
use App\Models\bank;
use App\Models\list_karyawan;
use App\Models\ListPerusahaan;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class KaryawanPendingImport implements ToModel, WithHeadingRow
{
    use RemembersRowNumber;

    public array $processedPerusahaanIds = [];
    public array $skippedRows = [];
    public int $savedRows = 0;

    public function __construct(
        private ?int $idPerusahaan = null,
        private ?int $idLender = null,
    ) {
    }

    public function model(array $row)
    {
        $rowNumber = $this->getRowNumber();

        if ($this->isEmptyRow($row)) {
            return null;
        }

        $requiredError = $this->validateRequiredRow($row);

        if ($requiredError) {
            $this->skip($rowNumber, $row, $requiredError);
            return null;
        }

        $status = $this->normalizeStatus($row['status'] ?? $row['status_karyawan'] ?? null);
        $statusSp = $this->normalizeStatusSp($row['status_sp'] ?? null);

        if (! $status) {
            $this->skip($rowNumber, $row, 'Status karyawan harus salah satu dari: tetap, percobaan, phk, resign');
            return null;
        }

        if ($statusSp === null) {
            $this->skip($rowNumber, $row, 'Status SP harus salah satu dari: 0, 1, 2, 3');
            return null;
        }

        $lengthError = $this->validateMaxLengths($row);

        if ($lengthError) {
            $this->skip($rowNumber, $row, $lengthError);
            return null;
        }

        $companyQuery = ListPerusahaan::query()
            ->when($this->idLender, fn ($query) => $query->where('id_lender', $this->idLender));

        $listPerusahaan = $this->idPerusahaan
            ? (clone $companyQuery)->whereKey($this->idPerusahaan)->first()
            : (clone $companyQuery)->where('id_code_perusahaan', $row['code_perusahaan'])->first();

        if (! $listPerusahaan) {
            $this->skip($rowNumber, $row, 'Perusahaan tidak ditemukan atau bukan milik lender ini');
            return null;
        }

        $namaBank = $this->cleanValue($row['nama_bank'] ?? null);
        $bank = bank::query()
            ->whereRaw('LOWER(TRIM(code_bank)) = ?', [strtolower($namaBank)])
            ->orWhereRaw('LOWER(TRIM(description)) = ?', [strtolower($namaBank)])
            ->first();

        if (! $bank) {
            $this->skip($rowNumber, $row, 'Bank tidak ditemukan');
            return null;
        }

        try {
            $tanggalBergabung = $this->normalizeDate($row['tanggal_bergabung'])->format('Y-m-d');
        } catch (\Throwable $th) {
            $this->skip($rowNumber, $row, 'Tanggal bergabung tidak valid. Gunakan format YYYY-MM-DD, contoh 2024-10-21');
            return null;
        }

        $amountPinjaman = amount_pinjaman::query()
            ->where('id_perusahaan', $listPerusahaan->id)
            ->first();

        $penghasilan = $this->normalizeCurrencyValue($row['penghasilan'] ?? 0);
        $persentase = (float) ($amountPinjaman->maximal_persentase ?? 0);
        $maxPlafon = (int) (ceil(($penghasilan * ($persentase / 100)) / 100000) * 100000);

        try {
            $karyawan = list_karyawan::query()->updateOrCreate(
                [
                    'nik' => $row['nik'],
                    'id_list_perusahaan' => $listPerusahaan->id,
                ],
                [
                    'nip' => $row['nip'],
                    'whatsapp' => $this->normalizeWhatsapp($row['no_whatsapp']),
                    'nama_karyawan' => $row['nama_karyawan'],
                    'alamat' => $row['alamat_ktp'],
                    'divisi' => $row['divisi'],
                    'jabatan' => $row['jabatan'],
                    'max_plafon' => $maxPlafon,
                    'departement' => $row['departemen'],
                    'tanggal_bergabung' => $tanggalBergabung,
                    'status' => $status,
                    'status_sp' => $statusSp,
                    'id_nama_bank' => $bank->id,
                    'nama_rekening' => $row['nama_rekening'],
                    'nomor_bank' => $row['nomor_bank'],
                    'approve_satu' => 'approve',
                    'approve_dua' => 'approve',
                ]
            );
        } catch (\Throwable $th) {
            $this->skip($rowNumber, $row, $this->databaseErrorMessage($th));
            return null;
        }

        if (! in_array($listPerusahaan->id, $this->processedPerusahaanIds, true)) {
            $this->processedPerusahaanIds[] = $listPerusahaan->id;
        }

        $this->savedRows++;

        return $karyawan;
    }

    private function skip(int $rowNumber, array $row, string $reason): void
    {
        $this->skippedRows[] = [
            'row' => $rowNumber,
            'nik' => $row['nik'] ?? null,
            'reason' => $reason,
        ];
    }

    private function isEmptyRow(array $row): bool
    {
        foreach ($row as $value) {
            if ($value !== null && $this->cleanValue($value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function cleanValue($value): string
    {
        return trim(str_replace("\xc2\xa0", ' ', (string) $value));
    }

    private function validateRequiredRow(array $row): ?string
    {
        $requiredColumns = [
            'nik' => 'NIK',
            'nip' => 'NIP',
            'no_whatsapp' => 'No Whatsapp',
            'nama_karyawan' => 'Nama Karyawan',
            'alamat_ktp' => 'Alamat KTP',
            'divisi' => 'Divisi',
            'jabatan' => 'Jabatan',
            'departemen' => 'Departemen',
            'tanggal_bergabung' => 'Tanggal Bergabung',
            'nama_bank' => 'Nama Bank',
            'nama_rekening' => 'Nama Rekening',
            'nomor_bank' => 'Nomor Bank',
            'penghasilan' => 'Penghasilan',
        ];

        if (! array_key_exists('status', $row) && ! array_key_exists('status_karyawan', $row)) {
            return 'Kolom status wajib ada';
        }

        foreach ($requiredColumns as $column => $label) {
            if (! array_key_exists($column, $row) || $row[$column] === null || $this->cleanValue($row[$column]) === '') {
                return "Kolom {$label} wajib diisi";
            }
        }

        $statusValue = $row['status'] ?? $row['status_karyawan'] ?? null;
        if ($statusValue === null || $this->cleanValue($statusValue) === '') {
            return 'Kolom Status wajib diisi';
        }

        return null;
    }

    private function validateMaxLengths(array $row): ?string
    {
        $columns = [
            'nik' => 'NIK',
            'nip' => 'NIP',
            'no_whatsapp' => 'No Whatsapp',
            'nama_karyawan' => 'Nama Karyawan',
            'alamat_ktp' => 'Alamat KTP',
            'divisi' => 'Divisi',
            'jabatan' => 'Jabatan',
            'departemen' => 'Departemen',
            'nama_rekening' => 'Nama Rekening',
        ];

        foreach ($columns as $column => $label) {
            if (strlen($this->cleanValue($row[$column] ?? '')) > 255) {
                return "Kolom {$label} maksimal 255 karakter";
            }
        }

        return null;
    }

    private function databaseErrorMessage(\Throwable $th): string
    {
        $message = $th->getMessage();

        if (str_contains($message, 'Duplicate entry') && str_contains($message, 'list_karyawans_nip_unique')) {
            return 'NIP sudah terdaftar di data karyawan';
        }

        if (str_contains($message, 'Duplicate entry') && str_contains($message, 'list_karyawans_nik_unique')) {
            return 'NIK sudah terdaftar di data karyawan';
        }

        if (str_contains($message, 'Duplicate entry') && str_contains($message, 'list_karyawans_whatsapp_unique')) {
            return 'No Whatsapp sudah terdaftar di data karyawan';
        }

        if (str_contains($message, "Data truncated for column 'status_sp'")) {
            return 'Status SP harus salah satu dari: 0, 1, 2, 3';
        }

        if (str_contains($message, 'Data too long for column')) {
            return 'Ada kolom yang melebihi batas panjang database. Batas umum kolom teks adalah 255 karakter';
        }

        return 'Data gagal disimpan ke database. Cek NIK, NIP, No Whatsapp, dan format kolom sesuai template';
    }

    private function normalizeCurrencyValue($value): float
    {
        if ($value === null || $value === '') {
            return 0;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $value = $this->cleanValue($value);
        $value = str_replace(['Rp', 'rp', 'IDR', 'idr', ' '], '', $value);

        if (preg_match('/^\d{1,3}(,\d{3})+(\.\d+)?$/', $value)) {
            return (float) str_replace(',', '', $value);
        }

        if (preg_match('/^\d{1,3}(\.\d{3})+(,\d+)?$/', $value)) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
            return (float) $value;
        }

        if (str_contains($value, ',') && ! str_contains($value, '.')) {
            $value = str_replace(',', '', $value);
        }

        return (float) preg_replace('/[^0-9.]/', '', $value);
    }

    private function normalizeStatus($value): ?string
    {
        $status = strtolower($this->cleanValue($value));
        $aliases = [
            'tetap' => 'tetap',
            'permanent' => 'tetap',
            'karyawan tetap' => 'tetap',
            'percobaan' => 'percobaan',
            'probation' => 'percobaan',
            'masa percobaan' => 'percobaan',
            'phk' => 'phk',
            'resign' => 'resign',
            'mengundurkan diri' => 'resign',
        ];

        return $aliases[$status] ?? null;
    }

    private function normalizeStatusSp($value): ?string
    {
        if ($value === null || $value === '') {
            return '0';
        }

        $statusSp = $this->cleanValue($value);

        return in_array($statusSp, ['0', '1', '2', '3'], true) ? $statusSp : null;
    }

    private function normalizeDate($value): \DateTimeInterface
    {
        if ($value instanceof \DateTimeInterface) {
            return $value;
        }

        if (is_numeric($value)) {
            return Date::excelToDateTimeObject($value);
        }

        $value = $this->cleanValue($value);
        $months = [
            'januari' => 'January',
            'februari' => 'February',
            'maret' => 'March',
            'april' => 'April',
            'mei' => 'May',
            'juni' => 'June',
            'juli' => 'July',
            'agustus' => 'August',
            'september' => 'September',
            'oktober' => 'October',
            'november' => 'November',
            'desember' => 'December',
        ];

        $normalized = str_ireplace(array_keys($months), array_values($months), strtolower($value));

        return Carbon::parse($normalized);
    }

    private function normalizeWhatsapp($value): string
    {
        $value = $this->cleanValue($value);

        return substr($value, 0, 1) === '0' ? '62'.substr($value, 1) : $value;
    }
}
