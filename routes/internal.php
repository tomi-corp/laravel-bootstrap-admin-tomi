<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\KspController;
use App\Http\Controllers\Internal\LenderController;
use App\Http\Controllers\Internal\CompanyController;

Route::view('/', 'internal.dashboard.index')->name('dashboard');
Route::view('/dashboard', 'internal.dashboard.index')->name('dashboard.page');
Route::view('/ppob', 'internal.ppob.index')->name('ppob');
Route::get('/companies', [CompanyController::class, 'index'])->name('companies');
Route::post('/companies/table', [CompanyController::class, 'listPerusahaanTable'])->name('companies.table');
Route::post('/companies/add', [CompanyController::class, 'addPerusahaan'])->name('companies.add');
Route::post('/companies/detail', [CompanyController::class, 'detailPerusahaan'])->name('companies.detail');
Route::post('/companies/update', [CompanyController::class, 'editPerusahaan'])->name('companies.update');
Route::post('/companies/delete', [CompanyController::class, 'deletePerusahaan'])->name('companies.delete');
Route::post('/companies/delete-selected', [CompanyController::class, 'deleteSelectedCompany'])->name('companies.delete-selected');
Route::get('/companies/export', [CompanyController::class, 'exportCompany'])->name('companies.export');
Route::get('/companies/import/template', [CompanyController::class, 'importTemplate'])->name('companies.import.template');
Route::post('/companies/import', [CompanyController::class, 'importCompany'])->name('companies.import');
Route::post('/companies/pks/list', [CompanyController::class, 'pksList'])->name('companies.pks.list');
Route::post('/companies/pks/delete', [CompanyController::class, 'deletePks'])->name('companies.pks.delete');
Route::get('/companies/pks/{id}/file', [CompanyController::class, 'pksFile'])->whereNumber('id')->name('companies.pks.file');
Route::post('/companies/locations/cities', [CompanyController::class, 'kabupaten'])->name('companies.locations.cities');
Route::post('/companies/locations/districts', [CompanyController::class, 'kecamatan'])->name('companies.locations.districts');
Route::post('/companies/locations/villages', [CompanyController::class, 'desa'])->name('companies.locations.villages');
Route::get('/lenders', [LenderController::class, 'index'])->name('lenders');
Route::post('/lenders/table', [LenderController::class, 'lenderTable'])->name('lenders.table');
Route::post('/lenders/add', [LenderController::class, 'addLender'])->name('lenders.add');
Route::post('/lenders/detail', [LenderController::class, 'lenderDetail'])->name('lenders.detail');
Route::post('/lenders/update', [LenderController::class, 'updateLender'])->name('lenders.update');
Route::post('/lenders/delete', [LenderController::class, 'deleteLender'])->name('lenders.delete');
Route::post('/lenders/delete-selected', [LenderController::class, 'deleteSelectedLender'])->name('lenders.delete-selected');
Route::get('/lenders/export', [LenderController::class, 'exportLender'])->name('lenders.export');
Route::get('/lenders/import/template', [LenderController::class, 'importTemplate'])->name('lenders.import.template');
Route::post('/lenders/import', [LenderController::class, 'importLender'])->name('lenders.import');
Route::post('/lenders/locations/cities', [KspController::class, 'kabupaten'])->name('lenders.locations.cities');
Route::post('/lenders/locations/districts', [KspController::class, 'kecamatan'])->name('lenders.locations.districts');
Route::post('/lenders/locations/villages', [KspController::class, 'desa'])->name('lenders.locations.villages');
Route::view('/tomi-dana', 'internal.tomi-dana.index')->name('tomi-dana');
Route::get('/tomi-dana/companies', [CompanyController::class, 'featureIndex'])
    ->defaults('featureCode', 'tomidana')->defaults('view', 'internal.tomi-dana.companies.index')
    ->name('tomi-dana.companies');
Route::post('/tomi-dana/companies/table', [CompanyController::class, 'featureTable'])
    ->defaults('featureCode', 'tomidana')
    ->name('tomi-dana.companies.table');
Route::view('/tomi-dana-plus', 'internal.tomi-dana-plus.index')->name('tomi-dana-plus');
Route::view('/tomi-gaji', 'internal.tomi-gaji.index')->name('tomi-gaji');
Route::view('/tomi-invoice', 'internal.tomi-invoice.index')->name('tomi-invoice');
Route::view('/tomi-gawai', 'internal.tomi-gawai.index')->name('tomi-gawai');
Route::view('/daily-attendance', 'internal.daily-attendance.index')->name('daily-attendance');
Route::view('/mobile', 'internal.mobile.index')->name('mobile');
Route::view('/admin-management', 'internal.admin-management.index')->name('admin-management');
Route::view('/bank-accounts', 'internal.bank-accounts.index')->name('bank-accounts');
Route::view('/referral', 'internal.referral.index')->name('referral');
