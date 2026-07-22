<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\OrderCancellationService;
use Illuminate\Console\Command;

class CancelStaleOrdersCommand extends Command
{
    protected $signature = 'orders:cancel-stale';

    protected $description = 'Tự động hủy các đơn hàng thanh toán trực tuyến bị treo ở trạng thái "pending" quá lâu, hoàn kho và giải phóng mã giảm giá';

    public function handle(OrderCancellationService $cancellationService): int
    {
        $timeoutMinutes = (int) config('shop.pending_order_timeout_minutes');

        // COD không áp dụng auto-cancel: đơn "pending" của COD là chờ nhân viên xác nhận,
        // không phải chờ thanh toán trực tuyến hết hạn.
        $staleOrders = Order::where('status', 'pending')
            ->where('payment_status', 'pending')
            ->where('payment_method', '!=', 'cod')
            ->where('created_at', '<=', now()->subMinutes($timeoutMinutes))
            ->get();

        if ($staleOrders->isEmpty()) {
            $this->info('Không có đơn hàng nào quá hạn thanh toán.');
            return self::SUCCESS;
        }

        $cancelled = 0;
        foreach ($staleOrders as $order) {
            $reason = "Tự động hủy do quá hạn thanh toán ({$timeoutMinutes} phút).";
            if ($cancellationService->cancel($order, $reason)) {
                $cancelled++;
            }
        }

        $this->info("Đã tự động hủy {$cancelled}/{$staleOrders->count()} đơn hàng quá hạn thanh toán.");

        return self::SUCCESS;
    }
}
