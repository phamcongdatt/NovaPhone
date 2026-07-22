<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\User;
use App\Services\OrderCancellationService;
use Illuminate\Console\Command;

class TestOrderCancellation extends Command
{
    protected $signature = 'order:test-cancel {orderId} {--admin-user=}';
    protected $description = 'Test order cancellation flow with notification';

    public function __construct(
        private readonly OrderCancellationService $cancellationService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $orderId = $this->argument('orderId');
        $order = Order::find($orderId);

        if (!$order) {
            $this->error("Không tìm thấy đơn hàng ID: {$orderId}");
            return self::FAILURE;
        }

        $this->info("Đơn hàng: {$order->order_code}");
        $this->info("Trạng thái: {$order->status}");
        $this->info("User: {$order->user->name} ({$order->user->email})");

        $reason = $this->ask('Lý do hủy đơn');

        $cancelledBy = null;
        if ($adminId = $this->option('admin-user')) {
            $cancelledBy = User::find($adminId);
            if (!$cancelledBy) {
                $this->error("Không tìm thấy admin ID: {$adminId}");
                return self::FAILURE;
            }
        }

        $this->info("Đang hủy đơn hàng...");

        $result = $this->cancellationService->cancel($order, $reason, $cancelledBy);

        if ($result) {
            $this->info('✓ Đơn hàng đã được hủy thành công!');
            $this->info('✓ Email thông báo sẽ được gửi trong hàng đợi (Queue)');
            return self::SUCCESS;
        }

        $this->error('✗ Không thể hủy đơn hàng');
        return self::FAILURE;
    }
}
