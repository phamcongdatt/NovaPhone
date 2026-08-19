<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\InventoryHistory;
use App\Models\PaymentTransaction;
use App\Models\ReturnRequest;
use App\Models\User;
use App\Notifications\ReturnCompletedNotification;
use Illuminate\Support\Facades\DB;

class ReturnRefundService
{
    public function complete(ReturnRequest $returnRequest, User $admin, string $method, string $reference): bool
    {
        $completed = DB::transaction(function () use ($returnRequest, $admin, $method, $reference) {
            $locked = ReturnRequest::whereKey($returnRequest->id)->lockForUpdate()->first();
            if (! $locked || ! in_array($locked->status, ['approved', 'refund_processing', 'refund_review_required'], true)) {
                return false;
            }

            $locked->load('items.orderItem', 'order.items');
            $refundAmount = $locked->calculatedRefundAmount();

            foreach ($locked->items as $returnItem) {
                $orderItem = $returnItem->orderItem;
                $inventory = Inventory::where('product_id', $orderItem->product_id)
                    ->when($orderItem->variant_id, fn ($query) => $query->where('variant_id', $orderItem->variant_id), fn ($query) => $query->whereNull('variant_id'))
                    ->lockForUpdate()->first();

                $inventory?->increment('quantity', $returnItem->quantity);
                InventoryHistory::create([
                    'product_id' => $orderItem->product_id,
                    'variant_id' => $orderItem->variant_id,
                    'type' => 'import',
                    'quantity' => $returnItem->quantity,
                    'note' => 'Hoàn kho từ yêu cầu '.$locked->return_code,
                    'user_id' => $admin->id,
                ]);
            }

            PaymentTransaction::create([
                'order_id' => $locked->order_id,
                'gateway' => $method,
                'transaction_code' => $reference,
                'amount' => $refundAmount,
                'status' => 'refunded',
                'response_message' => 'Hoàn tiền theo yêu cầu '.$locked->return_code,
                'paid_at' => now(),
            ]);

            $returnedQuantities = $locked->items->pluck('quantity', 'order_item_id');
            $isFullReturn = $locked->order->items->every(
                fn ($item) => (int) ($returnedQuantities[$item->id] ?? 0) === (int) $item->quantity
            );
            if ($isFullReturn) {
                $locked->order->update(['payment_status' => 'refunded']);
            }

            $locked->update([
                'status' => 'completed', 'refund_amount' => $refundAmount,
                'refund_method' => $method, 'refund_reference' => $reference,
                'refunded_at' => now(), 'completed_at' => now(),
            ]);

            return true;
        });

        if ($completed) {
            $fresh = $returnRequest->fresh(['order', 'user']);
            $fresh->user->notify(new ReturnCompletedNotification($fresh));
        }

        return $completed;
    }
}
