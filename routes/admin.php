<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PengajuanPinjamanController;
use App\Http\Controllers\Admin\PpobController;
use App\Http\Controllers\MenuMobileController;
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
        // menu mobile
        Route::get('/mobile/menu', [MenuMobileController::class, 'index'])->name('admin.menumobile.index');
        Route::post('/mobile/list', [MenuMobileController::class, 'listMenu'])->name('admin.listMenu.index');
        Route::post('/mobile/menu/update', [MenuMobileController::class, 'updateMenu'])->name('admin.menumobile.update');
        Route::post('/mobile/menu/add', [MenuMobileController::class, 'addMenu'])->name('admin.menumobile.add');
        Route::post('/mobile/menu/delete', [MenuMobileController::class, 'deleteMenu'])->name('admin.menumobile.delete');
        Route::post('/mobile/menu/detail', [MenuMobileController::class, 'detailMenu'])->name('admin.menumobile.detail');
        // pengajuan pinjaman
        Route::get('/pengajuan/pinjaman', [PengajuanPinjamanController::class, 'index'])->name('admin.pengajuan.index');
        Route::post('/pengajuan/pending/table', [PengajuanPinjamanController::class, 'pendingPinjamanTable'])->name('admin.pengajuan.pending.table');
        Route::post('/pengajuan/detail', [PengajuanPinjamanController::class, 'detailPengajuan'])->name('admin.pengajuan.detail');
        Route::post('/pengajuan/verifikasi', [PengajuanPinjamanController::class, 'verifikasiPengajuan'])->name('admin.pengajuan.verifikasi');
    }
);
