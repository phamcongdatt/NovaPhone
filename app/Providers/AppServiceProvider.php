<?php

namespace App\Providers;

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
            $view->with('categoryLinks', [
                ['label' => 'iPhone', 'href' => route('home', ['brand' => 'apple']) . '#san-pham'],
                ['label' => 'Samsung', 'href' => route('home', ['brand' => 'samsung']) . '#san-pham'],
                ['label' => 'Xiaomi', 'href' => route('home', ['brand' => 'xiaomi']) . '#san-pham'],
                ['label' => 'OPPO', 'href' => route('home', ['brand' => 'oppo']) . '#san-pham'],
                ['label' => 'Vivo', 'href' => route('home', ['brand' => 'vivo']) . '#san-pham'],
                ['label' => 'Realme', 'href' => route('home', ['brand' => 'realme']) . '#san-pham'],
                ['label' => 'Flagship', 'href' => route('home', ['features' => ['featured']]) . '#san-pham'],
            ]);

            if (! request()->is('api/*') && request()->hasSession()) {
                $cartService = app(\App\Services\CartService::class);
                $view->with([
                    'cartCount' => $cartService->getCount(),
                    'cartTotal' => $cartService->getTotal(),
                    'cartItems' => $cartService->getItems(),
                    'wishlistCount' => auth()->check() ? auth()->user()->wishlists()->count() : 0,
                    'wishlistProductIds' => auth()->check() ? auth()->user()->wishlists()->pluck('product_id')->toArray() : [],
                ]);
            } else {
                $view->with([
                    'cartCount' => 0,
                    'cartTotal' => 0,
                    'cartItems' => collect(),
                    'wishlistCount' => 0,
                    'wishlistProductIds' => [],
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
