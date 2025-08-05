<?php

namespace App\Imports;

use App\Models\bank;
use App\Models\list_data_ksp;
use App\Models\list_karyawan;
use App\Models\ListPerusahaan;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use \PhpOffice\PhpSpreadsheet\Shared\Date;


class KaryawanPendingImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $id = ListPerusahaan::where('id_code_perusahaan', $row['code_perusahaan'])
            ->first();
        $bank = bank::where('code_bank', $row['nama_bank'])
            ->first();

        return new list_karyawan([
            'id_list_perusahaan' => $id->id,
            'nik' => $row['nik'],
            'nip' => $row['nip'],
            'whatsapp' => $row['no_whatsapp'],
            'nama_karyawan' => $row['nama_karyawan'],
            'alamat' => $row['alamat_ktp'],
            'divisi' => $row['divisi'],
            'jabatan' => $row['jabatan'],
            'penghasilan' => $row['penghasilan'],
            'departement' => $row['departemen'],
            'tanggal_bergabung' => Date::excelToDateTimeObject($row['tanggal_bergabung'])->format('Y-m-d'),
            'status' => $row['status'],
            'status_sp' => '' . $row['status_sp'],
            'id_nama_bank' => $bank->id,
            'nama_rekening' => $row['nama_rekening'],
            'nomor_bank' => $row['nomor_bank'],
            'approve_satu' => 'pending',
            'approve_dua' => 'pending',
        ]);
    }
}
