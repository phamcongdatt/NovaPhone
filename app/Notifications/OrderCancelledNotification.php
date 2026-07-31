<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Order $order,
        private readonly string $reason,
        private readonly bool $isAdminCancel = false,
        private readonly ?string $adminName = null,
        private readonly ?string $guestOrderUrl = null,
    ) {
    }

    public function via($notifiable): array
    {
        return $notifiable instanceof AnonymousNotifiable
            ? ['mail']
            : ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $cancelledAt = $this->order->statusHistories()
            ->where('status', 'cancelled')
            ->first()?->created_at;
        $orderUrl = $this->guestOrderUrl ?? route('orders.show', $this->order);

        return (new MailMessage())
            ->subject('Đơn hàng ' . $this->order->order_code . ' đã bị hủy')
            ->markdown('mail.order_cancelled', [
                'user' => $notifiable,
                'order' => $this->order,
                'reason' => $this->reason,
                'isAdminCancel' => $this->isAdminCancel,
                'adminName' => $this->adminName,
                'cancelledAt' => $cancelledAt,
                'orderUrl' => $orderUrl,
            ]);
    }

    public function toDatabase($notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_code' => $this->order->order_code,
            'reason' => $this->reason,
            'is_admin_cancel' => $this->isAdminCancel,
            'admin_name' => $this->adminName,
            'message' => 'Đơn hàng ' . $this->order->order_code . ' đã bị hủy. Lý do: ' . $this->reason,
        ];
    }
}
