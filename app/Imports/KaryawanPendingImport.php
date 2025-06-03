<?php

namespace App\Imports;

use App\Models\list_data_ksp;
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
        $id = ListPerusahaan::where('id_code_perusahaan', $row['code_perusahaan'])->first();
        return new list_data_ksp([
            'id_list_perusahaan' => $id->id,
            'nik' => $row['nik'],
            'nip' => $row['nip'],
            'whatsapp' => $row['whatsapp'],
            'nama_karyawan' => $row['nama_karyawan'],
            'alamat' => $row['alamat'],
            'jabatan' => $row['jabatan'],
            'penghasilan' => $row['penghasilan'],
            'departement' => $row['departement'],
            'tanggal_bergabung' => Date::excelToDateTimeObject($row['tanggal_bergabung'])->format('Y-m-d'),
            'status' => $row['status'],
            'approve_satu' => 'pending',
            'approve_dua' => 'pending',
        ]);
    }
}
