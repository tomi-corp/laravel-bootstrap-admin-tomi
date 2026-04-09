<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BtaRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Pemakaian:
     * ->middleware('bta.role:admin')
     * ->middleware('bta.role:master,super user')
     */
    public function handle(Request $request, Closure $next, ...$types): Response
    {
        $user = auth('bta')->user();

        // kalau belum login / guard bta tidak ada user
        if (!$user) {
            return redirect()->route('bta.login');
        }

        $userType = strtolower(trim($user->type ?? ''));

        $allowedTypes = array_map(function ($type) {
            return strtolower(trim($type));
        }, $types);

        if (!in_array($userType, $allowedTypes)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
