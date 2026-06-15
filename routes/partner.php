<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Partner\AuthController;
use App\Http\Controllers\Partner\DashboardController;
use App\Http\Controllers\Partner\CompanyController;
use App\Http\Controllers\Partner\EmployeeController;
use App\Http\Controllers\Partner\LoanApplicationController;

Route::get('/login', [AuthController::class, 'login'])
    ->name('partners.login');

Route::post('/login', [AuthController::class, 'loginProcess'])
    ->name('partners.login.process');

Route::get('/register', [AuthController::class, 'register'])
    ->name('partners.register');

Route::post('/register', [AuthController::class, 'registerProcess'])
    ->name('partners.register.process');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('partners.logout');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('partners.dashboard');

Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])
    ->name('partners.password.request');

Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])
    ->name('partners.password.email');

Route::get('/company', [CompanyController::class, 'index'])
    ->name('partners.company.index');

Route::get('/company/configuration', [CompanyController::class, 'configuration'])
    ->name('partners.configuration.index');

Route::get('/company/employees', [EmployeeController::class, 'index'])
    ->name('partners.employees.index');

Route::get('/company/loan-applications', [LoanApplicationController::class, 'index'])
    ->name('partners.loan-applications.index');
