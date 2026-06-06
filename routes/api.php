<?php

    use App\Http\Controllers\Server\DocumentController;
    use App\Http\Controllers\User\AuthController;
    use App\Http\Controllers\User\DailyCheckinController;
    use App\Http\Controllers\User\HistoryController;
    use App\Http\Controllers\User\MenuController;
    use App\Http\Controllers\User\NotificationController;
    use App\Http\Controllers\User\OrderController;
    use App\Http\Controllers\User\OtaController;
    use App\Http\Controllers\User\PinjamanController;
    use App\Http\Controllers\User\PointController;
    use App\Http\Controllers\User\PrivyController;
    use App\Http\Controllers\User\ProductController;
    use App\Http\Controllers\User\ProductVariantController;
    use App\Http\Controllers\User\GadgetInstallmentController;
    use Illuminate\Support\Facades\Route;

    Route::middleware(['CenterFileMiddleware'])->group(
        function () {
            Route::post('/get/pk', [DocumentController::class, 'pk']);
            Route::post('/get/image', [DocumentController::class, 'image']);
        }
    );

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
                    Route::post('/verif/otp/email', [AuthController::class, 'verifOtpEmail']);
                    Route::post('/revisi/email', [AuthController::class, 'revisiEmail']);

                    Route::middleware('LimitRequestMiddleware:1')->group(function () {
                        Route::post('/verif/kirim/ulang/otp', [AuthController::class, 'kirimUlangOtp']);
                        Route::post('/verif/kirim/ulang/otp/email', [AuthController::class, 'kirimUlangOtpEmail']);
                        Route::post('/action/pinjaman/otp/ulang', [PinjamanController::class, 'actionPinjamanOTPUlang']);
                    });
                    Route::middleware(['EmailMiddleware', 'WhatsappMiddleware'])->group(function () {
                        Route::post('/email/update', [AuthController::class, 'emailUpdate']);
                        Route::post('/email/otp', [AuthController::class, 'emailOtp']);
                        Route::post('/password/update', [AuthController::class, 'updatePassword']);
                        Route::post('/profile/update', [AuthController::class, 'profileUpdate']);
                        Route::post('/profile/update/liveness', [AuthController::class, 'profileUpdateLiveness']);

                        // pinjaman

                        Route::post('/cek/pegawai', [PinjamanController::class, 'cekPegawai']);
                        Route::post('/get/maximal/minimal', [PinjamanController::class, 'maximalMinimal']);
                        Route::post('/get/tenor', [PinjamanController::class, 'getTenor']);

                        Route::post('/cek/pegawai', [PinjamanController::class, 'cekPegawai']);
                        Route::post('/get/maximal/minimal', [PinjamanController::class, 'maximalMinimal']);
                        Route::match(['get', 'post'], '/get/tenor', [PinjamanController::class, 'getTenor']);

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
                        // ota react native
                    });
                    // Route::post('/profile/update', [AuthController::class, 'login']);
                    // Route::post('/login', [AuthController::class, 'login']);
                    // Route::post('/languange/{lang}', [AuthController::class, 'lang']);
                    // Route::post('/register', [AuthController::class, 'register']);
                    // Route::post('/bpr/logout', [BprAuthController::class, 'logout'])->name('bpr.logout');
                    // product

                    Route::post('/ota/check', [OtaController::class, 'checkUpdate']);

                    Route::get('/product/list', [ProductController::class, 'list']);
                    Route::post('/product/detail', [ProductController::class, 'detail']);
                    Route::post('/product/store', [ProductController::class, 'store']);
                    Route::post('/product/update', [ProductController::class, 'update']);
                    Route::post('/product/delete', [ProductController::class, 'delete']);
                    // product variant
                    Route::post('/product/variant/list', [ProductVariantController::class, 'list']);
                    Route::post('/product/variant/detail', [ProductVariantController::class, 'detail']);
                    Route::post('/product/variant/store', [ProductVariantController::class, 'store']);
                    Route::post('/product/variant/update', [ProductVariantController::class, 'update']);
                    Route::post('/product/variant/delete', [ProductVariantController::class, 'delete']);

                    // order
                    Route::post('/order/store', [OrderController::class, 'store']);

                    Route::get('/gadget-installments', [GadgetInstallmentController::class, 'index']);
                    Route::post('/gadget-installments', [GadgetInstallmentController::class, 'store']);
                    Route::get('/gadget-installments/{id}', [GadgetInstallmentController::class, 'show']);
                    Route::put('/gadget-installments/{id}', [GadgetInstallmentController::class, 'update']);
                    Route::patch('/gadget-installments/{id}', [GadgetInstallmentController::class, 'update']);
                    Route::delete('/gadget-installments/{id}', [GadgetInstallmentController::class, 'destroy']);
                }
            );
        }
    );
