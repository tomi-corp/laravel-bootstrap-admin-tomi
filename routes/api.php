<?php

use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\MenuController;
use App\Http\Controllers\User\PinjamanController;
use Illuminate\Support\Facades\Route;




Route::middleware(['LanguangeMiddleware'])->group(
    function () {

        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/languange/{lang}', [AuthController::class, 'lang']);
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/forgot/password', [AuthController::class, 'forgotPassword']);
        Route::post('/forgot/password/otp', [AuthController::class, 'checkOtp']);
        Route::post('/forgot/password/action', [AuthController::class, 'actionUpdate']);


        // Route::post('/bpr/logout', [BprAuthController::class, 'logout'])->name('bpr.logout');

        Route::middleware(['ApiMiddleware'])->group(
            function () {
                Route::post('/me', [AuthController::class, 'me']);
                Route::post('/verif/otp', [AuthController::class, 'verifOtp']);
                Route::post('/verif/kirim/ulang/otp', [AuthController::class, 'kirimUlangOtp']);
                Route::post('/menu', [MenuController::class, 'listMenu']);
                Route::post('/password/update', [AuthController::class, 'updatePassword']);
                Route::post('/profile/update', [AuthController::class, 'profileUpdate']);

                // pinjaman
                Route::post('/get/tenor', [PinjamanController::class, 'getTenor']);
                Route::post('/get/simulation', [PinjamanController::class, 'getSimulation']);
                Route::post('/action/pinjaman', [PinjamanController::class, 'actionPinjaman']);
                Route::post('/history/pinjaman', [PinjamanController::class, 'historyPinjam']);


                // Route::post('/profile/update', [AuthController::class, 'login']);
                // Route::post('/login', [AuthController::class, 'login']);
                // Route::post('/languange/{lang}', [AuthController::class, 'lang']);
                // Route::post('/register', [AuthController::class, 'register']);
                // Route::post('/bpr/logout', [BprAuthController::class, 'logout'])->name('bpr.logout');
            }
        );
    }
);
