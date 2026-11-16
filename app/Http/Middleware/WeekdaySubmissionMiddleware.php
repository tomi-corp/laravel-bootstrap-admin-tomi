<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class WeekdaySubmissionMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Carbon::today()->isWeekend()) {
            return new JsonResponse([
                'status' => 400,
                'holiday' => true,
                'message' => 'Hari ini adalah hari libur. Pengajuan hanya dapat dilakukan pada hari Senin sampai Jumat.',
            ], 400);
        }

        return $next($request);
    }
}
