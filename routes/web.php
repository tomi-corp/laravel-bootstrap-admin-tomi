<?php

use App\Http\Controllers\Guest\VerifEmailController;
use Illuminate\Support\Facades\Route;

Route::get('/confirmation/{code}', [VerifEmailController::class, 'verifEmail'])->name('verif.email');

// Route::get('/', function () {
//     return view('welcome');
// });
