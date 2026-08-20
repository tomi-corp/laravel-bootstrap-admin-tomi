<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CostController;
use App\Http\Controllers\Admin\DailyCheckinController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GenerateInvoiceController;
use App\Http\Controllers\Admin\KspController;
use App\Http\Controllers\Admin\LenderController;
use App\Http\Controllers\Admin\MenuMobileController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OtaController;
use App\Http\Controllers\Admin\PpobController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\InvoiceFeePageController;
use App\Http\Controllers\Admin\InvoiceFeePdfController;
use App\Http\Controllers\Admin\SalaryFinancingController;
use App\Http\Controllers\Admin\TomiInvoiceController;
use App\Http\Controllers\Admin\WebsiteInvoiceController;
use App\Http\Controllers\Admin\GadgetInstallmentController;
use Illuminate\Support\Facades\Route;


Route::get('/invoice-fee', [InvoiceFeePageController::class, 'index'])
    ->name('admin.invoice.fee.page');

Route::get('/invoice-fee/{id}', [InvoiceFeePdfController::class, 'show'])
    ->name('admin.invoice.fee.page.show');

Route::get('/invoice-fee/{id}/pdf', [InvoiceFeePdfController::class, 'downloadPdf'])
    ->name('admin.invoice.fee.pdf');

Route::get('/transfer-request/{id}/pdf', [InvoiceFeePdfController::class, 'downloadTransferRequestPdf'])
    ->name('admin.transfer.request.pdf');

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
                // KSP => Perusahaan => invoiving
                Route::get('/ksp/pinjaman/invoicing', [KspController::class, 'kspInvoicingIndex'])->name('admin.ksp.invoicing.index');
                Route::post('/ksp/list/perusahaan/invoicing/table', [KspController::class, 'listPerusahaanInvoicingTable'])->name('admin.ksp.perusahaan.invoicing.table');
                Route::get('/ksp/pengajuan/invoicing/pdf/{id}', [KspController::class, 'invoicingDownload'])->name('admin.ksp.invoicing.download');
                // ksp => lender
                Route::get('/ksp/lender', [LenderController::class, 'indexLender'])->name('admin.lender.index');
                Route::post('/ksp/lender/add', [LenderController::class, 'addLender'])->name('admin.lender.add');
                Route::post('/ksp/lender/table', [LenderController::class, 'lenderTable'])->name('admin.lender.table');
                Route::post('/ksp/lender/pks/show', [LenderController::class, 'pksShow'])->name('admin.lender.pks.show');
                Route::post('/ksp/lender/image/show', [LenderController::class, 'imageShow'])->name('admin.lender.image.show');
                Route::post('/ksp/lender/detail', [LenderController::class, 'lenderDetail'])->name('admin.lender.detail');
                Route::post('/ksp/lender/update', [LenderController::class, 'updateLender'])->name('admin.lender.update');
                Route::post('/ksp/lender/delete', [LenderController::class, 'deleteLender'])->name('admin.lender.delete');
                Route::post('/ksp/lender/approval/table', [LenderController::class, 'approvalTableLender'])->name('admin.lender.approval.lender');
                Route::post('/ksp/lender/approval/detail', [LenderController::class, 'approvalDetail'])->name('admin.lender.approval.detail');
                Route::post('/ksp/lender/approval/action', [LenderController::class, 'approvalAction'])->name('admin.lender.approval.action');
                Route::post('/ksp/lender/update/history', [LenderController::class, 'updateHistoryLender'])->name('admin.lender.update.history');
                // ksp => lender => invoicing
                Route::get('/ksp/lender/invoicing', [LenderController::class, 'indexInvoicingLender'])->name('admin.lender.invoicing.index');
                Route::post('/ksp/lender/invoicing/sementara/table', [LenderController::class, 'invoicingSementaraTable'])->name('admin.lender.invoicing.sementara.table');
                Route::post('/ksp/lender/invoicing/pending/table', [LenderController::class, 'invoicingLenderPending'])->name('admin.lender.invoicing.pending.table');
                Route::post('/ksp/lender/invoicing/done/table', [LenderController::class, 'invoicingLenderDone'])->name('admin.lender.invoicing.done.table');
                Route::post('/ksp/lender/invoicing/pending/update', [LenderController::class, 'invoicingLenderPendingUpdate'])->name('admin.lender.invoicing.pending.update');
                Route::get('/ksp/lender/invoicing/{id}', [LenderController::class, 'downloadInvoice'])->name('admin.lender.invoicing.download');
                Route::post('/ksp/lender/invocing/approval/table', [LenderController::class, 'approvePendingInvoice'])->name('admin.lender.approval.invoicing.lender');
                Route::post('/ksp/lender/invocing/approval/detail', [LenderController::class, 'approvalDetailInvocing'])->name('admin.lender.approval.invoicing.detail');
                Route::post('/ksp/lender/invocing/approval/action', [LenderController::class, 'approvalInvoicingAction'])->name('admin.lender.approval.invoicing.action');
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
                Route::post('/ksp/invoicing/table', [KspController::class, 'invoicingTable'])->name('admin.ksp.invoicing.table');
                Route::post('/ksp/invoicing/update', [KspController::class, 'invoicingUpdate'])->name('admin.ksp.invoicing.update');
                // KSP => Pengajuan => pending
                Route::post('/ksp/pengajuan/statistic', [KspController::class, 'pengajuanStatistic'])->name('admin.ksp.pengajuan.statistic');
                Route::get('/ksp/pengajuan/{id}', [KspController::class, 'pengajuanIndex'])->name('admin.ksp.pengajuan');
                Route::post('/ksp/pengajuan/onproses/table', [KspController::class, 'pengajuanOnprosesTable'])->name('admin.ksp.pengajuan.onproses.table');
                // KSP => Pengajuan => pending
                Route::post('/ksp/pengajuan/pending/table', [KspController::class, 'pengajuanPendingTable'])->name('admin.ksp.pengajuan.pending.table');
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

                // ksp => create invoice manual
                Route::get('/generate/invoice', [GenerateInvoiceController::class, 'index'])->name('admin.generate.invoice.index');
                Route::get('/generate/invoice/stats', [GenerateInvoiceController::class, 'stats'])->name('admin.generate.invoice.stats');
                Route::get('/generate/invoice/download/template', [GenerateInvoiceController::class, 'downloadTemplateInvoice'])->name('admin.generate.invoice.download');
                Route::post('/generate/invoice/download/generate', [GenerateInvoiceController::class, 'generateInvoice'])->name('admin.generate.invoice.generate');
                Route::post('/generate/invoice/download/table', [GenerateInvoiceController::class, 'generateTable'])->name('admin.generate.invoice.table');
                Route::get('/generate/invoice/download/excel/{id}', [GenerateInvoiceController::class, 'downloadExcel'])->name('admin.generate.invoice.download.excel');
                Route::get('/generate/invoice/fee/tomi/{id}', [GenerateInvoiceController::class, 'generateFee'])->name('admin.generate.invoice.fee');
                Route::get('/generate/invoice/perusahaan/{id}', [GenerateInvoiceController::class, 'generateInvoiceDownload'])->name('admin.generate.invoice.perusahaan');

                // ota => mobile
                Route::get('/ota', [OtaController::class, 'otaIndex'])->name('admin.ota.index');
                Route::post('/ota/upload', [OtaController::class, 'otaUpload'])->name('admin.ota.upload');
                Route::post('/ota/table', [OtaController::class, 'otaTable'])->name('admin.ota.table');
                Route::post('/ota/delete', [OtaController::class, 'deleteOta'])->name('admin.ota.delete');

                // send notification
                // admin.sendnotif.index
                Route::get('/send/notification', [NotificationController::class, 'notificationPushIndex'])->name('admin.sendnotif.index');
                Route::post('/send/notification/action', [NotificationController::class, 'notificationPushAction'])->name('admin.sendnotif.action');
                Route::post('/send/notification/table', [NotificationController::class, 'notificationPushTable'])->name('admin.sendnotif.table');

                // manage admin
                Route::get('/manage/admin', [AuthController::class, 'manageAdminIndex'])->name('admin.manage.admin.index');
                Route::post('/manage/admin/table', [AuthController::class, 'manageAdminTable'])->name('admin.manage.admin.table');
                Route::post('/manage/admin/add', [AuthController::class, 'manageAdminAdd'])->name('admin.manage.admin.add');
                Route::post('/manage/admin/delete', [AuthController::class, 'manageAdminDelete'])->name('admin.manage.admin.delete');
                Route::post('/manage/admin/audit/table', [AuthController::class, 'manageAuditTable'])->name('admin.manage.admin.audit.table');

                // salary financing
                Route::get('/salary-financing', [SalaryFinancingController::class, 'index'])->name('admin.salary-financing.index');

                Route::get('/salary-financing/invoice/{id}/download', [SalaryFinancingController::class, 'downloadInvoice'])->name('admin.salary-financing.invoice.download');

                Route::get('/salary-financing/{id}', [SalaryFinancingController::class, 'show'])->name('admin.salary-financing.show');

                Route::get('/cicilan-elektronik', [GadgetInstallmentController::class, 'index'])->name('admin.cicilan-elektronik.index');

                Route::get('/website/invoice', [WebsiteInvoiceController::class, 'index'])
                    ->name('admin.website.invoice.index');

            Route::get('invoice-financing', [TomiInvoiceController::class, 'index'])
                ->name('admin.invoice-financing.index');

            Route::get('invoice-financing/{id}', [TomiInvoiceController::class, 'show'])
                ->name('admin.invoice-financing.show');

            Route::get('gadget-installment', 
            [GadgetInstallmentController::class, 'index'])->name('admin.gadget-installment.index');

            }
        );
    }
);
