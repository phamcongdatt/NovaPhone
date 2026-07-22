<?php

namespace App\Services;

use App\Events\OrderCancelled;
use App\Models\Inventory;
use App\Models\InventoryHistory;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Hủy đơn hàng theo một quy trình duy nhất, dùng chung cho:
 * - Khách hàng tự hủy đơn (OrderController::cancel)
 * - Admin hủy đơn (Admin\OrderController::cancel)
 * - Lệnh tự động hủy đơn quá hạn thanh toán (CancelStaleOrdersCommand)
 *
 * Đảm bảo mọi đường hủy đơn đều nhất quán: hoàn kho, đồng bộ sold_count,
 * ghi lịch sử trạng thái, giải phóng lượt dùng mã giảm giá, và gửi thông báo cho khách.
 */
class OrderCancellationService
{
    public function __construct(
        private readonly SoldCountService $soldCountService
    ) {
    }

    /**
     * Hủy một đơn hàng. Trả về false nếu đơn không còn ở trạng thái có thể hủy
     * (đã bị hủy/xử lý bởi luồng khác - ví dụ thanh toán vừa thành công ngay trước đó).
     */
    public function cancel(Order $order, string $reason, ?User $cancelledBy = null): bool
    {
        $result = DB::transaction(function () use ($order, $reason, $cancelledBy) {
            // Khóa dòng đơn hàng để tránh race condition với vnpayReturn() cập nhật đồng thời
            $locked = Order::whereKey($order->id)->lockForUpdate()->first();

            if (! $locked || ! $locked->isCancellable()) {
                return false;
            }

            $oldStatus = $locked->status;

            $locked->update([
                'status'           => 'cancelled',
                'cancelled_reason' => $reason,
                'cancelled_by'     => $cancelledBy?->id,
            ]);

            OrderStatusHistory::create([
                'order_id'   => $locked->id,
                'status'     => 'cancelled',
                'note'       => $reason,
                'created_by' => $cancelledBy?->id,
            ]);

            // Nếu đơn đã thuộc nhóm sales thì trừ lại sold_count
            $this->soldCountService->syncOnStatusChange($locked, $oldStatus, 'cancelled');

            // Hoàn lại tồn kho & ghi lịch sử xuất/nhập kho
            $locked->loadMissing('items', 'orderCoupons.coupon', 'user');

            foreach ($locked->items as $item) {
                $inventory = $item->variant_id
                    ? Inventory::where('product_id', $item->product_id)->where('variant_id', $item->variant_id)->first()
                    : Inventory::where('product_id', $item->product_id)->whereNull('variant_id')->first();

                if ($inventory) {
                    $inventory->increment('quantity', $item->quantity);
                }

                InventoryHistory::create([
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'type'       => 'import',
                    'quantity'   => $item->quantity,
                    'note'       => 'Hoàn kho tự động do huỷ đơn hàng #' . $locked->order_code,
                    'user_id'    => $cancelledBy?->id,
                ]);
            }

            // Giải phóng lượt dùng mã giảm giá đã áp dụng cho đơn này
            foreach ($locked->orderCoupons as $orderCoupon) {
                $orderCoupon->coupon?->decrement('used_count');
            }

            return true;
        });

        // Dispatch event sau transaction (outside transaction để tránh lock)
        if ($result) {
            OrderCancelled::dispatch($order->fresh(), $reason, $cancelledBy);
        }

        return $result;
    }
}
