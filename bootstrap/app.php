<?php

use App\Http\Middleware\TwoFactorMiddleware;
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
            Route::middleware(['web', 'AdminMiddleware', 'TwoFactorMiddleware'])
                ->prefix('internal')
                ->name('internal.')
                ->group(base_path('routes/internal.php'));
            Route::middleware(['web', 'AdminMiddleware', 'TwoFactorMiddleware'])
                ->prefix('lender')
                ->name('lender.')
                ->group(base_path('routes/lender.php'));
            Route::middleware(['web', 'AdminMiddleware', 'TwoFactorMiddleware'])
                ->prefix('referral')
                ->name('referral.')
                ->group(base_path('routes/referral.php'));
            Route::middleware(['web', 'AdminMiddleware', 'TwoFactorMiddleware'])
                ->prefix('company')
                ->name('company.')
                ->group(base_path('routes/company.php'));
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
            Route::middleware('api')
                ->prefix('v1/api')
                ->group(base_path('routes/api.php'));
            Route::middleware('web')
                ->prefix('bta')
                ->group(base_path('routes/bta.php'));
            Route::middleware('web')
                ->prefix('seller')
                ->group(base_path('routes/seller.php'));
            Route::middleware('web')
                ->prefix('partner')
                ->group(base_path('routes/partner.php'));
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
        $middleware->trustProxies(at: '*');

        // Tambahkan CORS sebagai middleware GLOBAL (berlaku untuk SEMUA request)
        $middleware->prepend([
            \App\Http\Middleware\CorsMiddleware::class,
        ]);
        $middleware->alias([
            'LanguangeMiddleware' => \App\Http\Middleware\LanguangeMiddleware::class,
            'ApiMiddleware' => \App\Http\Middleware\ApiMiddleware::class,
            'AdminMiddleware' => \App\Http\Middleware\AdminMiddleware::class,
            'LoginAdminMiddleware' => \App\Http\Middleware\LoginAdminMiddleware::class,
            'EmailMiddleware' => \App\Http\Middleware\VerifEmailMiddleware::class,
            'WhatsappMiddleware' => \App\Http\Middleware\VerifWhatsappMiddleware::class,
            'DataKasbonMiddleware' => \App\Http\Middleware\DataKasbonMiddleware::class,
            'LimitRequestMiddleware' => \App\Http\Middleware\LimitRequestMiddelware::class,
            'SignedDocumentMiddleware' => \App\Http\Middleware\SignedDocumentMiddleware::class,
            'CenterFileMiddleware' => \App\Http\Middleware\CenterFileMiddleware::class,
            'PayrollMaintenanceMiddleware' => \App\Http\Middleware\PayrollMaintenanceMiddleware::class,
            'WeekdaySubmissionMiddleware' => \App\Http\Middleware\WeekdaySubmissionMiddleware::class,
            'TwoFactorMiddleware' =>  \App\Http\Middleware\TwoFactorMiddleware::class,
            'BtaMiddleware' =>  \App\Http\Middleware\BtaMiddleware::class,
            'BtaGuestMiddleware' =>  \App\Http\Middleware\BtaGuestMiddleware::class,
            'SellerMiddleware' =>  \App\Http\Middleware\SellerMiddleware::class,
            'SellerGuestMiddleware' =>  \App\Http\Middleware\SellerGuestMiddleware::class,
            'Cors' => \App\Http\Middleware\CorsMiddleware::class,
            'bta.type' => \App\Http\Middleware\BtaRoleMiddleware::class,

        ]);
        $middleware->validateCsrfTokens(except: [
            '/callback/peminjaman/payroll/disbursement',
            '/callback/pembayaran/tagihan/perusahaan/tomi',
            '/callback/register/privy',
            '/daftar/ekasbon',
            '/pengajuan-penghapusan-akun'
        ]);
    })


    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
