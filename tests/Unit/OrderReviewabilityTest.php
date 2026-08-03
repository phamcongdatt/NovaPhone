<?php

namespace Tests\Unit;

use App\Models\Order;
use PHPUnit\Framework\TestCase;

class OrderReviewabilityTest extends TestCase
{
    public function test_paid_delivered_order_can_be_reviewed(): void
    {
        $order = new Order([
            'status' => 'delivered',
            'payment_method' => 'vnpay',
            'payment_status' => 'paid',
        ]);

        $this->assertTrue($order->canBeReviewed());
    }

    public function test_paid_received_order_can_be_reviewed(): void
    {
        $order = new Order([
            'status' => 'received',
            'payment_method' => 'vnpay',
            'payment_status' => 'paid',
        ]);

        $this->assertTrue($order->canBeReviewed());
    }

    public function test_delivered_cod_order_can_be_reviewed_even_before_payment_status_sync(): void
    {
        $order = new Order([
            'status' => 'delivered',
            'payment_method' => 'cod',
            'payment_status' => 'pending',
        ]);

        $this->assertTrue($order->canBeReviewed());
    }

    public function test_processing_order_cannot_be_reviewed(): void
    {
        $order = new Order([
            'status' => 'processing',
            'payment_method' => 'vnpay',
            'payment_status' => 'paid',
        ]);

        $this->assertFalse($order->canBeReviewed());
    }
}
