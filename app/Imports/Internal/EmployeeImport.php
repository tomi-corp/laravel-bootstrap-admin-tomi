<?php

namespace App\Imports\Internal;

use App\Models\bank;
use App\Models\list_karyawan;
use App\Models\ListPerusahaan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

/**
 * Upload karyawan dari halaman List Karyawan internal.
 * Format file sama dengan upload admin, tetapi hanya baris dengan code_perusahaan milik perusahaan di halaman ini
 * yang disimpan; baris perusahaan lain dilewati.
 */
class EmployeeImport implements ToModel, WithHeadingRow
{
    private int $imported = 0;

    private int $skipped = 0;

    public function __construct(private ListPerusahaan $company)
    {
    }

    public function model(array $row)
    {
        if (trim((string) ($row['code_perusahaan'] ?? '')) !== $this->company->id_code_perusahaan) {
            $this->skipped++;

            return null;
        }

        $whatsapp = (string) $row['no_whatsapp'];
        $this->imported++;

        return new list_karyawan([
            'id_list_perusahaan' => $this->company->id,
            'nik' => $row['nik'],
            'nip' => $row['nip'],
            'whatsapp' => str_starts_with($whatsapp, '0') ? '62'.substr($whatsapp, 1) : $whatsapp,
            'nama_karyawan' => $row['nama_karyawan'],
            'alamat' => $row['alamat_ktp'],
            'divisi' => $row['divisi'],
            'jabatan' => $row['jabatan'],
            'max_plafon' => $row['max_plafon'],
            'departement' => $row['departemen'],
            'tanggal_bergabung' => Date::excelToDateTimeObject($row['tanggal_bergabung'])->format('Y-m-d'),
            'status' => $row['status'],
            'status_sp' => (string) $row['status_sp'],
            'id_nama_bank' => bank::where('code_bank', $row['nama_bank'])->value('id'),
            'nama_rekening' => $row['nama_rekening'],
            'nomor_bank' => $row['nomor_bank'],
            'approve_satu' => 'pending',
            'approve_dua' => 'pending',
        ]);
    }

    public function importedCount(): int
    {
        return $this->imported;
    }

    public function skippedCount(): int
    {
        return $this->skipped;
    }
}
