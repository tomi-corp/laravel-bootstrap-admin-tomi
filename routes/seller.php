<?php

use App\Http\Controllers\Seller\AuthController;
use App\Http\Controllers\Seller\ProductController;
use App\Http\Controllers\Seller\DashboardController;
use App\Http\Controllers\Seller\OrderController;
use Illuminate\Support\Facades\Route;

Route::middleware(['SellerGuestMiddleware'])->group(function () {
    Route::get('/login', [AuthController::class, 'login'])
        ->name('seller.login');

    Route::post('/login', [AuthController::class, 'loginAction'])
        ->name('seller.login.action');
});

Route::middleware(['SellerMiddleware'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('seller.logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('seller.dashboard');

    Route::get('/electronic-installment', [ProductController::class, 'index'])
        ->name('seller.electronic-installment.index');

    Route::get('/electronic-installment/products/create', [ProductController::class, 'create'])
        ->name('seller.electronic-installment.products.create');

    Route::get('/electronic-installment/products/template', [ProductController::class, 'downloadTemplate'])
        ->name('seller.electronic-installment.products.template');

    Route::post('/electronic-installment/products', [ProductController::class, 'store'])
        ->name('seller.electronic-installment.products.store');

    Route::get('/electronic-installment/products/import', [ProductController::class, 'importProductPage'])
        ->name('seller.electronic-installment.products.import.page');

    Route::post('/electronic-installment/products/import', [ProductController::class, 'importProducts'])
        ->name('seller.electronic-installment.products.import');

    Route::get('/electronic-installment/products/{id}/edit', [ProductController::class, 'edit'])
        ->name('seller.electronic-installment.products.edit');

    Route::put('/electronic-installment/products/{id}', [ProductController::class, 'update'])
        ->name('seller.electronic-installment.products.update');

    Route::delete('/electronic-installment/products/{id}', [ProductController::class, 'destroy'])
        ->name('seller.electronic-installment.products.delete');

    Route::get('/gadget-installment', [ProductController::class, 'index'])
        ->name('seller.gadget-installment.index');

    Route::get('/electronic-installment/orders', [OrderController::class, 'index'])
        ->name('seller.electronic-installment.orders.index');

    Route::get('/electronic-installment/orders/{id}', [OrderController::class, 'show'])
        ->name('seller.electronic-installment.orders.show');

    Route::put('/electronic-installment/orders/{id}/status', [OrderController::class, 'updateStatus'])
        ->name('seller.electronic-installment.orders.update-status');

    Route::post('/electronic-installment/orders/{id}/approve', [OrderController::class, 'approve'])
        ->name('seller.electronic-installment.orders.approve');

    Route::delete('/electronic-installment/orders/{id}', [OrderController::class, 'delete'])
        ->name('seller.electronic-installment.orders.delete');

    Route::redirect('/cicilan-elektronik', '/seller/electronic-installment');
    Route::redirect('/cicilan-gadget', '/seller/gadget-installment');
    Route::redirect('/cicilan-gedget', '/seller/gadget-installment');
});
