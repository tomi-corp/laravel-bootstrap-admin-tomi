<?php

namespace App\Exports;

use App\Models\download;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DashboardReportExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function collection(): Collection
    {
        $now = Carbon::now();
        $lastMonth = $now->copy()->subMonth();

        return collect([
            ['Total Register', User::count(), 'Sepanjang waktu'],
            ['Register Bulan Ini', User::whereMonth('created_at', $now->month)
                ->whereYear('created_at', $now->year)
                ->count(), $now->translatedFormat('F Y')],
            ['Register Bulan Kemarin', User::whereMonth('created_at', $lastMonth->month)
                ->whereYear('created_at', $lastMonth->year)
                ->count(), $lastMonth->translatedFormat('F Y')],
            ['Total Unduhan', download::count(), 'Sepanjang waktu'],
            ['Unduhan Bulan Ini', download::whereMonth('created_at', $now->month)
                ->whereYear('created_at', $now->year)
                ->count(), $now->translatedFormat('F Y')],
            ['Unduhan Bulan Kemarin', download::whereMonth('created_at', $lastMonth->month)
                ->whereYear('created_at', $lastMonth->year)
                ->count(), $lastMonth->translatedFormat('F Y')],
        ]);
    }

    public function headings(): array
    {
        return ['Metrik', 'Jumlah', 'Periode'];
    }
}
