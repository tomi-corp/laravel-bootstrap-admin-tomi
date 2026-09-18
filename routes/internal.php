<?php

use App\Modules\Internal\Controllers\CompanyController;
use Illuminate\Support\Facades\Route;

Route::name('internal.')->group(function () {
    Route::post('/companies/table', [CompanyController::class, 'table'])->name('companies.table');
    Route::get('/companies/cities', [CompanyController::class, 'cities'])->name('companies.cities');
    Route::get('/companies/districts', [CompanyController::class, 'districts'])->name('companies.districts');
    Route::get('/companies/villages', [CompanyController::class, 'villages'])->name('companies.villages');

    Route::resource('companies', CompanyController::class);
});
