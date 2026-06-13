<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
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
        // Tùy biến nội dung email xác thực sang tiếng Việt mà vẫn dùng
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
