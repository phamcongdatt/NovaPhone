<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class SoldCountService
{
    /**
     * Đồng bộ products.sold_count khi trạng thái đơn hàng thay đổi.
     *
     * - Chuyển vào SALES_STATUSES  → cộng quantity
     * - Chuyển ra khỏi SALES_STATUSES → trừ quantity (không âm)
     * - Chuyển giữa 2 status đều thuộc SALES_STATUSES → không đổi
     */
    public function syncOnStatusChange(Order $order, string $oldStatus, string $newStatus): void
    {
        $wasInSales = in_array($oldStatus, Order::SALES_STATUSES, true);
        $nowInSales = in_array($newStatus, Order::SALES_STATUSES, true);
       if ($wasInSales === $nowInSales) {
            return;
        }

        $sign = $nowInSales ? 1 : -1;
        $this->applyDelta($order, $sign);
    }

    /**
     * Cộng/trừ sold_count theo từng product trong đơn.
     *
     * @param  int  $sign  +1 để cộng, -1 để trừ
     */
    public function applyDelta(Order $order, int $sign): void
    {
        $order->loadMissing('items');

        $qtyByProduct = $order->items
            ->groupBy('product_id')
            ->map(fn ($items) => (int) $items->sum('quantity'));

        foreach ($qtyByProduct as $productId => $quantity) {
            if ($quantity <= 0) {
                continue;
            }

            if ($sign > 0) {
                Product::whereKey($productId)->increment('sold_count', $quantity);
                continue;
            }

            // Trừ nhưng không để âm
            Product::whereKey($productId)
                ->where('sold_count', '>=', $quantity)
                ->decrement('sold_count', $quantity);

            Product::whereKey($productId)
                ->where('sold_count', '<', $quantity)
                ->update(['sold_count' => 0]);
        }
    }

    /**
     * Tính lại sold_count toàn bộ sản phẩm từ order_items (idempotent).
     */
    public function recalculateAll(): int
    {
        $salesStatuses = Order::SALES_STATUSES;
        $placeholders = implode(',', array_fill(0, count($salesStatuses), '?'));

        return DB::update("
            UPDATE products
            SET sold_count = (
                SELECT COALESCE(SUM(oi.quantity), 0)
                FROM order_items oi
                INNER JOIN orders o ON o.id = oi.order_id
                WHERE oi.product_id = products.id
                  AND o.status IN ({$placeholders})
            )
        ", $salesStatuses);
    }
}
