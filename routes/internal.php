<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\KspController;
use App\Http\Controllers\Internal\LenderController;

Route::view('/', 'internal.dashboard.index')->name('dashboard');
Route::view('/dashboard', 'internal.dashboard.index')->name('dashboard.page');
Route::view('/ppob', 'internal.ppob.index')->name('ppob');
Route::view('/companies', 'internal.companies.index')->name('companies');
Route::get('/lenders', [LenderController::class, 'index'])->name('lenders');
Route::post('/lenders/table', [LenderController::class, 'lenderTable'])->name('lenders.table');
Route::post('/lenders/add', [LenderController::class, 'addLender'])->name('lenders.add');
Route::post('/lenders/detail', [LenderController::class, 'lenderDetail'])->name('lenders.detail');
Route::post('/lenders/update', [LenderController::class, 'updateLender'])->name('lenders.update');
Route::post('/lenders/delete', [LenderController::class, 'deleteLender'])->name('lenders.delete');
Route::post('/lenders/locations/cities', [KspController::class, 'kabupaten'])->name('lenders.locations.cities');
Route::post('/lenders/locations/districts', [KspController::class, 'kecamatan'])->name('lenders.locations.districts');
Route::post('/lenders/locations/villages', [KspController::class, 'desa'])->name('lenders.locations.villages');
Route::view('/tomi-dana', 'internal.tomi-dana.index')->name('tomi-dana');
Route::view('/tomi-dana-plus', 'internal.tomi-dana-plus.index')->name('tomi-dana-plus');
Route::view('/tomi-gaji', 'internal.tomi-gaji.index')->name('tomi-gaji');
Route::view('/tomi-invoice', 'internal.tomi-invoice.index')->name('tomi-invoice');
Route::view('/tomi-gawai', 'internal.tomi-gawai.index')->name('tomi-gawai');
Route::view('/daily-attendance', 'internal.daily-attendance.index')->name('daily-attendance');
Route::view('/mobile', 'internal.mobile.index')->name('mobile');
Route::view('/admin-management', 'internal.admin-management.index')->name('admin-management');
Route::view('/bank-accounts', 'internal.bank-accounts.index')->name('bank-accounts');
Route::view('/referral', 'internal.referral.index')->name('referral');
