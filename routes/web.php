<?php

use App\Http\Controllers\AccountController;

use App\Http\Controllers\Admin\CategoryController;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FlashSaleController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Api\GeminiChatbotController;
use App\Http\Controllers\AuthController;

use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductDetailController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\CouponController;
use App\Models\FlashSale;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/search/quick', [ProductController::class, 'quickSearch'])->name('search.quick');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [ProductDetailController::class, 'show'])
    ->name('products.show');
Route::post('/products/{product:id}/review', [ProductReviewController::class, 'store'])
    ->middleware('auth')
    ->name('products.review.store');

Route::get('/search', [SearchController::class, 'index'])->name('search');

// ---------- Posts (Tin tức) ----------
Route::get('/tin-tuc', [\App\Http\Controllers\PostController::class, 'index'])->name('posts.index');
Route::get('/tin-tuc/{slug}', [\App\Http\Controllers\PostController::class, 'show'])->name('posts.show');

// ---------- Product Comparison ----------
Route::get('/compare', [CompareController::class, 'index'])->name('compare.index');
Route::post('/compare', [CompareController::class, 'add'])->name('compare.add');
Route::delete('/compare/{product:id}', [CompareController::class, 'remove'])->name('compare.remove');
Route::delete('/compare', [CompareController::class, 'clear'])->name('compare.clear');

// ---------- Authentication Routes ----------

