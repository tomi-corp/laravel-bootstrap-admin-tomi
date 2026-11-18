<?php

namespace App\Exports;

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DashboardReportPivotSheet implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    public function __construct(private readonly Collection $allProductRows)
    {
    }

    public function collection(): Collection
    {
        $rows = $this->allProductRows
            ->filter(fn (array $row) => ! empty($row[1]) && ! empty($row[3]))
            ->map(fn (array $row) => [
                'company' => (string) ($row[1] ?? '-'),
                'date' => CarbonImmutable::parse($row[3]),
                'count' => 1,
                'nominal' => (float) ($row[7] ?? 0),
                'interest' => (float) ($row[16] ?? 0),
                'service' => (float) ($row[32] ?? 0),
                'tomi_fee' => (float) ($row[34] ?? 0),
                'admin' => (float) ($row[24] ?? 0),
                'member_fee' => (float) ($row[33] ?? 0),
                'outstanding' => (float) ($row[12] ?? 0),
            ])
            ->groupBy('company')
            ->sortKeys();

        $output = collect();
        $grand = $this->emptyTotals();

        foreach ($rows as $company => $companyRows) {
            $companyTotals = $this->emptyTotals();

            foreach ($companyRows->groupBy(fn (array $row) => $row['date']->format('Y')) as $year => $yearRows) {
                foreach ($yearRows->groupBy(fn (array $row) => $row['date']->format('n')) as $month => $monthRows) {
                    $totals = $this->sumRows($monthRows);
                    $output->push($this->displayRow($company, (int) $year, (int) $month, $totals));
                    $companyTotals = $this->addTotals($companyTotals, $totals);
                    $grand = $this->addTotals($grand, $totals);
                }

                $output->push($this->totalRow($company, $year . ' Total', $this->sumRows($yearRows)));
            }

            $output->push($this->totalRow($company . ' Total', null, $companyTotals));
        }

        $output->push($this->totalRow('Grand Total', null, $grand));

        return $output;
    }

    public function headings(): array
    {
        return [
            'PERUSAHAAN',
            'Years (TANGGAL)',
            'Months (TANGGAL)',
            'TANGGAL',
            'Count of Nama',
            'Sum of Nominal',
            'Sum of Pendapatan Bunga KSPDDT',
            'Sum of Biaya Layanan TOMI',
            'Sum of Pendapatan Admin Tomi / Fee Tomi',
            'Sum of Biaya ADM KSPDDT',
            'Sum of Iuran Anggota',
            'Sum of Sisa Outstanding',
        ];
    }

    public function title(): string
    {
        return 'Pivot All Produk';
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();
        $lastColumn = $sheet->getHighestColumn();

        $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'ED7D31']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FFFFFF']]],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(42);
        $sheet->freezePane('A2');
        $sheet->getStyle("E2:L{$lastRow}")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("A1:L{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle("E2:L{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        for ($row = 2; $row <= $lastRow; $row++) {
            $label = (string) $sheet->getCell("A{$row}")->getValue();
            $yearLabel = (string) $sheet->getCell("B{$row}")->getValue();
            $color = null;

            if ($label === 'Grand Total') {
                $color = 'F4B183';
            } elseif (str_ends_with($label, ' Total') || str_ends_with($yearLabel, ' Total')) {
                $color = 'FCE4D6';
            }

            if ($color) {
                $sheet->getStyle("A{$row}:L{$row}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $color]],
                    'borders' => ['top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D9D9D9']]],
                ]);
            }
        }

        return [];
    }

    private function emptyTotals(): array
    {
        return ['count' => 0, 'nominal' => 0, 'interest' => 0, 'service' => 0, 'tomi_fee' => 0, 'admin' => 0, 'member_fee' => 0, 'outstanding' => 0];
    }

    private function sumRows(Collection $rows): array
    {
        $totals = $this->emptyTotals();

        foreach ($rows as $row) {
            $totals = $this->addTotals($totals, $row);
        }

        return $totals;
    }

    private function addTotals(array $left, array $right): array
    {
        foreach (array_keys($this->emptyTotals()) as $key) {
            $left[$key] += (float) ($right[$key] ?? 0);
        }

        return $left;
    }

    private function displayRow(string $company, int $year, int $month, array $totals): array
    {
        return [$company, $year, CarbonImmutable::create($year, $month, 1)->format('M'), null, $totals['count'], $totals['nominal'], $totals['interest'], $totals['service'], $totals['tomi_fee'], $totals['admin'], $totals['member_fee'], $totals['outstanding']];
    }

    private function totalRow(string $label, ?string $period, array $totals): array
    {
        return [$label, $period, null, null, $totals['count'], $totals['nominal'], $totals['interest'], $totals['service'], $totals['tomi_fee'], $totals['admin'], $totals['member_fee'], $totals['outstanding']];
    }
}
