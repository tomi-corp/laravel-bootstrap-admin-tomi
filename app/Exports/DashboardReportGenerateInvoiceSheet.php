<?php

namespace App\Exports;

use App\Models\invoice_generate_manual;
use App\Services\Insight\InsightPeriod;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Sheet invoice hasil Generate Invoice TomiDana (upload Excel pencairan) pada periode laporan.
 */
class DashboardReportGenerateInvoiceSheet implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    public function __construct(private readonly InsightPeriod $period)
    {
    }

    public function collection(): Collection
    {
        $invoices = invoice_generate_manual::query()
            ->whereBetween('created_at', [$this->period->start(), $this->period->end()])
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $rows = $invoices->values()->map(fn ($invoice, $index) => [
            $index + 1,
            CarbonImmutable::parse($invoice->created_at)->toDateString(),
            strtoupper((string) $invoice->nama_perusahaan),
            $invoice->filename,
            (int) $invoice->jumlah_peminjam,
            (float) $invoice->total_pinjaman,
            (float) $invoice->total_dibayarkan,
            (float) $invoice->total_fee,
            strtoupper((string) ($invoice->status ?: '-')),
        ]);

        return $rows->push([
            'Grand Total',
            null,
            null,
            null,
            (int) $invoices->sum('jumlah_peminjam'),
            (float) $invoices->sum('total_pinjaman'),
            (float) $invoices->sum('total_dibayarkan'),
            (float) $invoices->sum('total_fee'),
            null,
        ]);
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal Generate',
            'Nama Perusahaan',
            'File Sumber',
            'Jumlah Peminjam',
            'Total Pinjaman',
            'Total Dibayarkan',
            'Total Fee',
            'Status',
        ];
    }

    public function title(): string
    {
        return 'Generate Invoice';
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();

        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'ED7D31']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->freezePane('A2');
        $sheet->getStyle("E2:H{$lastRow}")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("A{$lastRow}:I{$lastRow}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F4B183']],
        ]);

        return [];
    }
}
