<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Notifications\OrderCreatedNotification;
use App\Services\GuestOrderAccessService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class SendOrderCreatedNotification implements ShouldQueue
{
    public function handle(OrderCreated $event): void
    {
        $order = $event->order;

        if ($order->user) {
            $order->user->notify(new OrderCreatedNotification($order));
            return;
        }

        if ($order->customer_email) {
            Notification::route('mail', $order->customer_email)->notify(
                new OrderCreatedNotification(
                    $order,
                    app(GuestOrderAccessService::class)->showUrl($order)
                )
            );
        }
    }
}
