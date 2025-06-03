<?php

namespace App\Exports;

use App\Models\pinjaman;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PinjamanPendingExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
{
    use Exportable;

    private $collection;

    public function __construct($collection)
    {
        $this->collection = $collection;
    }

    // set the collection of members to export
    public function collection()
    {
        return $this->collection;
    }

    // map what a single member row should look like
    // this method will iterate over each collection item
    public function map($collection): array
    {
        return [
            $collection->id_pinjaman,
            $collection->data_karyawan->nama_karyawan,
            $collection->nominal,
            $collection->tenor,
            $collection->rate,
            $collection->biaya_admin,
            $collection->iuran_anggota,
            $collection->total_biaya_admin,
            $collection->total_angsuran_perbulan,
            $collection->jumlah_yang_diterima,
        ];
    }

    public function headings(): array
    {
        return ["ID Pinjaman", "nama", "Nominal Pinjaman", "Tenor", "Rate", "Biaya Admin", "Iuran Anggota", "Total Biaya Admin", "Total Angsuran Perbulan", "Jumlah Yang Diterima"];
    }
}
