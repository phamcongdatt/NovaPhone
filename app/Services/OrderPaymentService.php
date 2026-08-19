<?php

namespace App\Services;

use App\Models\Order;

class OrderPaymentService
{
    /**
     * COD được xem là đã thanh toán khi giao hàng thành công.
     * firstOrCreate giúp thao tác an toàn khi được gọi lại từ luồng xác nhận đã nhận.
     */
    public function markCodAsPaid(Order $order): void
    {
        if ($order->payment_method !== 'cod' || $order->payment_status === 'refunded') {
            return;
        }

        if ($order->payment_status !== 'paid') {
            $order->update(['payment_status' => 'paid']);
        }

        $order->payments()->firstOrCreate(
            [
                'gateway' => 'cod',
                'transaction_code' => 'COD-'.$order->order_code,
            ],
            [
                'amount' => $order->total_amount,
                'status' => 'success',
                'response_code' => '00',
                'response_message' => 'Đã thu tiền khi giao hàng',
                'paid_at' => now(),
            ]
        );
    }
}
