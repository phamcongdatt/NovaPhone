<?php

namespace Tests\Feature;

use App\Events\OrderCreated;
use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderCreatedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class OrderCreatedNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_created_event_dispatches_notification()
    {
        Notification::fake();

        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'order_code' => 'NVP-TEST123',
            'subtotal' => 1000000,
            'discount_amount' => 100000,
            'shipping_fee' => 30000,
            'total_amount' => 930000,
        ]);

        OrderCreated::dispatch($order);

        Notification::assertSentTo($user, OrderCreatedNotification::class);
    }

    public function test_order_created_notification_includes_correct_data()
    {
        Notification::fake();

        $user = User::factory()->create(['email' => 'test@example.com']);
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'order_code' => 'NVP-TEST456',
            'total_amount' => 500000,
        ]);

        OrderCreated::dispatch($order);

        Notification::assertSentTo(
            $user,
            OrderCreatedNotification::class,
            function ($notification) use ($order) {
                $data = $notification->toDatabase($user);
                return $data['order_code'] === $order->order_code &&
                       $data['total_amount'] == $order->total_amount;
            }
        );
    }

    public function test_order_created_sends_mail_notification()
    {
        Notification::fake();

        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        OrderCreated::dispatch($order);

        Notification::assertSentTo(
            $user,
            OrderCreatedNotification::class,
            function ($notification) {
                return in_array('mail', $notification->via($user));
            }
        );
    }
}
