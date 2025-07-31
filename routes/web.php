<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Guest\CallbackController;
use App\Http\Controllers\Guest\VerifEmailController;
use Illuminate\Support\Facades\Route;

Route::get('/confirmation/{code}', [VerifEmailController::class, 'verifEmail'])->name('verif.email');
Route::post('/callback/peminjaman/payroll/disbursement', [CallbackController::class, 'callbackDisbursement']);
Route::post('/callback/pembayaran/tagihan/perusahaan/tomi', [CallbackController::class, 'callbackPaymentInvoicing']);
Route::get('sIztJyJPErLl52H1s1zlXu5q5z5k6mMwBYC6VQlGQ65eMNGSB5u/{code}/{id}/{code_perusahaan}', [DashboardController::class, 'invoicee']);


Route::post('/callback/register/privy', [CallbackController::class, 'privyRegisterCallback']);

// Route::get('/', function () {
//     return view('welcome');
// });
