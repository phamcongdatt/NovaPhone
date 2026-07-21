<?php

namespace App\Listeners;

use App\Events\OrderCancelled;
use App\Notifications\OrderCancelledNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOrderCancelledNotification implements ShouldQueue
{
    public function handle(OrderCancelled $event): void
    {
        $event->order->user->notify(
            new OrderCancelledNotification(
                $event->order,
                $event->reason,
                $event->cancelledBy,
                $event->cancelledBy?->name
            )
        );
    }
}
