<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DataKasbonMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // return $next($request);
        $user = User::where('id', auth('api')->user()->id)->first();
        if ($user->nip == null || $user->nama_perusahaan == null || $user->jabatan == null || $user->penghasilan == null || $user->slip_gaji == null || $user->ktp == null || $user->selfie_ktp == null) {
            return response()->json([
                'code' => 400,
                'message' => 'Oops, lengkapi data terlebih dahulu yuk'
            ], 400);
        } else {
            return $next($request);
        }
    }
}
