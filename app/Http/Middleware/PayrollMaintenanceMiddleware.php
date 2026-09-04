<?php

namespace App\Http\Middleware;

use App\Models\ListPerusahaan;
use App\Models\pinjaman;
use App\Models\tenor_pinjaman;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PayrollMaintenanceMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $perusahaan = $this->resolvePerusahaan($request);

        if ($perusahaan && $this->isInPayrollMaintenanceWindow((int) $perusahaan->payment_date)) {
            return response()->json([
                'status' => 503,
                'maintenance' => true,
                'message' => 'Mohon maaf, layanan sedang dalam maintenance pada periode payroll. Silakan coba kembali setelah periode maintenance selesai.',
            ], 503);
        }

        return $next($request);
    }

    private function resolvePerusahaan(Request $request): ?ListPerusahaan
    {
        if ($request->filled('nip')) {
            $uniqueCode = explode('-', (string) $request->nip)[0] ?? null;

            if ($uniqueCode) {
                $perusahaan = ListPerusahaan::where('unique_code', $uniqueCode)->first();

                if ($perusahaan) {
                    return $perusahaan;
                }
            }
        }

        if ($request->filled('id_tenor_pinjaman')) {
            $tenor = tenor_pinjaman::with('perusahaan')->find($request->id_tenor_pinjaman);

            if ($tenor && $tenor->perusahaan) {
                return $tenor->perusahaan;
            }
        }

        $user = auth('api')->user();

        if (!$user) {
            return null;
        }

        if ($request->filled('id_pinjaman')) {
            return pinjaman::where('id', $request->id_pinjaman)
                ->where('user_id', $user->id)
                ->with('perusahaan')
                ->first()
                ?->perusahaan;
        }

        return pinjaman::where('user_id', $user->id)
            ->whereIn('status', ['on_proses', 'pending', 'approve'])
            ->with('perusahaan')
            ->latest()
            ->first()
            ?->perusahaan;
    }

    private function isInPayrollMaintenanceWindow(int $paymentDate): bool
    {
        if ($paymentDate < 1) {
            return false;
        }

        $today = Carbon::today();

        foreach ([-1, 0, 1] as $monthOffset) {
            $month = $today->copy()->addMonthsNoOverflow($monthOffset);
            $payrollDate = $month->copy()->day(min($paymentDate, $month->daysInMonth));
            $startDate = $payrollDate->copy()->subDays(7)->startOfDay();
            $endDate = $paymentDate > 25
                ? $payrollDate->copy()->endOfMonth()
                : $payrollDate->copy()->addDays(7)->endOfDay();

            if ($today->betweenIncluded($startDate, $endDate)) {
                return true;
            }
        }

        return false;
    }
}
