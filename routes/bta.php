<?php

use App\Http\Controllers\Bta\AnalystController;
use App\Http\Controllers\Bta\AuthController;
use App\Http\Controllers\Bta\CredamController;
use App\Http\Controllers\Bta\DashboardController;
use App\Http\Controllers\Bta\NasabahController;
use App\Http\Controllers\Bta\WilayahController;
use Illuminate\Support\Facades\Route;

Route::middleware(['BtaGuestMiddleware'])->group(
    function () {
        Route::get('/login', [AuthController::class, 'login'])->name('bta.login');
        Route::post('/login', [AuthController::class, 'actionLogin'])->name('bta.login.action');
    }
);


Route::middleware(['BtaMiddleware'])->group(
    function () {
        Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('bta.dashboard.index');
        Route::post('/logout', [AuthController::class, 'logout'])->name('bta.logout');
        // nasabah
        Route::get('/nasabah', [NasabahController::class, 'nasabahIndex'])->name('bta.nasabah.index');
        // Route::get('/nasabah/download/pdf', [NasabahController::class, 'downloadPdf']);
        Route::post('/nasabah/download/pdf', [NasabahController::class, 'downloadPdf'])->name('bta.nasabah.pk.download');
        Route::post('/nasabah/edit', [NasabahController::class, 'nasabahDetail'])->name('bta.nasabah.detail');
        Route::post('/nasabah/insert', [NasabahController::class, 'nasabahInsert'])->name('bta.nasabah.insert');
        Route::post('/nasabah/analyst/setuju/upload', [NasabahController::class, 'nasabahUploadSetuju'])->name('bta.nasabah.upload.setuju');
        Route::post('/nasabah/update', [NasabahController::class, 'nasabahUpdate'])->name('bta.nasabah.update');
        Route::post('/nasabah/admin/analyst/table', [NasabahController::class, 'analystTable'])->name('bta.admin.nasabah.analyst.table');
        Route::post('/nasabah/admin/revisi/table', [NasabahController::class, 'revisiTable'])->name('bta.admin.nasabah.revisi.table');
        Route::post('/nasabah/admin/disetujui/table', [NasabahController::class, 'disetujuiTable'])->name('bta.admin.nasabah.disetujui.table');
        Route::post('/nasabah/admin/ditolak/table', [NasabahController::class, 'ditolakTable'])->name('bta.admin.nasabah.ditolak.table');
        Route::post('/nasabah/admin/credam/table', [NasabahController::class, 'credamTable'])->name('bta.admin.nasabah.credam.table');
        Route::post('/nasabah/admin/selesai/table', [NasabahController::class, 'selesaiTable'])->name('bta.admin.nasabah.selesai.table');


        Route::get('/secure-file/{token}', [AnalystController::class, 'downloadFile'])
            ->name('bta.secure.file.download');

        Route::get('/analyst', [AnalystController::class, 'analystIndex'])->name('bta.analyst.index');
        Route::post('/analyst/table', [AnalystController::class, 'analystTable'])->name('bta.analyst.table');
        Route::get('/analyst/action/review/{noktp}', [AnalystController::class, 'analystReview'])->name('bta.analyst.review');
        Route::post('/analyst/action/submit/{id}', [AnalystController::class, 'analystUpdate'])->name('bta.analyst.update');
        Route::get('/credam', [CredamController::class, 'credamIndex'])->name('bta.credam.index');
        Route::post('/credam/table', [CredamController::class, 'credamTable'])->name('bta.credam.table');
        Route::get('/credam/review/{noktp}', [CredamController::class, 'credamReview'])->name('bta.credam.review');
        Route::post('/credam/approve', [CredamController::class, 'credamApprove'])->name('bta.credam.approve');

        // provinsi
        Route::post('/provinsi', [WilayahController::class, 'provinsi'])->name('bta.wilayah.provinsi');
        Route::post('/kota', [WilayahController::class, 'kota'])->name('bta.wilayah.kota');
        Route::post('/kota/edit', [WilayahController::class, 'kotaEdit'])->name('bta.wilayah.kota.edit');
        Route::post('/kecamatan', [WilayahController::class, 'kecamatan'])->name('bta.wilayah.kecamatan');
        Route::post('/kecamatan/edit', [WilayahController::class, 'kecamatanEdit'])->name('bta.wilayah.kecamatan.edit');
        Route::post('/kelurahan', [WilayahController::class, 'kelurahan'])->name('bta.wilayah.kelurahan');
        Route::post('/kelurahan/edit', [WilayahController::class, 'kelurahanEdit'])->name('bta.wilayah.kelurahan.edit');
        Route::post('/kodepos', [WilayahController::class, 'kodepos'])->name('bta.wilayah.kodepos');
        Route::post('/dati', [WilayahController::class, 'dati'])->name('bta.wilayah.dati');
        Route::post('/bank', [WilayahController::class, 'bank'])->name('bta.wilayah.bank');
    }
);
