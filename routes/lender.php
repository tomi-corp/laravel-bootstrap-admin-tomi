<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'lender.dashboard.index')->name('index');
Route::view('/dashboard', 'lender.dashboard.index')->name('dashboard');
Route::view('/tomi-dana', 'lender.tomi-dana.index')->name('tomi-dana');
Route::view('/tomi-gaji', 'lender.tomi-gaji.index')->name('tomi-gaji');
Route::view('/tomi-invoice', 'lender.tomi-invoice.index')->name('tomi-invoice');
Route::view('/tomi-gawai', 'lender.tomi-gawai.index')->name('tomi-gawai');
