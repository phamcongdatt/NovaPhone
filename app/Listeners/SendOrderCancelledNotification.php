<?php

namespace App\Listeners;

use App\Events\OrderCancelled;
use App\Notifications\OrderCancelledNotification;
use App\Services\GuestOrderAccessService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class SendOrderCancelledNotification implements ShouldQueue
{
    public function handle(OrderCancelled $event): void
    {
        $order = $event->order;
        $notification = new OrderCancelledNotification(
            $order,
            $event->reason,
            isAdminCancel: $event->cancelledBy !== null,
            adminName: $event->cancelledBy?->name,
            guestOrderUrl: $order->user_id === null
                ? app(GuestOrderAccessService::class)->showUrl($order)
                : null,
        );

        if ($order->user) {
            $order->user->notify($notification);
            return;
        }

        if ($order->customer_email) {
            Notification::route('mail', $order->customer_email)->notify($notification);
        }
    }
}
