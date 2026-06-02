<?php

namespace App\Http\Controllers\Bta;

use App\Http\Controllers\Controller;
use App\Models\nasabah;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $hasTanggalPengajuan = Schema::hasColumn('nasabahs', 'tanggal_pengajuan');
        $hasTanggalDisbursement = Schema::hasColumn('nasabahs', 'tanggal_disbursement');
        $hasBuktiDisbursement = Schema::hasColumn('nasabahs', 'bukti_disbursement');
        $hasPersetujuanAnalyst = Schema::hasColumn('nasabahs', 'persetujuan_analyst');
        $hasPlafond = Schema::hasColumn('nasabahs', 'plafond');

        $totalNasabah = nasabah::count();
        $totalRevisi = nasabah::where('position', 'analyst')->where('status', 'revisi')->count();
        $totalAnalyst = nasabah::where('position', 'analyst')->where('status', 'pending')->count();
        $totalCredam = nasabah::where('position', 'credam')->where('status', 'pending')->count();
        $totalDitolak = nasabah::where('status', 'reject')->count();
        $totalPlafondDicairkan = $this->disbursedPlafondTotal(
            $hasBuktiDisbursement,
            $hasPersetujuanAnalyst,
            $hasPlafond
        );

        $totalSelesai = $this->cairQuery($hasTanggalDisbursement, $hasBuktiDisbursement)->count();
        $totalFinance = $this->financeQuery($hasTanggalDisbursement, $hasBuktiDisbursement)->count();

        $chartYear = Carbon::now()->year;
        $chartData = $this->monthlyChartData(
            $chartYear,
            $hasTanggalPengajuan,
            $hasTanggalDisbursement,
            $hasBuktiDisbursement
        );

        $today = Carbon::now()->translatedFormat('l, d F Y');

        return view('bta.dashboard.index', compact(
            'today',
            'totalNasabah',
            'totalRevisi',
            'totalAnalyst',
            'totalCredam',
            'totalDitolak',
            'totalPlafondDicairkan',
            'totalSelesai',
            'totalFinance',
            'chartYear'
        ) + $chartData);
    }

    private function disbursedPlafondTotal(
        bool $hasBuktiDisbursement,
        bool $hasPersetujuanAnalyst,
        bool $hasPlafond
    ): float {
        if (!$hasBuktiDisbursement || (!$hasPersetujuanAnalyst && !$hasPlafond)) {
            return 0;
        }

        $columns = ['bukti_disbursement'];

        if ($hasPersetujuanAnalyst) {
            $columns[] = 'persetujuan_analyst';
        }

        if ($hasPlafond) {
            $columns[] = 'plafond';
        }

        return nasabah::select($columns)
            ->whereNotNull('bukti_disbursement')
            ->where('bukti_disbursement', '!=', '')
            ->get()
            ->sum(function ($nasabah) use ($hasPersetujuanAnalyst, $hasPlafond) {
                if ($hasPersetujuanAnalyst && !empty($nasabah->persetujuan_analyst)) {
                    return (float) $nasabah->persetujuan_analyst;
                }

                if ($hasPlafond && !empty($nasabah->plafond)) {
                    return (float) $nasabah->plafond;
                }

                return 0;
            });
    }

    private function cairQuery(bool $hasTanggalDisbursement, bool $hasBuktiDisbursement): Builder
    {
        $query = nasabah::where('position', 'credam')->where('status', 'approve');

        if ($hasTanggalDisbursement) {
            return $query->whereNotNull('tanggal_disbursement');
        }

        if ($hasBuktiDisbursement) {
            return $query->whereNotNull('bukti_disbursement');
        }

        return $query;
    }

    private function financeQuery(bool $hasTanggalDisbursement, bool $hasBuktiDisbursement): Builder
    {
        $query = nasabah::where('position', 'credam')->where('status', 'approve');

        if ($hasTanggalDisbursement) {
            return $query->whereNull('tanggal_disbursement');
        }

        if ($hasBuktiDisbursement) {
            return $query->whereNull('bukti_disbursement');
        }

        return nasabah::whereRaw('1 = 0');
    }

    private function monthlyChartData(
        int $year,
        bool $hasTanggalPengajuan,
        bool $hasTanggalDisbursement,
        bool $hasBuktiDisbursement
    ): array
    {
        $monthLabels = [];
        $chartNasabah = array_fill(1, 12, 0);
        $chartProses = array_fill(1, 12, 0);
        $chartCair = array_fill(1, 12, 0);
        $chartDitolak = array_fill(1, 12, 0);

        for ($month = 1; $month <= 12; $month++) {
            $monthLabels[] = Carbon::create($year, $month, 1)->translatedFormat('M');
        }

        $columns = [
            'position',
            'status',
            'created_at',
        ];

        if ($hasTanggalPengajuan) {
            $columns[] = 'tanggal_pengajuan';
        }

        if ($hasTanggalDisbursement) {
            $columns[] = 'tanggal_disbursement';
        }

        if ($hasBuktiDisbursement) {
            $columns[] = 'bukti_disbursement';
        }

        $nasabahs = nasabah::select($columns)->get();

        foreach ($nasabahs as $nasabah) {
            $date = $this->chartDate($nasabah, $hasTanggalPengajuan);

            if (!$date || (int) $date->format('Y') !== $year) {
                continue;
            }

            $month = (int) $date->format('n');
            $chartNasabah[$month]++;

            if ($nasabah->status === 'reject') {
                $chartDitolak[$month]++;
                continue;
            }

            if ($this->isCair($nasabah, $hasTanggalDisbursement, $hasBuktiDisbursement)) {
                $chartCair[$month]++;
                continue;
            }

            $chartProses[$month]++;
        }

        return [
            'monthLabels' => $monthLabels,
            'chartNasabah' => array_values($chartNasabah),
            'chartProses' => array_values($chartProses),
            'chartCair' => array_values($chartCair),
            'chartDitolak' => array_values($chartDitolak),
        ];
    }

    private function chartDate($nasabah, bool $hasTanggalPengajuan): ?Carbon
    {
        $rawDate = $hasTanggalPengajuan && !empty($nasabah->tanggal_pengajuan)
            ? $nasabah->tanggal_pengajuan
            : $nasabah->created_at;

        if (!$rawDate) {
            return null;
        }

        try {
            return Carbon::parse($rawDate);
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function isCair($nasabah, bool $hasTanggalDisbursement, bool $hasBuktiDisbursement): bool
    {
        if ($nasabah->position !== 'credam' || $nasabah->status !== 'approve') {
            return false;
        }

        if ($hasTanggalDisbursement) {
            return !empty($nasabah->tanggal_disbursement);
        }

        if ($hasBuktiDisbursement) {
            return !empty($nasabah->bukti_disbursement);
        }

        return true;
    }
}
