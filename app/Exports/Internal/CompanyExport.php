<?php

namespace App\Exports\Internal;

use App\Models\ListPerusahaan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CompanyExport extends DefaultValueBinder implements FromArray, ShouldAutoSize, WithCustomValueBinder, WithHeadings, WithStyles
{
    /**
     * Header kolom sama dengan template import agar file export bisa dipakai ulang untuk import.
     */
    public const HEADINGS = [
        'kode_perusahaan',
        'pic_perusahaan',
        'pic_internal',
        'nama_perusahaan',
        'email_perusahaan',
        'telepon',
        'nomor_pks',
        'provinsi',
        'kabupaten',
        'kecamatan',
        'desa',
        'kodepos',
        'alamat_lengkap',
        'tanggal_pembayaran',
        'lender',
        'status',
    ];

    public function __construct(private bool $templateOnly = false, private ?Builder $query = null)
    {
    }

    public function headings(): array
    {
        return self::HEADINGS;
    }

    public function array(): array
    {
        if ($this->templateOnly) {
            return [];
        }

        return ($this->query ?? ListPerusahaan::query())
            ->with(['provinsi', 'kabupaten', 'kecamatan', 'desa', 'lender'])
            ->orderBy('nama')
            ->get()
            ->map(fn (ListPerusahaan $item) => [
                $this->upper($item->id_code_perusahaan),
                $this->upper($item->pic_perusahaan),
                $this->upper($item->pic_internal),
                $this->upper($item->nama),
                Str::lower((string) $item->email_perusahaan),
                (string) $item->telp,
                $this->upper($item->nomor_pks),
                $this->upper($item->provinsi?->provinsi),
                $this->upper($item->kabupaten?->kabupaten),
                $this->upper($item->kecamatan?->kecamatan),
                $this->upper($item->desa?->desa),
                (string) ($item->desa?->kodepos ?? ''),
                $this->upper($item->alamat_lengkap),
                (string) $item->payment_date,
                $this->upper($item->lender?->nama),
                $item->status === 'active' ? 'AKTIF' : 'TIDAK AKTIF',
            ])
            ->all();
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }

    /**
     * Simpan semua nilai sebagai teks agar nomor telepon tidak berubah jadi notasi ilmiah.
     */
    public function bindValue(Cell $cell, $value): bool
    {
        $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);

        return true;
    }

    private function upper(?string $value): string
    {
        return Str::upper((string) $value);
    }
}
