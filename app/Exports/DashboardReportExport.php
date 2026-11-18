<?php

namespace App\Exports;

use App\Services\Insight\InsightPeriod;
use App\Services\Insight\InsightService;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DashboardReportExport implements WithMultipleSheets
{
    public function __construct(
        private readonly InsightService $insight,
        private readonly InsightPeriod $period,
    ) {
    }

    public function sheets(): array
    {
        $allProducts = new DashboardReportSheet('overview', 'All Produk', $this->insight, $this->period);
        $allProductRows = $allProducts->collection();

        return [
            new DashboardReportPivotSheet($allProductRows),
            new DashboardReportSheet('overview', 'All Produk', $this->insight, $this->period, $allProductRows),
            new DashboardReportSheet('tomidana', 'TomiDana', $this->insight, $this->period),
            new DashboardReportSheet('tomigaji', 'TomiGaji', $this->insight, $this->period),
            new DashboardReportSheet('tomiinvoice', 'TomiInvoice', $this->insight, $this->period),
        ];
    }
}
