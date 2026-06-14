<?php

use App\Http\Controllers\AccountController;
use APP\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductDetailController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProductController;
// Trang chủ & sản phẩm
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/products/{product:slug}', [ProductDetailController::class, 'show'])
    ->name('products.show');

// Guest routes (chưa đăng nhập)
Route::middleware('guest')->group(function () {
    Route::get('/register',               [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',              [AuthController::class, 'register']);

    Route::get('/login',                  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',                 [AuthController::class, 'login']);

    Route::get('/forgot-password',        [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password',       [AuthController::class, 'sendResetLink'])->name('password.email');

    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password',        [AuthController::class, 'resetPassword'])->name('password.update');
});

// Đăng nhập nhanh (demo - chỉ hoạt động ở môi trường local)
Route::get('/quick-login', [AuthController::class, 'quickLogin'])->name('quick-login');

// Giỏ hàng
Route::get('/cart',                   [CartController::class, 'index'])->name('cart.index');
Route::post('/cart',                  [CartController::class, 'store'])->name('cart.store');
Route::patch('/cart/update/{item}',   [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{item}',  [CartController::class, 'destroy'])->name('cart.destroy');

// Routes chỉ cần đăng nhập (chưa bắt buộc xác thực email): logout + luồng xác thực email
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Trang thông báo "vui lòng xác thực email"
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    // Link trong email xác thực (bảo vệ bằng chữ ký URL)
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('home')->with('status', 'Email của bạn đã được xác thực thành công!');
    })->middleware('signed')->name('verification.verify');

    // Gửi lại email xác thực (tối đa 6 lần/phút)
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    })->middleware('throttle:6,1')->name('verification.send');
});

// Routes yêu cầu đã đăng nhập + email đã được xác thực
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

    Route::get('/account',   [AccountController::class, 'show'])->name('account.show');

    // Đơn hàng
    Route::get('/orders',          [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}',  [OrderController::class, 'show'])->name('orders.show');

    // Checkout
    Route::get('/checkout',                           [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout/place-order',              [CheckoutController::class, 'store'])->name('checkout.place-order');
    Route::get('/checkout/payment-gateway/{order}',   [CheckoutController::class, 'paymentGateway'])->name('checkout.payment-gateway');
    Route::post('/checkout/payment-process/{order}',  [CheckoutController::class, 'processPayment'])->name('checkout.payment-process');
    Route::get('/checkout/success/{order}',           [CheckoutController::class, 'success'])->name('checkout.success');
});
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('products', ProductController::class)->except(['show']);

        Route::patch('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])
            ->name('products.toggle-status');
    });
