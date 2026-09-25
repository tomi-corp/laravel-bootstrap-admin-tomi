<?php

use App\Modules\Company\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::name('company.')->group(function () {
    Route::redirect('/', '/company/dashboard')->name('home');

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Domain routes
    |--------------------------------------------------------------------------
    |
    | Tambahkan route perusahaan di sini dulu selama modulnya masih kecil.
    | Kalau domainnya sudah membesar, baru pecah ke file route per domain.
    |
    */
});
