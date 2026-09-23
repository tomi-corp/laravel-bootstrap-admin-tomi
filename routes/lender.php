<?php

use App\Modules\Lender\Controllers\DashboardController;
use App\Modules\Lender\Controllers\CompanyController;
use App\Modules\Lender\Controllers\AuthController;
use App\Modules\Lender\Controllers\DisbursementController;
use App\Modules\Lender\Controllers\LoanController;
use App\Modules\Lender\Controllers\RepaymentController;
use Illuminate\Support\Facades\Route;

Route::name('lender.')->group(function () {
    Route::middleware('LenderGuestMiddleware')->group(function () {
        Route::redirect('/', '/lender/login');
        Route::get('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/login', [AuthController::class, 'authenticate'])->name('login.action');
        Route::get('/forgot-password', [AuthController::class, 'forgot'])->name('forgot');
        Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('forgot.action');
        Route::get('/reset-password/{token}', [AuthController::class, 'reset'])->name('reset');
        Route::post('/reset-password/{token}', [AuthController::class, 'updatePassword'])->name('reset.action');
    });

    Route::middleware('LenderMiddleware')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/summary', [DashboardController::class, 'summary'])->name('dashboard.summary');
        Route::get('/tomi-dana/disbursement', [DashboardController::class, 'disbursement'])->name('tomi-dana.disbursement');
        Route::get('/tomi-dana/repayment', [DashboardController::class, 'repayment'])->name('tomi-dana.repayment');
        Route::resource('pinjaman', LoanController::class)
            ->parameters([
                'pinjaman' => 'loan',
            ])
            ->names('loans');
        Route::resource('disbursement', DisbursementController::class)
            ->parameters([
                'disbursement' => 'disbursement',
            ])
            ->names('disbursements');
        Route::resource('repayment', RepaymentController::class)
            ->parameters([
                'repayment' => 'repayment',
            ])
            ->names('repayments');
        Route::get('/list-perusahaan/cities', [CompanyController::class, 'cities'])->name('companies.cities');
        Route::get('/list-perusahaan/districts', [CompanyController::class, 'districts'])->name('companies.districts');
        Route::get('/list-perusahaan/villages', [CompanyController::class, 'villages'])->name('companies.villages');
        Route::resource('list-perusahaan', CompanyController::class)
            ->parameters([
                'list-perusahaan' => 'company',
            ])
            ->names('companies');
    });
});
