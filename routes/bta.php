<?php

use App\Http\Controllers\Bta\AnalystController;
use App\Http\Controllers\Bta\AuthController;
use App\Http\Controllers\Bta\CredamController;
use App\Http\Controllers\Bta\DashboardController;
use App\Http\Controllers\Bta\NasabahController;
use App\Http\Controllers\Bta\UserController;
use App\Http\Controllers\Bta\WilayahController;
use Illuminate\Support\Facades\Route;

Route::middleware(['BtaGuestMiddleware'])->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('bta.login');
    Route::post('/login', [AuthController::class, 'actionLogin'])->name('bta.login.action');
});

Route::middleware(['BtaMiddleware'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('bta.dashboard.index');
    Route::post('/logout', [AuthController::class, 'logout'])->name('bta.logout');

    /**
     * NASABAH
     * admin, master, super user
     */
    Route::get('/nasabah', [NasabahController::class, 'nasabahIndex'])
        ->middleware('bta.type:admin,master,super user')
        ->name('bta.nasabah.index');

    Route::get('/nasabah/tambah', [NasabahController::class, 'nasabahTambahIndex'])
        ->middleware('bta.type:admin,master,super user')
        ->name('bta.nasabah.tambah');

    Route::post('/nasabah/download/pdf', [NasabahController::class, 'downloadPdf'])
        ->middleware('bta.type:admin,master,super user')
        ->name('bta.nasabah.pk.download');

    Route::get('/nasabah/revisi/{noktp}', [NasabahController::class, 'nasabahRevisi'])
        ->middleware('bta.type:admin,master,super user')
        ->name('bta.nasabah.revisi');

    Route::post('/nasabah/edit', [NasabahController::class, 'nasabahDetail'])
        ->middleware('bta.type:admin,master,super user')
        ->name('bta.nasabah.detail');

    Route::post('/nasabah/insert', [NasabahController::class, 'nasabahInsert'])
        ->middleware('bta.type:admin,master,super user')
        ->name('bta.nasabah.insert');

    Route::post('/nasabah/update', [NasabahController::class, 'nasabahUpdate'])
        ->middleware('bta.type:admin,master,super user')
        ->name('bta.nasabah.update');

    Route::post('/nasabah/admin/analyst/table', [NasabahController::class, 'analystTable'])
        ->middleware('bta.type:admin,master,super user')
        ->name('bta.admin.nasabah.analyst.table');

    Route::post('/nasabah/admin/revisi/table', [NasabahController::class, 'revisiTable'])
        ->middleware('bta.type:admin,master,super user')
        ->name('bta.admin.nasabah.revisi.table');

    Route::post('/nasabah/admin/disetujui/table', [NasabahController::class, 'disetujuiTable'])
        ->middleware('bta.type:admin,master,super user')
        ->name('bta.admin.nasabah.disetujui.table');

    Route::post('/nasabah/admin/ditolak/table', [NasabahController::class, 'ditolakTable'])
        ->middleware('bta.type:admin,master,super user')
        ->name('bta.admin.nasabah.ditolak.table');

    Route::post('/nasabah/admin/credam/table', [NasabahController::class, 'credamTable'])
        ->middleware('bta.type:admin,master,super user')
        ->name('bta.admin.nasabah.credam.table');

    Route::post('/nasabah/admin/selesai/table', [NasabahController::class, 'selesaiTable'])
        ->middleware('bta.type:admin,master,super user')
        ->name('bta.admin.nasabah.selesai.table');

    /**
     * ANALYST
     * analyst, master, super user
     */
    Route::get('/secure-file/{token}', [AnalystController::class, 'downloadFile'])
        ->middleware('bta.type:analyst,master,super user')
        ->name('bta.secure.file.download');

    Route::get('/analyst', [AnalystController::class, 'analystIndex'])
        ->middleware('bta.type:analyst,master,super user')
        ->name('bta.analyst.index');

    Route::post('/analyst/table', [AnalystController::class, 'analystTable'])
        ->middleware('bta.type:analyst,master,super user')
        ->name('bta.analyst.table');

    Route::get('/analyst/action/review/{noktp}', [AnalystController::class, 'analystReview'])
        ->middleware('bta.type:analyst,master,super user')
        ->name('bta.analyst.review');

    Route::post('/analyst/action/submit', [AnalystController::class, 'analystUpdate'])
        ->middleware('bta.type:analyst,master,super user')
        ->name('bta.analyst.update');

    Route::post('/nasabah/analyst/setuju/upload', [NasabahController::class, 'nasabahUploadSetuju'])
        ->middleware('bta.type:analyst,master,super user')
        ->name('bta.nasabah.upload.setuju');

    /**
     * CREDAM
     * credam, master, super user
     */
    Route::get('/credam', [CredamController::class, 'credamIndex'])
        ->middleware('bta.type:credam,master,super user')
        ->name('bta.credam.index');

    Route::post('/credam/table', [CredamController::class, 'credamTable'])
        ->middleware('bta.type:credam,master,super user')
        ->name('bta.credam.table');

    Route::get('/credam/review/{id}', [CredamController::class, 'credamReview'])
        ->middleware('bta.type:credam,master,super user')
        ->name('bta.credam.review');

    Route::post('/credam/approve', [CredamController::class, 'credamApprove'])
        ->middleware('bta.type:credam,master,super user')
        ->name('bta.credam.approve');

    /**
     * USERS
     * super user only
     */
    Route::get('/users', [UserController::class, 'index'])
        ->middleware('bta.type:super user')
        ->name('bta.users.index');

    Route::get('/users/create', [UserController::class, 'create'])
        ->middleware('bta.type:super user')
        ->name('bta.users.create');

    Route::post('/users/store', [UserController::class, 'store'])
        ->middleware('bta.type:super user')
        ->name('bta.users.store');

    Route::get('/users/{id}/edit', [UserController::class, 'edit'])
        ->middleware('bta.type:super user')
        ->name('bta.users.edit');

    Route::get('/users/{id}/show', [UserController::class, 'show'])
        ->middleware('bta.type:super user')
        ->name('bta.users.show');

    Route::put('/users/{id}', [UserController::class, 'update'])
        ->middleware('bta.type:super user')
        ->name('bta.users.update');

    Route::delete('/users/{id}', [UserController::class, 'destroy'])
        ->middleware('bta.type:super user')
        ->name('bta.users.destroy');

    Route::post('/users/bulk-delete', [UserController::class, 'bulkDelete'])
        ->middleware('bta.type:super user')
        ->name('bta.users.bulkDelete');

    /**
     * WILAYAH / MASTER DATA
     * all user
     */
    Route::post('/provinsi', [WilayahController::class, 'provinsi'])
        ->middleware('bta.type:admin,master,analyst,credam,super user')
        ->name('bta.wilayah.provinsi');

    Route::post('/kota', [WilayahController::class, 'kota'])
        ->middleware('bta.type:admin,master,analyst,credam,super user')
        ->name('bta.wilayah.kota');

    Route::post('/kota/edit', [WilayahController::class, 'kotaEdit'])
        ->middleware('bta.type:admin,master,analyst,credam,super user')
        ->name('bta.wilayah.kota.edit');

    Route::post('/kecamatan', [WilayahController::class, 'kecamatan'])
        ->middleware('bta.type:admin,master,analyst,credam,super user')
        ->name('bta.wilayah.kecamatan');

    Route::post('/kecamatan/edit', [WilayahController::class, 'kecamatanEdit'])
        ->middleware('bta.type:admin,master,analyst,credam,super user')
        ->name('bta.wilayah.kecamatan.edit');

    Route::post('/kelurahan', [WilayahController::class, 'kelurahan'])
        ->middleware('bta.type:admin,master,analyst,credam,super user')
        ->name('bta.wilayah.kelurahan');

    Route::post('/kelurahan/edit', [WilayahController::class, 'kelurahanEdit'])
        ->middleware('bta.type:admin,master,analyst,credam,super user')
        ->name('bta.wilayah.kelurahan.edit');

    Route::post('/kodepos', [WilayahController::class, 'kodepos'])
        ->middleware('bta.type:admin,master,analyst,credam,super user')
        ->name('bta.wilayah.kodepos');

    Route::post('/dati', [WilayahController::class, 'dati'])
        ->middleware('bta.type:admin,master,analyst,credam,super user')
        ->name('bta.wilayah.dati');

    Route::post('/bank', [WilayahController::class, 'bank'])
        ->middleware('bta.type:admin,master,analyst,credam,super user')
        ->name('bta.wilayah.bank');

    Route::post('/wilayah/kota', [WilayahController::class, 'kotaNew'])
        ->middleware('bta.type:admin,master,analyst,credam,super user')
        ->name('bta.wilayah.kota.new');

    Route::post('/wilayah/kecamatan', [WilayahController::class, 'kecamatanNew'])
        ->middleware('bta.type:admin,master,analyst,credam,super user')
        ->name('bta.wilayah.kecamatan.new');

    Route::post('/wilayah/dati', [WilayahController::class, 'datiNew'])
        ->middleware('bta.type:admin,master,analyst,credam,super user')
        ->name('bta.wilayah.dati.new');

    Route::post('/wilayah/kelurahan', [WilayahController::class, 'kelurahanNew'])
        ->middleware('bta.type:admin,master,analyst,credam,super user')
        ->name('bta.wilayah.kelurahan.new');

    Route::post('/wilayah/kodepos', [WilayahController::class, 'kodeposNew'])
        ->middleware('bta.type:admin,master,analyst,credam,super user')
        ->name('bta.wilayah.kodepos.new');

    Route::get('/users/profile', [UserController::class, 'profile'])
        ->middleware('bta.type:admin,master,analyst,credam,super user')
        ->name('bta.profile');

    Route::get('/profile', [UserController::class, 'index'])->name('profile.index');
    Route::post('/profile/update-password', [UserController::class, 'updatePassword'])->name('profile.updatePassword');
});
