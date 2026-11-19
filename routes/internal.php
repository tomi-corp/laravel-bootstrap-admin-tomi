<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'internal.dashboard.index')->name('dashboard');
Route::view('/dashboard', 'internal.dashboard.index')->name('dashboard.page');
Route::view('/ppob', 'internal.ppob.index')->name('ppob');
Route::view('/companies', 'internal.companies.index')->name('companies');
Route::view('/lenders', 'internal.lenders.index')->name('lenders');
Route::view('/tomi-dana', 'internal.tomi-dana.index')->name('tomi-dana');
Route::view('/tomi-gaji', 'internal.tomi-gaji.index')->name('tomi-gaji');
Route::view('/tomi-invoice', 'internal.tomi-invoice.index')->name('tomi-invoice');
Route::view('/tomi-gawai', 'internal.tomi-gawai.index')->name('tomi-gawai');
Route::view('/daily-attendance', 'internal.daily-attendance.index')->name('daily-attendance');
Route::view('/mobile', 'internal.mobile.index')->name('mobile');
Route::view('/admin-management', 'internal.admin-management.index')->name('admin-management');
Route::view('/bank-accounts', 'internal.bank-accounts.index')->name('bank-accounts');
Route::view('/referral', 'internal.referral.index')->name('referral');
