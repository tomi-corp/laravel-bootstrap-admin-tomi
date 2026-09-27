<?php

use App\Modules\Internal\Controllers\AuthController;
use App\Modules\Internal\Controllers\CompanyController;
use App\Modules\Internal\Controllers\LenderController;
use Illuminate\Support\Facades\Route;

Route::name('internal.')->group(function () {
    Route::middleware('InternalGuestMiddleware')->group(function () {
        Route::get('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/login', [AuthController::class, 'loginAction'])->name('login.action');
    });

    Route::middleware('InternalAuthMiddleware')->group(function () {
        Route::redirect('/', '/internal/companies')->name('home');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::post('/lenders/table', [LenderController::class, 'table'])->name('lenders.table');
        Route::get('/lenders/cities', [LenderController::class, 'cities'])->name('lenders.cities');
        Route::get('/lenders/districts', [LenderController::class, 'districts'])->name('lenders.districts');
        Route::get('/lenders/villages', [LenderController::class, 'villages'])->name('lenders.villages');
        Route::resource('lenders', LenderController::class)->except(['create', 'edit']);

        Route::post('/companies/table', [CompanyController::class, 'table'])->name('companies.table');
        Route::get('/companies/cities', [CompanyController::class, 'cities'])->name('companies.cities');
        Route::get('/companies/districts', [CompanyController::class, 'districts'])->name('companies.districts');
        Route::get('/companies/villages', [CompanyController::class, 'villages'])->name('companies.villages');

        Route::resource('companies', CompanyController::class);
    });
});
