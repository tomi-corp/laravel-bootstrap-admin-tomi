<?php

use App\Modules\Lender\Controllers\DashboardController;
use App\Modules\Lender\Controllers\CompanyController;
use Illuminate\Support\Facades\Route;

Route::name('lender.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/summary', [DashboardController::class, 'summary'])->name('dashboard.summary');
    Route::get('/tomi-dana/disbursement', [DashboardController::class, 'disbursement'])->name('tomi-dana.disbursement');
    Route::get('/tomi-dana/repayment', [DashboardController::class, 'repayment'])->name('tomi-dana.repayment');
    Route::get('/list-perusahaan/cities', [CompanyController::class, 'cities'])->name('companies.cities');
    Route::get('/list-perusahaan/districts', [CompanyController::class, 'districts'])->name('companies.districts');
    Route::get('/list-perusahaan/villages', [CompanyController::class, 'villages'])->name('companies.villages');
    Route::resource('list-perusahaan', CompanyController::class)
        ->parameters([
            'list-perusahaan' => 'company',
        ])
        ->names('companies');
});
