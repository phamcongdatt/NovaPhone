<?php

namespace App\Providers;

use App\Models\Brand;
use App\Services\CartService;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            static $categoryLinks = null;
            static $brandLinks = null;
            static $userData = null;

            if ($categoryLinks === null) {
                $categoryLinks = \App\Models\Category::query()
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get(['name', 'slug'])
                    ->map(fn(\App\Models\Category $category) => [
                        'label' => $category->name,
                        'href' => route('home', ['category' => $category->slug]) . '#san-pham',
                    ]);

                $categoryLinks->push([
                    'label' => 'Flagship',
                    'href' => route('home', ['features' => ['featured']]) . '#san-pham',
                ]);

                $categoryLinks->prepend([
                    'label' => 'Tất cả sản phẩm',
                    'href' => route('home') . '#san-pham',
                ]);
            }

            if ($brandLinks === null) {
                $brandLinks = Brand::query()
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get(['name', 'slug'])
                    ->map(fn(Brand $brand) => [
                        'label' => $brand->name,
                        'href' => route('home', ['brand' => $brand->slug]) . '#san-pham',
                    ]);
            }

            $view->with([
                'categoryLinks' => $categoryLinks,
                'brandLinks' => $brandLinks,
            ]);

            if (!request()->is('api/*') && request()->hasSession()) {
                if ($userData === null) {
                    $cartService = app(CartService::class);
                    $userData = [
                        'cartCount' => $cartService->getCount(),
                        'cartTotal' => $cartService->getTotal(),
                        'cartItems' => $cartService->getItems(),
                        'wishlistCount' => auth()->check() ? auth()->user()->wishlists()->count() : 0,
                        'wishlistProductIds' => auth()->check() ? auth()->user()->wishlists()->pluck('product_id')->toArray() : [],
                        'compareCount' => app(\App\Services\CompareService::class)->getCount(),
                        'compareProductIds' => app(\App\Services\CompareService::class)->getProductIds(),
                    ];
                }
                $view->with($userData);
            } else {
                $view->with([
                    'cartCount' => 0,
                    'cartTotal' => 0,
                    'cartItems' => collect(),
                    'wishlistCount' => 0,
                    'wishlistProductIds' => [],
                    'compareCount' => 0,
                    'compareProductIds' => [],
                ]);
            }
        });

        // Email xác thực (tiếng Việt)
        VerifyEmail::toMailUsing(function (object $notifiable, string $url): MailMessage {
            return (new MailMessage)
                ->subject('Xác thực địa chỉ email — NovaPhone')
                ->greeting('Xin chào ' . $notifiable->name . ',')
                ->line('Cảm ơn bạn đã đăng ký tài khoản tại NovaPhone. Vui lòng nhấn nút bên dưới để xác thực địa chỉ email của bạn.')
                ->action('Xác thực email', $url)
                ->line('Liên kết này sẽ hết hạn sau 60 phút.')
                ->line('Nếu bạn không tạo tài khoản này, vui lòng bỏ qua email.')
                ->salutation('Trân trọng, Đội ngũ NovaPhone');
        });
    }
}