// Guest routes (chưa đăng nhập)
Route::middleware('guest')->group(function () {
    Route::get('/register',               [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',              [AuthController::class, 'register']);

    Route::get('/login',                  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',                 [AuthController::class, 'login']);

    // Social Authentication Routes
    Route::get('/auth/{provider}/redirect', [AuthController::class, 'redirectToProvider'])->name('auth.social.redirect');
    Route::get('/auth/{provider}/callback', [AuthController::class, 'handleProviderCallback'])->name('auth.social.callback');
    Route::post('/auth/login', [AuthController::class, 'socialLoginPost'])->name('auth.social.login-post');

    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');

    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

    // Google OAuth Routes
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('google.login');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
});


Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Đổi mật khẩu (yêu cầu đăng nhập)
Route::middleware('auth')->group(function () {
    Route::get('/password/change', [AuthController::class, 'showChangePassword'])->name('password.change');
    Route::post('/password/change', [AuthController::class, 'changePassword']);
});

// ---------- Account ----------
Route::get('/account', [AccountController::class, 'show'])
    ->middleware('auth')
    ->name('account.show');

// ---------- Wishlist Routes ----------
Route::middleware('auth')->group(function () {
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
});

// ---------- Profile ----------
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// ---------- Coupons ----------
Route::get('/coupons', [CouponController::class, 'index'])->name('coupons.index');
Route::post('/coupons/{coupon}/save', [CouponController::class, 'save'])->name('coupons.save');

// ---------- Cart Routes ----------
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
Route::post('/cart/buy-now', [CartController::class, 'buyNow'])->name('cart.buy-now');
Route::patch('/cart/update/{item}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{item}', [CartController::class, 'destroy'])->name('cart.destroy');
Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::post('/cart/set-selection', [CartController::class, 'setSelection'])->name('cart.set-selection');
// ---------- Checkout Routes ----------
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout/apply-coupon', [CheckoutController::class, 'applyCoupon'])->name('checkout.apply-coupon');
    Route::post('/checkout/remove-coupon', [CheckoutController::class, 'removeCoupon'])->name('checkout.remove-coupon');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.place-order');
    Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{order}/confirm-received', [OrderController::class, 'confirmReceived'])->name('orders.confirm-received');
});

// VNPay - cổng thanh toán thật
Route::get('/checkout/vnpay/create/{order}', [CheckoutController::class, 'vnpayCreate'])->name('checkout.vnpay.create');
Route::get('/checkout/vnpay/return', [CheckoutController::class, 'vnpayReturn'])->name('checkout.vnpay.return');

// ---------- Orders Routes ----------
Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

/*
|--------------------------------------------------------------------------
| Xác thực email (cơ chế mặc định của Laravel — MustVerifyEmail)
|--------------------------------------------------------------------------
*/
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('home')->with('status', 'Email của bạn đã được xác thực thành công!');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

/*
|--------------------------------------------------------------------------
| Khu vực quản trị (Admin) — yêu cầu đăng nhập + quyền admin
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Sản phẩm
        Route::patch('products/{product}/toggle-status', [AdminProductController::class, 'toggleStatus'])
            ->name('products.toggle-status');
        Route::resource('products', AdminProductController::class);

        // Danh mục
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('brands', \App\Http\Controllers\Admin\BrandController::class)->except(['show']);
        Route::resource('banners', \App\Http\Controllers\Admin\BannerController::class)->except(['show']);
        
        // Quản lý Bài viết
        Route::resource('post-categories', \App\Http\Controllers\Admin\PostCategoryController::class)->except(['show']);
        Route::resource('posts', \App\Http\Controllers\Admin\PostController::class)->except(['show']);

        // Flash Sale
        Route::patch('flash-sales/{flashSale}/toggle-status', [FlashSaleController::class, 'toggleStatus'])
->name('flash-sales.toggle-status');
Route::resource('flash-sales', FlashSaleController::class);

        // Mã giảm giá (Coupons)
        Route::resource('coupons', AdminCouponController::class);


        // Người dùng / Khách hàng (xem danh sách, chi tiết, khóa/mở khóa)
        Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('users/{user}', [AdminUserController::class, 'show'])->name('users.show');
        Route::patch('users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])
            ->name('users.toggle-status');


        // Thống kê đơn hàng (đặt TRƯỚC {order} để tránh conflict)
        Route::get('orders/statistics', [App\Http\Controllers\Admin\OrderStatisticsController::class, 'index'])->name('orders.statistics');
        // Đơn hàng (xem danh sách, chi tiết, xác nhận/cập nhật trạng thái, hủy)
        Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])
            ->name('orders.update-status');
        Route::patch('orders/{order}/cancel', [AdminOrderController::class, 'cancel'])
            ->name('orders.cancel');

        // Quản lý tồn kho
        Route::get('inventory', [App\Http\Controllers\Admin\InventoryController::class, 'index'])->name('inventory.index');
        Route::post('inventory/{inventory}/import', [App\Http\Controllers\Admin\InventoryController::class, 'import'])->name('inventory.import');
        Route::post('inventory/{inventory}/export', [App\Http\Controllers\Admin\InventoryController::class, 'export'])->name('inventory.export');
        Route::post('inventory/{inventory}/adjust', [App\Http\Controllers\Admin\InventoryController::class, 'adjust'])->name('inventory.adjust');
        Route::get('inventory/history', [App\Http\Controllers\Admin\InventoryController::class, 'history'])->name('inventory.history');

        // Thống kê doanh thu & xuất báo cáo
        Route::get('revenue', [App\Http\Controllers\Admin\RevenueController::class, 'index'])->name('revenue.index');
        Route::get('reports/revenue/excel', [App\Http\Controllers\Admin\ReportController::class, 'revenueExcel'])->name('reports.revenue.excel');
        Route::get('reports/revenue/pdf', [App\Http\Controllers\Admin\ReportController::class, 'revenuePdf'])->name('reports.revenue.pdf');

        // Bình luận / đánh giá (Admin Reviews management)

        Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');
        Route::patch('reviews/{review}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
        Route::patch('reviews/{review}/hide', [ReviewController::class, 'hide'])->name('reviews.hide');
        //   GEMMINI CHAT

        // Cài đặt
        Route::get('/settings/notifications', [SettingController::class, 'notifications'])->name('settings.notifications');
        Route::post('/settings/notifications', [SettingController::class, 'updateNotifications']);
    });
