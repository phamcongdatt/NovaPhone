<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('return_requests')
            ->where(fn ($query) => $query->whereNull('refund_amount')->orWhere('refund_amount', '<=', 0))
            ->orderBy('id')
            ->each(function ($returnRequest): void {
                $items = (float) DB::table('return_request_items')
                    ->where('return_request_id', $returnRequest->id)
                    ->sum('refund_amount');
                $amount = $items
                    + (float) $returnRequest->original_shipping_refund
                    + (float) $returnRequest->return_shipping_fee;

                if ($amount > 0) {
                    DB::table('return_requests')->where('id', $returnRequest->id)->update([
                        'refund_amount' => $amount,
                        'updated_at' => now(),
                    ]);
                }
            });
    }

    public function down(): void
    {
        // Không xóa số tiền hoàn đã được khôi phục từ chi tiết phiếu.
    }
};
