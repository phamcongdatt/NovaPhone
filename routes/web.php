<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
=======
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductDetailController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('home');
})->name('home');
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
=======
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/products/{product:slug}', [ProductDetailController::class, 'show'])
    ->name('products.show');

// ---------- Authentication Routes ----------
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/quick-login', [AuthController::class, 'quickLogin'])->name('quick-login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ---------- Cart Routes ----------
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
Route::patch('/cart/update/{item}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{item}', [CartController::class, 'destroy'])->name('cart.destroy');

// ---------- Checkout Routes ----------
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/checkout/place-order', [CheckoutController::class, 'store'])->name('checkout.place-order');
Route::get('/checkout/payment-gateway/{order}', [CheckoutController::class, 'paymentGateway'])->name('checkout.payment-gateway');
Route::post('/checkout/payment-process/{order}', [CheckoutController::class, 'processPayment'])->name('checkout.payment-process');
Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

// ---------- Orders Routes ----------
Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
=======
/*
|--------------------------------------------------------------------------
| Đăng ký tài khoản (chỉ cho khách chưa đăng nhập)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::get('/account', [AccountController::class, 'show'])
    ->middleware('auth')
    ->name('account.show');

/*
|--------------------------------------------------------------------------
| Xác thực email (cơ chế mặc định của Laravel — MustVerifyEmail)
|--------------------------------------------------------------------------
*/
// Trang thông báo "vui lòng kiểm tra email".
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// Endpoint trong link xác thực — middleware "signed" bảo vệ chữ ký URL.
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill(); // đánh dấu đã xác thực + bắn event Verified
    return redirect()->route('home')->with('status', 'Email của bạn đã được xác thực thành công!');
})->middleware(['auth', 'signed'])->name('verification.verify');

// Gửi lại email xác thực — throttle:6,1 = tối đa 6 request / phút.
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

/*
|--------------------------------------------------------------------------
| Ví dụ route yêu cầu email đã xác thực — áp middleware "verified"
|--------------------------------------------------------------------------
| Bỏ comment khi đã có controller tương ứng:
|
| Route::get('/dashboard', [DashboardController::class, 'index'])
|     ->middleware(['auth', 'verified'])
|     ->name('dashboard');
*/

