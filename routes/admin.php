<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PpobController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'login'])->name('admin.login');
Route::get('/forgot/password', [AuthController::class, 'forgot'])->name('admin.forgot');
Route::post('/forgot/password/action', [AuthController::class, 'forgotAction'])->name('admin.forgot.action');
Route::get('/forgot/password/{token}', [AuthController::class, 'forgotUpdatePassword'])->name('admin.forgot.token');
Route::post('/forgot/password/do/action/{token}', [AuthController::class, 'forgotActionDo'])->name('admin.do.forgot.action');


Route::post('/login', [AuthController::class, 'loginAction'])->name('admin.login.action');
Route::middleware(['AdminMiddleware'])->group(
    function () {
        Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('admin.dashboard.index');
        Route::get('/ppob', [PpobController::class, 'index'])->name('admin.ppob.index');
        Route::post('/logout', [AuthController::class, 'logoutAction'])->name('admin.logout');
    }
);
