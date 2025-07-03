<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        using: function () {
            Route::middleware('web')
                ->prefix('backoffice')
                ->group(base_path('routes/admin.php'));
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
            Route::middleware('api')
                ->prefix('v1/api')
                ->group(base_path('routes/api.php'));
        }
    )
    // ->withRouting(
    //     web: __DIR__ . '/../routes/web.php',
    //     commands: __DIR__ . '/../routes/console.php',
    //     health: '/up',
    //     using: function () {
    //         // Route::middleware('web')
    //         //     ->prefix('lender')
    //         //     ->group(base_path('routes/bpr.php'));
    //         Route::middleware('api')
    //             ->prefix('v1')
    //             ->group(base_path('routes/api.php'));
    //     }
    // )

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'LanguangeMiddleware' => \App\Http\Middleware\LanguangeMiddleware::class,
            'ApiMiddleware' => \App\Http\Middleware\ApiMiddleware::class,
            'AdminMiddleware' => \App\Http\Middleware\AdminMiddleware::class,
            'LoginAdminMiddleware' => \App\Http\Middleware\LoginAdminMiddleware::class,
            'EmailMiddleware' => \App\Http\Middleware\VerifEmailMiddleware::class,
            'DataKasbonMiddleware' => \App\Http\Middleware\DataKasbonMiddleware::class,
            'LimitRequestMiddleware' => \App\Http\Middleware\LimitRequestMiddelware::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            '/callback/peminjaman/payroll/disbursement',
            '/callback/pembayaran/tagihan/perusahaan/tomi',
        ]);
    })


    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
