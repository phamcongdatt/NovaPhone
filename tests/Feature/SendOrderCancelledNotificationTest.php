<?php

namespace Tests\Feature;

use App\Events\OrderCancelled;
use App\Listeners\SendOrderCancelledNotification;
use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderCancelledNotification as OrderCancelledNotificationClass;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendOrderCancelledNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_listener_sends_notification_to_user(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        $admin = User::factory()->create();

        $event = new OrderCancelled(
            order: $order,
            reason: 'Out of stock',
            cancelledBy: $admin
        );

        $listener = new SendOrderCancelledNotification();
        $listener->handle($event);

        Notification::assertSentTo(
            [$user],
            OrderCancelledNotificationClass::class
        );
    }

    public function test_listener_sends_notification_when_admin_cancels(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        $admin = User::factory()->create(['name' => 'John Admin']);

        $event = new OrderCancelled(
            order: $order,
            reason: 'Test reason',
            cancelledBy: $admin
        );

        $listener = new SendOrderCancelledNotification();
        $listener->handle($event);

        Notification::assertSentTo(
            [$user],
            OrderCancelledNotificationClass::class
        );
    }

    public function test_listener_handles_null_cancelled_by(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $event = new OrderCancelled(
            order: $order,
            reason: 'System cancellation',
            cancelledBy: null
        );

        $listener = new SendOrderCancelledNotification();
        $listener->handle($event);

        Notification::assertSentTo(
            [$user],
            OrderCancelledNotificationClass::class
        );
    }
}
