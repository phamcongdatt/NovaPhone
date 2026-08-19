<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            DB::table('orders')
                ->where('payment_method', 'cod')
                ->where('payment_status', 'pending')
                ->whereIn('status', ['delivered', 'received'])
                ->orderBy('id')
                ->chunkById(100, function ($orders) {
                    foreach ($orders as $order) {
                        DB::table('orders')->where('id', $order->id)->update([
                            'payment_status' => 'paid',
                            'updated_at' => now(),
                        ]);

                        $transactionCode = 'COD-'.$order->order_code;
                        if (! DB::table('payment_transactions')->where('transaction_code', $transactionCode)->exists()) {
                            DB::table('payment_transactions')->insert([
                                'order_id' => $order->id,
                                'gateway' => 'cod',
                                'transaction_code' => $transactionCode,
                                'amount' => $order->total_amount,
                                'status' => 'success',
                                'response_code' => '00',
                                'response_message' => 'Đồng bộ thanh toán COD đã giao/đã nhận',
                                'paid_at' => $order->user_received_at ?? $order->updated_at ?? now(),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                });
        });
    }

    public function down(): void
    {
        // Không hoàn tác dữ liệu thanh toán đã được xác nhận hợp lệ.
    }
};
