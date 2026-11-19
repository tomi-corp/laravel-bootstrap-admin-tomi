<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Internal\AdminManagementController;
use App\Http\Controllers\Internal\LenderController;
use App\Http\Controllers\Internal\CompanyController;
use App\Http\Controllers\Internal\TomiDana\CompanyController as TomiDanaCompanyController;
use App\Http\Controllers\Internal\TomiDana\ConfigurationController as TomiDanaConfigurationController;
use App\Http\Controllers\Internal\TomiDana\DisbursementController as TomiDanaDisbursementController;
use App\Http\Controllers\Internal\TomiDana\RepaymentController as TomiDanaRepaymentController;
use App\Http\Controllers\Internal\TomiDana\GenerateInvoiceController as TomiDanaGenerateInvoiceController;
use App\Http\Controllers\Internal\TomiDana\EmployeeController as TomiDanaEmployeeController;
use App\Http\Controllers\Internal\TomiDana\LoanApplicationController as TomiDanaLoanApplicationController;
use App\Http\Controllers\Internal\TomiDanaPlus\CompanyController as TomiDanaPlusCompanyController;
use App\Http\Controllers\Internal\TomiGaji\CompanyController as TomiGajiCompanyController;
use App\Http\Controllers\Internal\TomiInvoice\CompanyController as TomiInvoiceCompanyController;
use App\Http\Controllers\Internal\TomiGawai\CompanyController as TomiGawaiCompanyController;
use App\Http\Controllers\Internal\WilayahController;

