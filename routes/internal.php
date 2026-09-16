<?php

use App\Modules\Internal\Controllers\CompanyController;
use Illuminate\Support\Facades\Route;

Route::middleware(['AdminMiddleware'])->group(function () {
    Route::middleware(['TwoFactorMiddleware'])->name('internal.')->group(function () {
        Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
        Route::post('/companies/table', [CompanyController::class, 'table'])->name('companies.table');
        Route::post('/companies', [CompanyController::class, 'store'])->name('companies.store');
        Route::get('/companies/cities', [CompanyController::class, 'cities'])->name('companies.cities');
        Route::get('/companies/districts', [CompanyController::class, 'districts'])->name('companies.districts');
        Route::get('/companies/villages', [CompanyController::class, 'villages'])->name('companies.villages');
        Route::get('/companies/{company}', [CompanyController::class, 'show'])->name('companies.show');
        Route::put('/companies/{company}', [CompanyController::class, 'update'])->name('companies.update');
        Route::delete('/companies/{company}', [CompanyController::class, 'destroy'])->name('companies.destroy');
    });
});
