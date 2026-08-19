<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('delivered_at')->nullable()->after('user_received_at');
        });

        Schema::table('return_requests', function (Blueprint $table) {
            $table->decimal('original_shipping_refund', 15, 2)->default(0)->after('refund_amount');
            $table->decimal('return_shipping_fee', 15, 2)->default(0)->after('original_shipping_refund');
        });

        DB::table('orders')
            ->whereIn('status', ['delivered', 'received'])
            ->whereNull('delivered_at')
            ->orderBy('id')
            ->chunkById(100, function ($orders) {
                foreach ($orders as $order) {
                    $deliveredAt = DB::table('order_status_histories')
                        ->where('order_id', $order->id)
                        ->where('status', 'delivered')
                        ->value('created_at');

                    DB::table('orders')->where('id', $order->id)->update([
                        'delivered_at' => $deliveredAt
                            ?? $order->user_received_at
                            ?? $order->updated_at
                            ?? $order->created_at,
                    ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('return_requests', function (Blueprint $table) {
            $table->dropColumn(['original_shipping_refund', 'return_shipping_fee']);
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('delivered_at');
        });
    }
};
