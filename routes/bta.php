<?php

use App\Http\Controllers\Bta\AnalystController;
use App\Http\Controllers\Bta\AuthController;
use App\Http\Controllers\Bta\CredamController;
use App\Http\Controllers\Bta\DashboardController;
use App\Http\Controllers\Bta\NasabahController;
use App\Http\Controllers\Bta\WilayahController;
use App\Http\Controllers\Bta\UserController;
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
        Route::get('/nasabah/tambah', [NasabahController::class, 'nasabahTambahIndex'])->name('bta.nasabah.tambah');
        // Route::get('/nasabah/download/pdf', [NasabahController::class, 'downloadPdf']);
        Route::post('/nasabah/download/pdf', [NasabahController::class, 'downloadPdf'])->name('bta.nasabah.pk.download');
        Route::get('/nasabah/revisi/{noktp}', [NasabahController::class, 'nasabahRevisi'])->name('bta.nasabah.revisi');
        Route::post('/nasabah/edit', [NasabahController::class, 'nasabahDetail'])->name('bta.nasabah.detail');
        // Route::post('/nasabah/insert', [NasabahController::class, 'nasabahInsert'])->name('bta.nasabah.insert');
        Route::post('/nasabah/analyst/setuju/upload', [NasabahController::class, 'nasabahUploadSetuju'])->name('bta.nasabah.upload.setuju');
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
        Route::post('/analyst/action/submit', [AnalystController::class, 'analystUpdate'])->name('bta.analyst.update');
        Route::get('/credam', [CredamController::class, 'credamIndex'])->name('bta.credam.index');
        Route::post('/credam/table', [CredamController::class, 'credamTable'])->name('bta.credam.table');
        Route::get('/credam/review/{id}', [CredamController::class, 'credamReview'])->name('bta.credam.review');
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



        // wilayah baru
        Route::post('/wilayah/kota', [WilayahController::class, 'kotaNew'])->name('bta.wilayah.kota.new');
        Route::post('/wilayah/kecamatan', [WilayahController::class, 'kecamatanNew'])->name('bta.wilayah.kecamatan.new');
        Route::post('/wilayah/dati', [WilayahController::class, 'datiNew'])->name('bta.wilayah.dati.new');
        Route::post('/wilayah/kelurahan', [WilayahController::class, 'kelurahanNew'])->name('bta.wilayah.kelurahan.new');
        Route::post('/wilayah/kodepos', [WilayahController::class, 'kodeposNew'])->name('bta.wilayah.kodepos.new');

        // tambah nasabah
        Route::post('/nasabah/insert', [NasabahController::class, 'nasabahInsert'])->name('bta.nasabah.insert');
        Route::post('/nasabah/update', [NasabahController::class, 'nasabahUpdate'])->name('bta.nasabah.update');

        // Daftar semua user
        Route::get('/users', [UserController::class, 'index'])->name('bta.users.index');

        // Form tambah user
        Route::get('/users/create', [UserController::class, 'create'])->name('bta.users.create');

        // Proses simpan user
        Route::post('/users/store', [UserController::class, 'store'])->name('bta.users.store');

        // Form edit user
        Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('bta.users.edit');

        // Form show user
        Route::get('/users/{id}/show', [UserController::class, 'show'])->name('bta.users.show');

        // Proses update user
        Route::put('/users/{id}', [UserController::class, 'update'])->name('bta.users.update');

        // Hapus user
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('bta.users.destroy');

        // Bulk delete user (POST)
        Route::post('/users/bulk-delete', [UserController::class, 'bulkDelete'])->name('bta.users.bulkDelete');
    }
);
