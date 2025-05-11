<?php

use App\Http\Controllers\User\AuthController;
use Illuminate\Support\Facades\Route;




Route::middleware(['LanguangeMiddleware'])->group(
    function () {

        Route::post('/cek/username', [AuthController::class, 'cekUsername']);
        Route::post('/cek/referral', [AuthController::class, 'cekReff']);
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
                Route::post('/password/update', [AuthController::class, 'updatePassword']);
                Route::post('/profile/update', [AuthController::class, 'profileUpdate']);

                // Route::post('/profile/update', [AuthController::class, 'login']);
                // Route::post('/login', [AuthController::class, 'login']);
                // Route::post('/languange/{lang}', [AuthController::class, 'lang']);
                // Route::post('/register', [AuthController::class, 'register']);
                // Route::post('/bpr/logout', [BprAuthController::class, 'logout'])->name('bpr.logout');
            }
        );
    }
);
