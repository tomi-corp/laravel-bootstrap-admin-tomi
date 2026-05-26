<?php

namespace App\Http\Controllers\Bta;

use App\Http\Controllers\Controller;
use App\Models\nasabah;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $totalPengajuan = nasabah::count();
        // revisi 
        // analyst
        // disetujui analyst
        // credit administrator
        // di tolak
        // selesai
        $totalPending = nasabah::where('status', 'pending')->count();
        $totalRevisi = nasabah::where('status', 'revisi')->count();
        $totalApprove = nasabah::where('status', 'approve')->count();
        $totalReject = nasabah::where('status', 'reject')->count();

        // Label dashboard
        $totalSudahAnalisa = $totalPending;
        $totalDikembalikan = $totalRevisi;
        $totalProsesRealisasi = $totalApprove;
        $totalSudahRealisasi = $totalReject;

        // Persentase
        $persenPending = $totalPengajuan > 0 ? round(($totalPending / $totalPengajuan) * 100, 1) : 0;
        $persenRevisi = $totalPengajuan > 0 ? round(($totalRevisi / $totalPengajuan) * 100, 1) : 0;
        $persenApprove = $totalPengajuan > 0 ? round(($totalApprove / $totalPengajuan) * 100, 1) : 0;
        $persenReject = $totalPengajuan > 0 ? round(($totalReject / $totalPengajuan) * 100, 1) : 0;

        $today = Carbon::now()->translatedFormat('l, d F Y');

        return view('bta.dashboard.index', compact(
            'today',
            'totalPengajuan',
            'totalSudahAnalisa',
            'totalDikembalikan',
            'totalProsesRealisasi',
            'totalSudahRealisasi',
            'totalPending',
            'totalRevisi',
            'totalApprove',
            'totalReject',
            'persenPending',
            'persenRevisi',
            'persenApprove',
            'persenReject'
        ));
    }
}
