<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FlashSaleController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\OrderStatisticsController;
use App\Http\Controllers\Admin\PostCategoryController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RevenueController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductDetailController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReturnRequestController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\WishlistController;
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
Route::get('/tin-tuc', [PostController::class, 'index'])->name('posts.index');
Route::get('/tin-tuc/{slug}', [PostController::class, 'show'])->name('posts.show');

// ---------- Product Comparison ----------
Route::get('/compare', [CompareController::class, 'index'])->name('compare.index');
Route::post('/compare', [CompareController::class, 'add'])->name('compare.add');
Route::delete('/compare/{product:id}', [CompareController::class, 'remove'])->name('compare.remove');
Route::delete('/compare', [CompareController::class, 'clear'])->name('compare.clear');

// ---------- Authentication Routes ----------

// Guest routes (chưa đăng nhập)
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Google OAuth Routes
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('google.login');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

    // Social Authentication Routes
    Route::get('/auth/{provider}/redirect', [AuthController::class, 'redirectToProvider'])->name('auth.social.redirect');
    Route::get('/auth/{provider}/callback', [AuthController::class, 'handleProviderCallback'])->name('auth.social.callback');
    Route::post('/auth/login', [AuthController::class, 'socialLoginPost'])->name('auth.social.login-post');

    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');

    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::prefix('locations')
    ->middleware('throttle:120,1')
    ->group(function () {
        Route::get('/provinces', [LocationController::class, 'provinces'])
            ->name('locations.provinces');

        Route::get('/provinces/{provinceCode}/wards', [LocationController::class, 'wards'])
            ->name('locations.wards');
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
Route::middleware('auth')->group(function () {
    Route::get('/account', [AccountController::class, 'show'])->name('account.show');
    Route::get('/account/addresses', [AccountController::class, 'addresses'])->name('account.addresses');
});

// ---------- Address Routes ----------
Route::middleware('auth')->group(function () {
    Route::post('/addresses', [AddressController::class, 'store'])->name('address.store');
    Route::get('/addresses/{address}', [AddressController::class, 'show'])->name('address.show');
    Route::put('/addresses/{address}', [AddressController::class, 'update'])->name('address.update');
    Route::delete('/addresses/{address}', [AddressController::class, 'destroy'])->name('address.destroy');
    Route::post('/addresses/{address}/set-default', [AddressController::class, 'setDefault'])->name('address.set-default');
});

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
Route::post('/coupons/apply', [CouponController::class, 'apply'])->name('coupons.apply');

// ---------- Cart Routes ----------
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
Route::post('/cart/buy-now', [CartController::class, 'buyNow'])->name('cart.buy-now');
Route::patch('/cart/update/{item}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{item}', [CartController::class, 'destroy'])->name('cart.destroy');
Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::post('/cart/set-selection', [CartController::class, 'setSelection'])->name('cart.set-selection');
// ---------- Checkout Routes ----------
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/checkout/apply-coupon', [CheckoutController::class, 'applyCoupon'])->name('checkout.apply-coupon');
Route::post('/checkout/remove-coupon', [CheckoutController::class, 'removeCoupon'])->name('checkout.remove-coupon');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.place-order');
Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

//  Quan ly khach hang mau hang ma chua tao tai khoan
Route::get('/guest/orders/{order}', [CheckoutController::class, 'guestShow'])
    ->middleware('signed')
    ->name('guest.orders.show');
Route::post('/guest/orders/{order}/cancel', [CheckoutController::class, 'guestCancel'])
    ->middleware('signed')
    ->name('guest.orders.cancel');
Route::get('/guest/orders/{order}/payment', [CheckoutController::class, 'guestPay'])
    ->middleware('signed')
    ->name('guest.orders.pay');

Route::middleware('auth')->group(function () {
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{order}/confirm-received', [OrderController::class, 'confirmReceived'])->name('orders.confirm-received');
    Route::get('/orders/{order}/return', [ReturnRequestController::class, 'create'])->name('returns.create');
    Route::post('/orders/{order}/return', [ReturnRequestController::class, 'store'])->name('returns.store');
    Route::get('/returns/{returnRequest}', [ReturnRequestController::class, 'show'])->name('returns.show');
    Route::get('/returns/{returnRequest}/receipt', [ReturnRequestController::class, 'receipt'])->name('returns.receipt');
    Route::post('/returns/{returnRequest}/shipped', [ReturnRequestController::class, 'markShipped'])->name('returns.shipped');
    Route::post('/returns/{returnRequest}/refund-account', [ReturnRequestController::class, 'updateRefundAccount'])->name('returns.refund-account');
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
        Route::resource('products', AdminProductController::class)->except(['destroy']);

        // Danh mục
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('brands', BrandController::class)->except(['show']);
        Route::resource('banners', BannerController::class)->except(['show']);

        // Quản lý Bài viết
        Route::resource('post-categories', PostCategoryController::class)->except(['show']);
        Route::resource('posts', App\Http\Controllers\Admin\PostController::class)->except(['show', 'destroy']);

        // Flash Sale
        Route::resource('flash-sales', FlashSaleController::class)->except(['show']);

        // Mã giảm giá (Coupons)
        Route::resource('coupons', AdminCouponController::class)->except(['show']);

        // Người dùng / Khách hàng (xem danh sách, chi tiết, khóa/mở khóa)
        Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('users/{user}', [AdminUserController::class, 'show'])->name('users.show');
        Route::patch('users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])
            ->name('users.toggle-status');

        // Thống kê đơn hàng (đặt TRƯỚC {order} để tránh conflict)
        Route::get('orders/statistics', [OrderStatisticsController::class, 'index'])->name('orders.statistics');
        // Đơn hàng (xem danh sách, chi tiết, xác nhận/cập nhật trạng thái, hủy)
        Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])
            ->name('orders.update-status');
        Route::patch('orders/{order}/cancel', [AdminOrderController::class, 'cancel'])
            ->name('orders.cancel');

        Route::get('returns', [App\Http\Controllers\Admin\ReturnRequestController::class, 'index'])->name('returns.index');
        Route::get('returns/{returnRequest}', [App\Http\Controllers\Admin\ReturnRequestController::class, 'show'])->name('returns.show');
        Route::patch('returns/{returnRequest}/receive', [App\Http\Controllers\Admin\ReturnRequestController::class, 'receive'])->name('returns.receive');
        Route::patch('returns/{returnRequest}/review', [App\Http\Controllers\Admin\ReturnRequestController::class, 'review'])->name('returns.review');
        Route::patch('returns/{returnRequest}/refund', [App\Http\Controllers\Admin\ReturnRequestController::class, 'refund'])->name('returns.refund');

        // Quản lý tồn kho
        Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::post('inventory/{inventory}/import', [InventoryController::class, 'import'])->name('inventory.import');
        Route::post('inventory/{inventory}/export', [InventoryController::class, 'export'])->name('inventory.export');
        Route::post('inventory/{inventory}/adjust', [InventoryController::class, 'adjust'])->name('inventory.adjust');
        Route::get('inventory/history', [InventoryController::class, 'history'])->name('inventory.history');

        // Thống kê doanh thu & xuất báo cáo
        Route::get('revenue', [RevenueController::class, 'index'])->name('revenue.index');
        Route::get('reports/revenue/excel', [ReportController::class, 'revenueExcel'])->name('reports.revenue.excel');
        Route::get('reports/revenue/pdf', [ReportController::class, 'revenuePdf'])->name('reports.revenue.pdf');

        // Bình luận / đánh giá (Admin Reviews management)

        Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');
        Route::patch('reviews/{review}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
        Route::patch('reviews/{review}/hide', [ReviewController::class, 'hide'])->name('reviews.hide');
        //   GEMMINI CHAT

        // Cài đặt
        Route::get('/settings/notifications', [SettingController::class, 'notifications'])->name('settings.notifications');
        Route::post('/settings/notifications', [SettingController::class, 'updateNotifications']);
    });
