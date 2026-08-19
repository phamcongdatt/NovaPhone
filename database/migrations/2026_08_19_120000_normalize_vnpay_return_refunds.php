<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('return_requests')
            ->whereIn('order_id', DB::table('orders')->select('id')->where('payment_method', 'vnpay'))
            ->whereIn('status', ['requested', 'return_shipping', 'store_received', 'approved'])
            ->update(['return_shipping_fee' => 0, 'updated_at' => now()]);
    }

    public function down(): void
    {
        // Không khôi phục khoản phí sai đã làm số tiền hoàn vượt giao dịch VNPay gốc.
    }
};
