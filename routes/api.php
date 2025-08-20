<?php

use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\DailyCheckinController;
use App\Http\Controllers\User\HistoryController;
use App\Http\Controllers\User\MenuController;
use App\Http\Controllers\User\NotificationController;
use App\Http\Controllers\User\PinjamanController;
use App\Http\Controllers\User\PointController;
use App\Http\Controllers\User\PrivyController;
use Illuminate\Support\Facades\Route;




Route::middleware(['LanguangeMiddleware'])->group(
    function () {

        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/languange/{lang}', [AuthController::class, 'lang']);
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/forgot/password', [AuthController::class, 'forgotPassword']);
        Route::post('/forgot/password/otp', [AuthController::class, 'checkOtp']);
        Route::post('/forgot/password/action', [AuthController::class, 'actionUpdate']);
        //    menu
        Route::post('/menu', [MenuController::class, 'listMenu']);

        // Route::post('/bpr/logout', [BprAuthController::class, 'logout'])->name('bpr.logout');

        Route::middleware(['ApiMiddleware'])->group(
            function () {
                Route::post('/me', [AuthController::class, 'me']);
                Route::post('/logout', [AuthController::class, 'logout']);
                Route::post('/verif/otp', [AuthController::class, 'verifOtp']);
                Route::middleware('LimitRequestMiddleware:1')->group(function () {
                    Route::post('/verif/kirim/ulang/otp', [AuthController::class, 'kirimUlangOtp']);
                    Route::post('/action/pinjaman/otp/ulang', [PinjamanController::class, 'actionPinjamanOTPUlang']);
                });
                Route::middleware(['EmailMiddleware'])->group(function () {
                    Route::post('/email/update', [AuthController::class, 'emailUpdate']);
                    Route::post('/email/otp', [AuthController::class, 'emailOtp']);
                    Route::post('/password/update', [AuthController::class, 'updatePassword']);
                    Route::post('/profile/update', [AuthController::class, 'profileUpdate']);

                    // pinjaman

                    Route::post('/cek/pegawai', [PinjamanController::class, 'cekPegawai']);
                    Route::post('/get/maximal/minimal', [PinjamanController::class, 'maximalMinimal']);
                    Route::post('/get/tenor', [PinjamanController::class, 'getTenor']);

                    Route::post('/get/simulation', [PinjamanController::class, 'getSimulation']);

                    Route::post('/action/pinjaman', [PinjamanController::class, 'actionPinjaman']);
                    Route::post('/action/pinjaman/otp', [PinjamanController::class, 'actionPinjamanOTP']);
                    Route::post('/action/pinjaman/show/berkas', [PinjamanController::class, 'showberkas']);
                    Route::post('/action/pinjaman/ttd/berkas', [PinjamanController::class, 'tandaTangan']);

                    Route::post('/action/pinjaman/show/berkas/history', [PinjamanController::class, 'showBerkasHistory']);


                    // daily checkin
                    Route::post('/list/daily/checkin', [DailyCheckinController::class, 'getList']);
                    Route::post('/do/checkin', [DailyCheckinController::class, 'doDailyCheckin']);

                    // point 
                    Route::post('/point/history', [PointController::class, 'pointHistory']);

                    // notification
                    Route::post('/notification', [NotificationController::class, 'listNotif']);
                    Route::post('/notification/read', [NotificationController::class, 'readNotif']);
                    Route::post('/notification/delete', [NotificationController::class, 'deleteNOtif']);

                    // history
                    Route::post('/history', [HistoryController::class, 'history']);
                    Route::post('/history/detail', [HistoryController::class, 'historyDetail']);

                    // privy
                    Route::post('/register/privy', [PrivyController::class, 'registerPrivy']);
                    Route::post('/register/privy/resend', [PrivyController::class, 'resendRegister']);
                    Route::post('/register/privy/check', [PrivyController::class, 'cekStatusPrivy']);

                    // Route::post('/testing/api/privy', [PinjamanController::class, 'testingApi']);

                });
                // Route::post('/profile/update', [AuthController::class, 'login']);
                // Route::post('/login', [AuthController::class, 'login']);
                // Route::post('/languange/{lang}', [AuthController::class, 'lang']);
                // Route::post('/register', [AuthController::class, 'register']);
                // Route::post('/bpr/logout', [BprAuthController::class, 'logout'])->name('bpr.logout');
            }
        );
    }
);
