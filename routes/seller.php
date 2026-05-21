<?php

use App\Http\Controllers\Seller\AuthController;
use App\Http\Controllers\Seller\ElectronicInstallmentController;
use App\Http\Controllers\Seller\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'login'])
    ->name('seller.login');

Route::post('/login', [AuthController::class, 'loginAction'])
    ->name('seller.login.action');

Route::get('/logout', [AuthController::class, 'logout'])
    ->name('seller.logout');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('seller.dashboard');

Route::get('/electronic-installment', [ElectronicInstallmentController::class, 'index'])
    ->name('seller.electronic-installment.index');

Route::get('/electronic-installment/products/create', [ElectronicInstallmentController::class, 'createProduct'])
    ->name('seller.electronic-installment.products.create');

Route::get('/electronic-installment/products/template', [ElectronicInstallmentController::class, 'downloadTemplate'])
    ->name('seller.electronic-installment.products.template');

Route::post('/electronic-installment/products', [ElectronicInstallmentController::class, 'storeProduct'])
    ->name('seller.electronic-installment.products.store');

Route::get('/electronic-installment/products/import', [ElectronicInstallmentController::class, 'importProductPage'])
    ->name('seller.electronic-installment.products.import.page');

Route::post('/electronic-installment/products/import', [ElectronicInstallmentController::class, 'importProducts'])
    ->name('seller.electronic-installment.products.import');

Route::get('/electronic-installment/products/{id}/edit', [ElectronicInstallmentController::class, 'editProduct'])
    ->name('seller.electronic-installment.products.edit');

Route::put('/electronic-installment/products/{id}', [ElectronicInstallmentController::class, 'updateProduct'])
    ->name('seller.electronic-installment.products.update');

Route::delete('/electronic-installment/products/{id}', [ElectronicInstallmentController::class, 'deleteProduct'])
    ->name('seller.electronic-installment.products.delete');

Route::get('/gadget-installment', [ElectronicInstallmentController::class, 'index'])
    ->name('seller.gadget-installment.index');

Route::redirect('/cicilan-elektronik', '/seller/electronic-installment');
Route::redirect('/cicilan-gadget', '/seller/gadget-installment');
Route::redirect('/cicilan-gedget', '/seller/gadget-installment');
