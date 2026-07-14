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
            // Dữ liệu điều hướng dùng chung cho mọi trang sử dụng layout chính.

            $categoryLinks = \App\Models\Category::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['name', 'slug'])
                ->map(fn (\App\Models\Category $category) => [
                    'label' => $category->name,
            $categoryLinks = Brand::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['name', 'slug'])
                ->map(fn (Brand $brand) => [
                    'label' => $brand->name,
                    'href' => route('home', ['brand' => $brand->slug]).'#san-pham',

                ]);

            $categoryLinks->push([
                'label' => 'Flagship',
                'href' => route('home', ['features' => ['featured']]).'#san-pham',

            ]);

            $categoryLinks->prepend([
                'label' => 'Tất cả sản phẩm',
                'href' => route('home').'#san-pham',
            ]);

            $brandLinks = \App\Models\Brand::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['name', 'slug'])
                ->map(fn (\App\Models\Brand $brand) => [
                    'label' => $brand->name,
                    'href' => route('home', ['brand' => $brand->slug]).'#san-pham',
                ]);

            $view->with([
                'categoryLinks' => $categoryLinks,
                'brandLinks' => $brandLinks,


            ]);

            $view->with('categoryLinks', $categoryLinks);

            if (! request()->is('api/*') && request()->hasSession()) {
                $cartService = app(CartService::class);
                $view->with([
                    'cartCount' => $cartService->getCount(),
                    'cartTotal' => $cartService->getTotal(),
                    'cartItems' => $cartService->getItems(),
                    'wishlistCount' => auth()->check() ? auth()->user()->wishlists()->count() : 0,
                    'wishlistProductIds' => auth()->check() ? auth()->user()->wishlists()->pluck('product_id')->toArray() : [],
                    'compareCount' => app(\App\Services\CompareService::class)->getCount(),
                    'compareProductIds' => app(\App\Services\CompareService::class)->getProductIds(),
                ]);
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
                ->greeting('Xin chào '.$notifiable->name.',')
                ->line('Cảm ơn bạn đã đăng ký tài khoản tại NovaPhone. Vui lòng nhấn nút bên dưới để xác thực địa chỉ email của bạn.')
                ->action('Xác thực email', $url)
                ->line('Liên kết này sẽ hết hạn sau 60 phút.')
                ->line('Nếu bạn không tạo tài khoản này, vui lòng bỏ qua email.')
                ->salutation('Trân trọng, Đội ngũ NovaPhone');
        });
    }
}
