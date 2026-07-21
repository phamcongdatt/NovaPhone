<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly Order $order)
    {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('Xác nhận đơn hàng ' . $this->order->order_code)
            ->markdown('mail.order_created', [
                'user'  => $notifiable,
                'order' => $this->order,
            ]);
    }

    public function toDatabase($notifiable): array
    {
        return [
            'order_id'    => $this->order->id,
            'order_code'  => $this->order->order_code,
            'total_amount' => (float) $this->order->total_amount,
            'message'     => 'Đơn hàng ' . $this->order->order_code . ' đã được tiếp nhận. Tổng tiền: ' . number_format($this->order->total_amount, 0, ',', '.') . 'đ',
        ];
    }
}
