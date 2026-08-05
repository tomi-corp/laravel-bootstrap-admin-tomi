<?php

use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Guest\CallbackController;
use App\Http\Controllers\Guest\DownloadController;
use App\Http\Controllers\Guest\VerifEmailController;
use Illuminate\Support\Facades\Route;

Route::get('/confirmation/{code}', [VerifEmailController::class, 'verifEmail'])->name('verif.email');

Route::post('/callback/peminjaman/payroll/disbursement', [CallbackController::class, 'callbackDisbursement']);

Route::post('/callback/pembayaran/tagihan/perusahaan/tomi', [CallbackController::class, 'callbackPaymentInvoicing']);

Route::get('/invoicee/{encrypted}/{unique}/{idEnc}/{codePerusahaanEnc}', [DashboardController::class, 'invoicee']);

Route::get('/invoicing-layanan/{encrypted}/{unique}/{idEnc}/{codePerusahaanEnc}', [DashboardController::class, 'invoiceeLender']);

Route::post('/callback/register/privy', [CallbackController::class, 'privyRegisterCallback']);

// show document
Route::get('/preview', [AuthController::class, 'preview'])
    ->name('preview.file')
    ->middleware('SignedDocumentMiddleware');

Route::redirect('/', '/backoffice/login');

Route::get('/download/aplikasi/{id}', [DownloadController::class, 'download']);

Route::middleware('Cors')->group(function () {
    Route::post('/daftar/ekasbon', [DashboardController::class, 'registerEkasbon']);
});
