<?php

namespace App\Imports;

use App\Models\amount_pinjaman;
use App\Models\list_karyawan;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class TomiDanaEmployeeImport implements ToCollection, WithHeadingRow
{
    public function __construct(private readonly int $companyId)
    {
    }

    public function collection(Collection $rows): void
    {
        $amount = amount_pinjaman::where('id_perusahaan', $this->companyId)->first();
        $maximalPercentage = (float) ($amount?->maximal_persentase ?? 0);

        foreach ($rows as $row) {
            if (blank($row['nama_karyawan'] ?? null)) {
                continue;
            }

            $employee = new list_karyawan();
            $employee->id_list_perusahaan = $this->companyId;
            $employee->nama_karyawan = $row['nama_karyawan'];
            $employee->tanggal_bergabung = $this->dateValue($row['tanggal_bergabung'] ?? null);
            $employee->nik = $row['nik'] ?? null;
            $employee->nip = $row['nip'] ?? null;
            $employee->whatsapp = $row['whatsapp'] ?? $row['no_whatsapp'] ?? null;
            $employee->alamat = $row['alamat'] ?? $row['alamat_ktp'] ?? null;
            $employee->divisi = $row['divisi'] ?? null;
            $employee->departement = $row['departemen'] ?? $row['departement'] ?? null;
            $employee->jabatan = $row['jabatan'] ?? null;
            $employee->status = $row['status'] ?? 'active';
            $statusSp = $row['status_sp'] ?? null;
            $employee->status_sp = $statusSp === null ? null : (string) $statusSp;
            $employee->nama_rekening = $row['nama_rekening'] ?? null;
            $employee->nomor_bank = $row['nomor_bank'] ?? null;
            $penghasilan = $this->numberValue($row['penghasilan'] ?? null);
            $employee->max_plafon = $penghasilan === null
                ? null
                : round($penghasilan * ($maximalPercentage / 100), 0);
            $employee->approve_satu = $row['approve_satu'] ?? 'pending';
            $employee->approve_dua = $row['approve_dua'] ?? 'pending';
            $employee->save();
        }
    }

    private function dateValue(mixed $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        if (is_numeric($value)) {
            return Date::excelToDateTimeObject($value)->format('Y-m-d');
        }

        return Carbon::parse($value)->format('Y-m-d');
    }

    private function numberValue(mixed $value): ?float
    {
        if (blank($value)) {
            return null;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $normalized = preg_replace('/[^0-9,.-]/', '', (string) $value);
        $normalized = str_replace('.', '', $normalized);
        $normalized = str_replace(',', '.', $normalized);

        return is_numeric($normalized) ? (float) $normalized : null;
    }
}
