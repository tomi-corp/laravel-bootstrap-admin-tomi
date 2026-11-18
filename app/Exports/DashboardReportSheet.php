<?php

namespace App\Exports;

use App\Services\Insight\InsightPeriod;
use App\Services\Insight\InsightService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class DashboardReportSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    public function __construct(
        private readonly string $scope,
        private readonly string $sheetTitle,
        private readonly InsightService $insight,
        private readonly InsightPeriod $period,
    ) {
    }

    public function collection(): Collection
    {
        $data = $this->scope === 'overview'
            ? $this->insight->overview($this->period, null)
            : $this->insight->product($this->scope, $this->period, null);

        $rows = collect($data['kpis'] ?? [])->map(fn (array $kpi) => [
            $kpi['label'],
            $kpi['value'],
            $kpi['format'] ?? 'num',
            $this->period->label,
        ]);

        if ($this->scope === 'overview') {
            $rows = $rows->concat(
                collect($data['products'] ?? [])->flatMap(fn (array $product) => [
                    [$product['name'] . ' - Dana tersalurkan', $product['disbursed'], 'rp', $this->period->label],
                    [$product['name'] . ' - NOA penyaluran', $product['count'], 'num', $this->period->label],
                    [$product['name'] . ' - Sisa pembiayaan', $product['outstanding'], 'rp', $this->period->label],
                ])
            );
        }

        return $rows;
    }

    public function headings(): array
    {
        return ['Metrik', 'Nilai', 'Format', 'Periode'];
    }

    public function title(): string
    {
        return $this->sheetTitle;
    }
}