Route::view('/', 'internal.dashboard.index')->name('dashboard');
Route::view('/dashboard', 'internal.dashboard.index')->name('dashboard.page');
Route::view('/ppob', 'internal.ppob.index')->name('ppob');
Route::get('/companies', [CompanyController::class, 'index'])->name('companies');
Route::post('/companies/table', [CompanyController::class, 'table'])->name('companies.table');
Route::post('/companies/add', [CompanyController::class, 'store'])->name('companies.add');
Route::post('/companies/detail', [CompanyController::class, 'show'])->name('companies.detail');
Route::post('/companies/update', [CompanyController::class, 'update'])->name('companies.update');
Route::post('/companies/delete', [CompanyController::class, 'destroy'])->name('companies.delete');
Route::post('/companies/delete-selected', [CompanyController::class, 'deleteSelectedCompany'])->name('companies.delete-selected');
Route::get('/companies/export', [CompanyController::class, 'exportCompany'])->name('companies.export');
Route::get('/companies/import/template', [CompanyController::class, 'importTemplate'])->name('companies.import.template');
Route::post('/companies/import', [CompanyController::class, 'importCompany'])->name('companies.import');
Route::post('/companies/pks/list', [CompanyController::class, 'pksList'])->name('companies.pks.list');
Route::post('/companies/pks/delete', [CompanyController::class, 'deletePks'])->name('companies.pks.delete');
Route::get('/companies/pks/{id}/file', [CompanyController::class, 'pksFile'])->whereNumber('id')->name('companies.pks.file');
Route::post('/companies/locations/cities', [WilayahController::class, 'cities'])->name('companies.locations.cities');
Route::post('/companies/locations/districts', [WilayahController::class, 'districts'])->name('companies.locations.districts');
Route::post('/companies/locations/villages', [WilayahController::class, 'villages'])->name('companies.locations.villages');
Route::get('/lenders', [LenderController::class, 'index'])->name('lenders');
Route::post('/lenders/table', [LenderController::class, 'lenderTable'])->name('lenders.table');
Route::post('/lenders/add', [LenderController::class, 'addLender'])->name('lenders.add');
Route::post('/lenders/detail', [LenderController::class, 'lenderDetail'])->name('lenders.detail');
Route::post('/lenders/update', [LenderController::class, 'updateLender'])->name('lenders.update');
Route::post('/lenders/delete', [LenderController::class, 'deleteLender'])->name('lenders.delete');
Route::post('/lenders/delete-selected', [LenderController::class, 'deleteSelectedLender'])->name('lenders.delete-selected');
Route::get('/lenders/export', [LenderController::class, 'exportLender'])->name('lenders.export');
Route::get('/lenders/import/template', [LenderController::class, 'importTemplate'])->name('lenders.import.template');
Route::post('/lenders/import', [LenderController::class, 'importLender'])->name('lenders.import');
Route::post('/lenders/locations/cities', [WilayahController::class, 'cities'])->name('lenders.locations.cities');
Route::post('/lenders/locations/districts', [WilayahController::class, 'districts'])->name('lenders.locations.districts');
Route::post('/lenders/locations/villages', [WilayahController::class, 'villages'])->name('lenders.locations.villages');
Route::view('/tomi-dana', 'internal.tomi-dana.index')->name('tomi-dana');
Route::prefix('/tomi-dana')->name('tomi-dana.')->group(function () {
    Route::get('/companies', [TomiDanaCompanyController::class, 'index'])->name('companies');
    Route::post('/companies/table', [TomiDanaCompanyController::class, 'table'])->name('companies.table');

    // Konfigurasi pinjaman per perusahaan
    Route::get('/companies/{company}/configuration', [TomiDanaConfigurationController::class, 'index'])->whereNumber('company')->name('configuration');
    Route::post('/configuration/table', [TomiDanaConfigurationController::class, 'table'])->name('configuration.table');
    Route::post('/configuration/add', [TomiDanaConfigurationController::class, 'store'])->name('configuration.add');
    Route::post('/configuration/detail', [TomiDanaConfigurationController::class, 'show'])->name('configuration.detail');
    Route::post('/configuration/update', [TomiDanaConfigurationController::class, 'update'])->name('configuration.update');
    Route::post('/configuration/delete', [TomiDanaConfigurationController::class, 'destroy'])->name('configuration.delete');
    Route::post('/configuration/amount', [TomiDanaConfigurationController::class, 'amount'])->name('configuration.amount');

    // Proses pengajuan per perusahaan
    Route::get('/companies/{company}/applications', [TomiDanaLoanApplicationController::class, 'index'])->whereNumber('company')->name('applications');
    Route::post('/applications/statistic', [TomiDanaLoanApplicationController::class, 'statistic'])->name('applications.statistic');
    Route::post('/applications/table/{status}', [TomiDanaLoanApplicationController::class, 'table'])->whereIn('status', TomiDanaLoanApplicationController::STATUSES)->name('applications.table');
    Route::post('/applications/installments', [TomiDanaLoanApplicationController::class, 'installments'])->name('applications.installments');
    Route::post('/applications/invoices/table', [TomiDanaLoanApplicationController::class, 'invoiceTable'])->name('applications.invoices.table');
    Route::post('/applications/invoices/update', [TomiDanaLoanApplicationController::class, 'invoiceUpdate'])->name('applications.invoices.update');
    Route::get('/applications/invoices/{invoice}/download', [TomiDanaLoanApplicationController::class, 'invoiceDownload'])->whereNumber('invoice')->name('applications.invoices.download');

    // List karyawan per perusahaan
    Route::get('/companies/{company}/employees', [TomiDanaEmployeeController::class, 'index'])->whereNumber('company')->name('employees');
    Route::post('/companies/{company}/employees/table', [TomiDanaEmployeeController::class, 'table'])->whereNumber('company')->name('employees.table');
    Route::post('/companies/{company}/employees/upload', [TomiDanaEmployeeController::class, 'upload'])->whereNumber('company')->name('employees.upload');
    Route::post('/employees/pending/count', [TomiDanaEmployeeController::class, 'pendingCount'])->name('employees.pending.count');
    Route::post('/employees/pending', [TomiDanaEmployeeController::class, 'pending'])->name('employees.pending');
    Route::post('/employees/approve', [TomiDanaEmployeeController::class, 'approve'])->name('employees.approve');
    Route::post('/employees/detail', [TomiDanaEmployeeController::class, 'show'])->name('employees.detail');
    Route::post('/employees/update', [TomiDanaEmployeeController::class, 'update'])->name('employees.update');

    // Pencairan dana pinjaman
    Route::get('/disbursement', [TomiDanaDisbursementController::class, 'index'])->name('disbursement');
    Route::post('/disbursement/table', [TomiDanaDisbursementController::class, 'table'])->name('disbursement.table');
    Route::post('/disbursement/detail', [TomiDanaDisbursementController::class, 'detail'])->name('disbursement.detail');
    Route::post('/disbursement/action', [TomiDanaDisbursementController::class, 'action'])->name('disbursement.action');
    Route::post('/disbursement/utils', [TomiDanaDisbursementController::class, 'utils'])->name('disbursement.utils');
    Route::post('/disbursement/pk', [TomiDanaDisbursementController::class, 'pk'])->name('disbursement.pk');
    Route::post('/disbursement/ktp', [TomiDanaDisbursementController::class, 'ktp'])->name('disbursement.ktp');
    Route::post('/disbursement/selfie', [TomiDanaDisbursementController::class, 'selfie'])->name('disbursement.selfie');

    // Pembayaran invoice perusahaan
    Route::get('/repayment', [TomiDanaRepaymentController::class, 'index'])->name('repayment');
    Route::post('/repayment/table', [TomiDanaRepaymentController::class, 'table'])->name('repayment.table');
    Route::post('/repayment/utils', [TomiDanaRepaymentController::class, 'utils'])->name('repayment.utils');
    Route::post('/repayment/list', [TomiDanaRepaymentController::class, 'list'])->name('repayment.list');
    Route::post('/repayment/action', [TomiDanaRepaymentController::class, 'action'])->name('repayment.action');

    // Generate invoice dari upload Excel
    Route::get('/generate-invoice', [TomiDanaGenerateInvoiceController::class, 'index'])->name('generate-invoice');
    Route::post('/generate-invoice', [TomiDanaGenerateInvoiceController::class, 'store'])->name('generate-invoice.store');
    Route::get('/generate-invoice/{id}/pdf', [TomiDanaGenerateInvoiceController::class, 'pdf'])->whereNumber('id')->name('generate-invoice.pdf');

    // Biaya aplikasi (halaman masih placeholder)
    Route::view('/invoice-fee', 'internal.tomi-dana.invoice-fee.index')->name('invoice-fee');
});
Route::view('/tomi-dana-plus', 'internal.tomi-dana-plus.index')->name('tomi-dana-plus');
Route::prefix('/tomi-dana-plus')->name('tomi-dana-plus.')->group(function () {
    Route::get('/companies', [TomiDanaPlusCompanyController::class, 'index'])->name('companies');
    Route::post('/companies/table', [TomiDanaPlusCompanyController::class, 'table'])->name('companies.table');

    // Pencairan dana, pembayaran tagihan, dan biaya aplikasi (halaman masih placeholder)
    Route::view('/disbursement', 'internal.tomi-dana-plus.disbursement.index')->name('disbursement');
    Route::view('/repayment', 'internal.tomi-dana-plus.repayment.index')->name('repayment');
    Route::view('/invoice-fee', 'internal.tomi-dana-plus.invoice-fee.index')->name('invoice-fee');
});

