<?php

namespace App\Imports;

use App\Models\invoicing_pinjaman;
use App\Models\pinjaman;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ExportImportData implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $datees =  Carbon::now();
        foreach ($rows as $row => $key) {
            $pinjaman = pinjaman::where('id_pinjaman', $key['id_pinjaman'])->first();
            $pinjaman->status = $key['status'];
            if ($key['status'] == "approve") {

                $invoicing = invoicing_pinjaman::whereYear('period', '=', $datees->format('Y'))
                    ->whereMonth('created_at', '=', $datees->format('m'))
                    ->first();

                if (!isset($invoicing)) {
                    $invoicing = new invoicing_pinjaman();
                    $invoicing->id_list_perusahaan = $pinjaman->id_perusahaan;
                    $invoicing->period = $datees->format('Y-m-d');
                    $invoicing->total_transaksi_approve = 1;
                    $invoicing->total_fee_base = ($pinjaman->biaya_admin / 100) / 2 * $pinjaman->nominal;
                    $invoicing->income_ksp =  $pinjaman->nominal - $pinjaman->jumlah_yang_diterima - (($pinjaman->biaya_admin / 100) / 2 * $pinjaman->nominal);
                    $invoicing->iuran_anggota = $pinjaman->iuran_anggota;
                    $invoicing->status = 'belum_lunas';
                    $invoicing->save();
                } else {
                    $invoicing->total_transaksi_approve = $invoicing->total_transaksi_approve + 1;
                    $invoicing->total_fee_base = $invoicing->total_fee_base + ($pinjaman->biaya_admin / 100) / 2 * $pinjaman->nominal;
                    $invoicing->income_ksp = $invoicing->income_ksp + ($pinjaman->nominal - $pinjaman->jumlah_yang_diterima - (($pinjaman->biaya_admin / 100) / 2 * $pinjaman->nominal));
                    $invoicing->iuran_anggota = $invoicing->iuran_anggota + $pinjaman->iuran_anggota;
                    $invoicing->status = 'belum_lunas';
                    $invoicing->save();
                }
            }
            $pinjaman->save();
        }
    }
}
