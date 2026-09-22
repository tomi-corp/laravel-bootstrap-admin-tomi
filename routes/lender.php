<?php

use App\Modules\Lender\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::name('lender.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/summary', [DashboardController::class, 'summary'])->name('dashboard.summary');
    Route::get('/tomi-dana/disbursement', [DashboardController::class, 'disbursement'])->name('tomi-dana.disbursement');
    Route::get('/tomi-dana/repayment', [DashboardController::class, 'repayment'])->name('tomi-dana.repayment');
});