Route::view('/tomi-gaji', 'internal.tomi-gaji.index')->name('tomi-gaji');
Route::prefix('/tomi-gaji')->name('tomi-gaji.')->group(function () {
    Route::get('/companies', [TomiGajiCompanyController::class, 'index'])->name('companies');
    Route::post('/companies/table', [TomiGajiCompanyController::class, 'table'])->name('companies.table');

    // Pencairan dana, pembayaran tagihan, dan biaya aplikasi (halaman masih placeholder)
    Route::view('/disbursement', 'internal.tomi-gaji.disbursement.index')->name('disbursement');
    Route::view('/repayment', 'internal.tomi-gaji.repayment.index')->name('repayment');
    Route::view('/invoice-fee', 'internal.tomi-gaji.invoice-fee.index')->name('invoice-fee');
});

Route::view('/tomi-invoice', 'internal.tomi-invoice.index')->name('tomi-invoice');
Route::prefix('/tomi-invoice')->name('tomi-invoice.')->group(function () {
    Route::get('/companies', [TomiInvoiceCompanyController::class, 'index'])->name('companies');
    Route::post('/companies/table', [TomiInvoiceCompanyController::class, 'table'])->name('companies.table');

    // Pencairan dana, pembayaran tagihan, dan biaya aplikasi (halaman masih placeholder)
    Route::view('/disbursement', 'internal.tomi-invoice.disbursement.index')->name('disbursement');
    Route::view('/repayment', 'internal.tomi-invoice.repayment.index')->name('repayment');
    Route::view('/invoice-fee', 'internal.tomi-invoice.invoice-fee.index')->name('invoice-fee');
});

Route::view('/tomi-gawai', 'internal.tomi-gawai.index')->name('tomi-gawai');
Route::prefix('/tomi-gawai')->name('tomi-gawai.')->group(function () {
    Route::get('/companies', [TomiGawaiCompanyController::class, 'index'])->name('companies');
    Route::post('/companies/table', [TomiGawaiCompanyController::class, 'table'])->name('companies.table');

    // Pencairan dana, pembayaran tagihan, dan biaya aplikasi (halaman masih placeholder)
    Route::view('/disbursement', 'internal.tomi-gawai.disbursement.index')->name('disbursement');
    Route::view('/repayment', 'internal.tomi-gawai.repayment.index')->name('repayment');
    Route::view('/invoice-fee', 'internal.tomi-gawai.invoice-fee.index')->name('invoice-fee');
});

Route::view('/daily-attendance', 'internal.daily-attendance.index')->name('daily-attendance');
Route::view('/mobile', 'internal.mobile.index')->name('mobile');
Route::get('/admin-management', [AdminManagementController::class, 'index'])->name('admin-management');
Route::prefix('/admin-management')->name('admin-management.')->group(function () {
    Route::post('/table', [AdminManagementController::class, 'table'])->name('table');
    Route::post('/detail', [AdminManagementController::class, 'show'])->name('detail');
    Route::post('/add', [AdminManagementController::class, 'store'])->name('add');
    Route::post('/update', [AdminManagementController::class, 'update'])->name('update');
    Route::post('/delete', [AdminManagementController::class, 'destroy'])->name('delete');
    Route::post('/audit/table', [AdminManagementController::class, 'auditTable'])->name('audit.table');
});
Route::view('/bank-accounts', 'internal.bank-accounts.index')->name('bank-accounts');
Route::view('/referral', 'internal.referral.index')->name('referral');
