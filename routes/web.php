<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('home
    ');
});

// ─── Guest routes (chưa đăng nhập) ───────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/register',                     [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',                    [AuthController::class, 'register']);

    Route::get('/login',                        [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',                       [AuthController::class, 'login']);

    Route::get('/forgot-password',              [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password',             [AuthController::class, 'sendResetLink'])->name('password.email');

    Route::get('/reset-password/{token}',       [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password',              [AuthController::class, 'resetPassword'])->name('password.update');
});

// ─── Auth routes (đã đăng nhập) ───────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/dashboard',                    [AuthController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout',                      [AuthController::class, 'logout'])->name('logout');
});