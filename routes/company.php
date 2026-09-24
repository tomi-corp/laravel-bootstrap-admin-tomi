<?php

use App\Modules\Company\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::name('company.')->group(function () {
    Route::redirect('/', '/perusahaan/dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
