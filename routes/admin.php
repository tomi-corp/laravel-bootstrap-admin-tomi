<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CostController;
use App\Http\Controllers\Admin\DailyCheckinController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KspController;
use App\Http\Controllers\Admin\LenderController;
use App\Http\Controllers\Admin\MenuMobileController;
use App\Http\Controllers\Admin\PpobController;
use App\Http\Controllers\Admin\ProfileController;
use Illuminate\Support\Facades\Route;



Route::middleware(['LoginAdminMiddleware'])->group(
    function () {
        Route::get('/cek/test', [AuthController::class, 'test']);
        Route::get('/login', [AuthController::class, 'login'])->name('admin.login');
        Route::get('/forgot/password', [AuthController::class, 'forgot'])->name('admin.forgot');
        Route::post('/forgot/password/action', [AuthController::class, 'forgotAction'])->name('admin.forgot.action');
        Route::get('/forgot/password/{token}', [AuthController::class, 'forgotUpdatePassword'])->name('admin.forgot.token');
        Route::post('/forgot/password/do/action/{token}', [AuthController::class, 'forgotActionDo'])->name('admin.do.forgot.action');
        Route::post('/login', [AuthController::class, 'loginAction'])->name('admin.login.action');
    }
);


Route::middleware(['AdminMiddleware'])->group(
    function () {
        Route::post('/profile/confirmation/store', [ProfileController::class, 'confirmationCodeInput'])->name('admin.two-factor-auth.confirm.store');
        Route::get('/profile/confirmation/code', [ProfileController::class, 'confirmationCode'])->name('admin.profile.confirmationCode');

        Route::middleware(['TwoFactorMiddleware'])->group(
            function () {

                Route::get('/test', [DashboardController::class, 'test']);
                Route::get('/profile', [ProfileController::class, 'index'])->name('admin.profile.index');
                Route::post('/profile/update', [ProfileController::class, 'updateProfile'])->name('admin.profile.update');
                Route::post('/profile/enableTwoFactor', [ProfileController::class, 'enableTwoFactor'])->name('admin.profile.enableTwoFactor');



                Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('admin.dashboard.index');
                // ppob
                Route::get('/ppob', [PpobController::class, 'index'])->name('admin.ppob.index');
                Route::get('/ppob/transaction/komisi', [PpobController::class, 'komisiIndex'])->name('admin.ppob.komisi');
                Route::post('/ppob/transaction/komisi/summary/all', [PpobController::class, 'komisiSummaryAll'])->name('admin.ppob.komisi.summary.all');
                Route::get('/ppob/transaction/prepaid', [PpobController::class, 'prepaidIndex'])->name('admin.ppob.prepaid');
                Route::get('/ppob/transaction/postpaid', [PpobController::class, 'postpaidIndex'])->name('admin.ppob.postpaid');
                Route::post('/ppob/transaction/summary/prepaid', [PpobController::class, 'prepaidTansactionSummary'])->name('admin.ppob.prepaid.summary');
                Route::post('/ppob/transaction/summary/postpad', [PpobController::class, 'postpaidTransactionSummary'])->name('admin.ppob.postpaid.summary');
                Route::post('/ppob/transacion/prepaid/table', [PpobController::class, 'prepaidTransactionTable'])->name('admin.ppob.prepaid.table');
                Route::post('/ppob/transacion/postpaid/table', [PpobController::class, 'postpaidTransactionTable'])->name('admin.ppob.postpaid.table');

                Route::post('/logout', [AuthController::class, 'logoutAction'])->name('admin.logout');
                // menu mobile
                Route::get('/mobile/menu', [MenuMobileController::class, 'index'])->name('admin.menumobile.index');
                Route::post('/mobile/list', [MenuMobileController::class, 'listMenu'])->name('admin.listMenu.index');
                Route::post('/mobile/menu/update', [MenuMobileController::class, 'updateMenu'])->name('admin.menumobile.update');
                Route::post('/mobile/menu/add', [MenuMobileController::class, 'addMenu'])->name('admin.menumobile.add');
                Route::post('/mobile/menu/delete', [MenuMobileController::class, 'deleteMenu'])->name('admin.menumobile.delete');
                Route::post('/mobile/menu/detail', [MenuMobileController::class, 'detailMenu'])->name('admin.menumobile.detail');
                // KSP => Perusahaan
                Route::get('/ksp/pinjaman/income', [KspController::class, 'incomeIndex'])->name('admin.ksp.income.index');
                Route::get('/ksp/pinjaman', [KspController::class, 'index'])->name('admin.ksp.index');
                Route::post('/ksp/pinjaman/kabupaten', [KspController::class, 'kabupaten'])->name('admin.ksp.kabupaten');
                Route::post('/ksp/pinjaman/kecamatan', [KspController::class, 'kecamatan'])->name('admin.ksp.kecamatan');
                Route::post('/ksp/pinjaman/desa', [KspController::class, 'desa'])->name('admin.ksp.desa');
                Route::post('/ksp/add/perusahaan', [KspController::class, 'addPerusahaan'])->name('admin.ksp.add.perusahaan');
                Route::post('/ksp/detail/perusahaan', [KspController::class, 'detailPerusahaan'])->name('admin.ksp.detail.perusahaan');
                Route::post('/ksp/edit/perusahaan', [KspController::class, 'editPerusahaan'])->name('admin.ksp.edit.perusahaan');
                Route::post('/ksp/delete/perusahaan', [KspController::class, 'deletePerusahaan'])->name('admin.ksp.delete.perusahaan');
                // KSP => lender
                Route::get('/ksp/lender', [LenderController::class, 'index'])->name('admin.lender.index');

                // KSP => Karyawan
                Route::get('/ksp/list/karyawan/{id}', [KspController::class, 'listKaryawan'])->name('admin.list.karyawan');
                Route::post('/ksp/list/karyawan/{id}/table', [KspController::class, 'listKaryawanTable'])->name('admin.list.karyawan.table');
                Route::post('/ksp/list/karyawan/upload', [KspController::class, 'listKaryawanUpload'])->name('admin.list.karyawan.upload');
                Route::post('/ksp/list/karyawan/count', [KspController::class, 'listKaryawanPendingCount'])->name('admin.list.karyawan.pending.count');
                Route::post('/ksp/list/karyawan/pending', [KspController::class, 'listKaryawanPending'])->name('admin.list.karyawan.pending');
                Route::post('/ksp/list/karyawan/approve', [KspController::class, 'approveKaryawanPending'])->name('admin.list.karyawan.approve');
                Route::post('/ksp/list/karyawan/detail', [KspController::class, 'detailKaryawan'])->name('admin.list.karyawan.detail');
                Route::post('/ksp/list/karyawan/update', [KspController::class, 'updateKaryawan'])->name('admin.list.karyawan.updateKaryawan');
                Route::post('/ksp/list/perusahaan/table', [KspController::class, 'listPerusahaanTable'])->name('admin.ksp.perusahaan.table');
                // KSP => Pengajuan
                Route::post('/ksp/pending/table', [KspController::class, 'pendingPinjamanTable'])->name('admin.ksp.pending.table');
                Route::post('/ksp/detail', [KspController::class, 'detailPengajuan'])->name('admin.ksp.detail');
                Route::post('/ksp/verifikasi', [KspController::class, 'verifikasiPengajuan'])->name('admin.ksp.verifikasi');
                // KSP => configurasi
                Route::get('/ksp/konfigurasi/{id}', [KspController::class, 'konfigurasi'])->name('admin.ksp.konfigurasi');
                Route::post('/ksp/konfigurasi/amount/pinjaman', [KspController::class, 'konfigurasiAmount'])->name('admin.ksp.konfigurasi.amount');
                Route::post('/ksp/konfigurasi/table', [KspController::class, 'konfigurasiTable'])->name('admin.ksp.konfigurasi.table');
                Route::post('/ksp/konfigurasi/add', [KspController::class, 'addKonfigurasi'])->name('admin.ksp.konfigurasi.add');
                Route::post('/ksp/konfigurasi/delete', [KspController::class, 'deleteKonfigurasi'])->name('admin.ksp.konfigurasi.delete');
                Route::post('/ksp/konfigurasi/detail', [KspController::class, 'detailKonfigurasi'])->name('admin.ksp.konfigurasi.detail');
                Route::post('/ksp/konfigurasi/edit', [KspController::class, 'editKonfigurasi'])->name('admin.ksp.konfigurasi.edit');
                // KSP => Pengajuan => invoicing
                Route::post('/ksp/invoicing/table', [KspController::class, 'invoicingTable'])->name('admin.ksp.invoicing.lender.table');
                Route::get('/ksp/invoicing/pdf/{id}/{tahun}/{bulan}', [KspController::class, 'invoicingDownload'])->name('admin.ksp.invoicing.download');
                Route::post('/ksp/invoicing/update', [KspController::class, 'invoicingUpdate'])->name('admin.ksp.invoicing.update');
                // KSP => Pengajuan => pending
                Route::post('/ksp/pengajuan/statistic', [KspController::class, 'pengajuanStatistic'])->name('admin.ksp.pengajuan.statistic');
                Route::get('/ksp/pengajuan/{id}', [KspController::class, 'pengajuanIndex'])->name('admin.ksp.pengajuan');
                Route::post('/ksp/pengajuan/onproses/table', [KspController::class, 'pengajuanOnprosesTable'])->name('admin.ksp.pengajuan.onproses.table');
                // KSP => Pengajuan => pending
                Route::post('/ksp/pengajuan/pendin/table', [KspController::class, 'pengajuanPendingTable'])->name('admin.ksp.pengajuan.pending.table');
                // KSP => Pengajuan => Approve
                Route::post('/ksp/pengajuan/approve/table', [KspController::class, 'pengajuanApproveTable'])->name('admin.ksp.pengajuan.approve.table');
                // KSP => Pengajuan => Reject
                Route::post('/ksp/pengajuan/reject/table', [KspController::class, 'pengajuanRejectTable'])->name('admin.ksp.pengajuan.reject.table');
                // KSP => Pengajuan => Done
                Route::post('/ksp/pengajuan/done/table', [KspController::class, 'pengajuanDoneTable'])->name('admin.ksp.pengajuan.done.table');
                // =>check angsuran
                Route::post('/ksp/check/angsuran', [KspController::class, 'checkAngsuran'])->name('admin.ksp.check.angsuran');
                // DailyCheckin
                Route::get('/daily/checkin', [DailyCheckinController::class, 'dailyIndex'])->name('admin.daily.index');
                Route::post('/daily/checkin/table', [DailyCheckinController::class, 'dailyTable'])->name('admin.daily.table');
                Route::post('/daily/checkin/update', [DailyCheckinController::class, 'dailyUpdate'])->name('admin.daily.update');


                // ksp => cost
                Route::get('/cost/ksp', [CostController::class, 'costIndex'])->name('admin.cost.index');
                Route::post('/cost/ksp/add', [CostController::class, 'costAdd'])->name('admin.cost.add');
                Route::post('/cost/ksp/save/rekening', [CostController::class, 'saveRekening'])->name('admin.cost.save.rekening');
                Route::post('/cost/ksp/save/rekening/pandu', [CostController::class, 'saveRekeningPandu'])->name('admin.cost.save.rekeningPandu');
            }
        );
    }
);
