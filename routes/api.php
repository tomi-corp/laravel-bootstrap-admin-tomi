<?php

use App\Http\Controllers\Server\DocumentController;
use App\Http\Controllers\User\AttendanceController;
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\DailyCheckinController;
use App\Http\Controllers\User\GadgetInstallmentController;
use App\Http\Controllers\User\HistoryController;
use App\Http\Controllers\User\MenuController;
use App\Http\Controllers\User\NotificationController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\OtaController;
use App\Http\Controllers\User\PinjamanController;
use App\Http\Controllers\User\PointController;
use App\Http\Controllers\User\PrivyController;
use App\Http\Controllers\User\ProductController;
use App\Http\Controllers\User\UserPresenceController;
use Illuminate\Support\Facades\Route;

// Endpoint lama untuk akses antar-server.
Route::middleware(['CenterFileMiddleware'])->group(function () {
    Route::post('/get/pk', [DocumentController::class, 'pk']);
    Route::post('/get/image', [DocumentController::class, 'image']);
});

Route::middleware(['LanguangeMiddleware'])->group(function () {

    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/languange/{lang}', [AuthController::class, 'lang']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/forgot/password', [AuthController::class, 'forgotPassword']);
    Route::post('/forgot/password/otp', [AuthController::class, 'checkOtp']);
    Route::post('/forgot/password/action', [AuthController::class, 'actionUpdate']);

    Route::post('/menu', [MenuController::class, 'listMenu']);

    Route::middleware(['ApiMiddleware'])->group(function () {

        Route::post('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/verif/otp', [AuthController::class, 'verifOtp']);
        Route::post('/verif/otp/email', [AuthController::class, 'verifOtpEmail']);
        Route::post('/revisi/email', [AuthController::class, 'revisiEmail']);

        Route::middleware('LimitRequestMiddleware:1')->group(function () {
            Route::post('/verif/kirim/ulang/otp', [AuthController::class, 'kirimUlangOtp']);
            Route::post('/verif/kirim/ulang/otp/email', [AuthController::class, 'kirimUlangOtpEmail']);
            Route::middleware('PayrollMaintenanceMiddleware')->group(function () {
                Route::post('/action/pinjaman/otp/ulang', [PinjamanController::class, 'actionPinjamanOTPUlang']);
            });
            Route::post('/action/gadget-installments/otp/ulang', [GadgetInstallmentController::class, 'actionGadgetInstallmentOTPUlang']);
        });

        // attendance
        Route::get('/attendance/config', [AttendanceController::class, 'config']);
        Route::get('/attendance/today', [AttendanceController::class, 'today']);
        Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn']);
        Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut']);
        Route::get('/attendance/detail', [AttendanceController::class, 'detail']);
        Route::get('/attendance/photo/{attendanceRecord}/{type}', [AttendanceController::class, 'photo'])
            ->whereIn('type', ['check-in', 'check-out'])
            ->name('attendance.photo');
        Route::get('/attendance/calendar', [AttendanceController::class, 'calendar']);
        Route::post('/attendance/location/validate', [AttendanceController::class, 'validateLocation']);
        Route::post('/attendance/overtime/request', [AttendanceController::class, 'requestOvertime']);
        Route::get('/attendance/overtime/remaining', [AttendanceController::class, 'remainingOvertime']);
        Route::post('/attendance/overtime/finish', [AttendanceController::class, 'finishOvertime']);
        Route::post('/attendance/wfa/request', [AttendanceController::class, 'requestWfa']);
        Route::post('/attendance/leave/request', [AttendanceController::class, 'requestLeave']);
        Route::get('/attendance/leave/annual/remaining', [AttendanceController::class, 'remainingAnnualLeave']);
        Route::get('/attendance/approvals', [AttendanceController::class, 'approvals']);
        Route::patch('/attendance/approvals/{attendanceRecord}/status', [AttendanceController::class, 'updateApprovalStatus']);
        Route::post('/attendance/files', [AttendanceController::class, 'uploadFile']);

        Route::middleware(['EmailMiddleware', 'WhatsappMiddleware'])->group(function () {

            Route::post('/email/update', [AuthController::class, 'emailUpdate']);
            Route::post('/email/otp', [AuthController::class, 'emailOtp']);
            Route::post('/password/update', [AuthController::class, 'updatePassword']);
            Route::post('/profile/update', [AuthController::class, 'profileUpdate']);
            Route::post('/profile/update/liveness', [AuthController::class, 'profileUpdateLiveness']);

            Route::middleware('PayrollMaintenanceMiddleware')->group(function () {
                // pinjaman
                Route::post('/cek/pegawai', [PinjamanController::class, 'cekPegawai']);
                Route::post('/get/maximal/minimal', [PinjamanController::class, 'maximalMinimal']);
                Route::post('/get/tenor', [PinjamanController::class, 'getTenor']);
                Route::post('/get/simulation', [PinjamanController::class, 'getSimulation']);

                Route::post('/action/pinjaman', [PinjamanController::class, 'actionPinjaman']);
                Route::post('/action/pinjaman/otp', [PinjamanController::class, 'actionPinjamanOTP']);
                Route::get('/action/pinjaman/pk/pdf/{id?}', [PinjamanController::class, 'previewPkPdf']);
                Route::post('/action/pinjaman/show/berkas', [PinjamanController::class, 'showberkas']);
                Route::post('/action/pinjaman/ttd/berkas', [PinjamanController::class, 'tandaTangan']);
                Route::post('/action/pinjaman/show/berkas/history', [PinjamanController::class, 'showBerkasHistory']);
            });

            // cicilan gadget
            Route::post('/gadget-installments/cek/pegawai', [GadgetInstallmentController::class, 'cekPegawai']);
            Route::post('/gadget-installments/get/maximal/minimal', [GadgetInstallmentController::class, 'maximalMinimal']);
            Route::post('/gadget-installments/get/tenor', [GadgetInstallmentController::class, 'getTenor']);
            Route::post('/gadget-installments/get/simulation', [GadgetInstallmentController::class, 'getSimulation']);

            Route::post('/action/gadget-installments', [GadgetInstallmentController::class, 'actionGadgetInstallment']);
            Route::post('/action/gadget-installments/otp', [GadgetInstallmentController::class, 'actionGadgetInstallmentOTP']);
            Route::post('/action/gadget-installments/show/berkas', [GadgetInstallmentController::class, 'showberkas']);
            Route::post('/action/gadget-installments/ttd/berkas', [GadgetInstallmentController::class, 'tandaTangan']);
            Route::post('/action/gadget-installments/show/berkas/history', [GadgetInstallmentController::class, 'showBerkasHistory']);

            // daily checkin
            Route::post('/list/daily/checkin', [DailyCheckinController::class, 'getList']);
            Route::post('/do/checkin', [DailyCheckinController::class, 'doDailyCheckin']);

            // point
            Route::post('/point/history', [PointController::class, 'pointHistory']);

            // user presence
            Route::post('/presence/today', [UserPresenceController::class, 'today']);
            Route::post('/presence/history', [UserPresenceController::class, 'history']);
            Route::post('/presence/check-in', [UserPresenceController::class, 'checkIn']);
            Route::post('/presence/check-out', [UserPresenceController::class, 'checkOut']);
            Route::post('/presence/list', [UserPresenceController::class, 'list']);
            Route::post('/presence/store', [UserPresenceController::class, 'store']);
            Route::post('/presence/update', [UserPresenceController::class, 'update']);

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
        });

        // product
        Route::get('/product/list', [ProductController::class, 'list']);
        Route::post('/product/detail', [ProductController::class, 'detail']);

        // order
        Route::post('/order/store', [OrderController::class, 'store']);
    });

    // ota react native
    Route::post('/ota/check', [OtaController::class, 'checkUpdate']);
});
