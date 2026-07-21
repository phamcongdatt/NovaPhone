<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_status_histories', function (Blueprint $table) {
            $table->string('delivery_proof_image')->nullable()->after('note')->comment('Hình ảnh chứng minh giao hàng');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('user_received_at')->nullable()->after('cancelled_by')->comment('Ngày user xác nhận đã nhận');
        });
    }

    public function down(): void
    {
        Schema::table('order_status_histories', function (Blueprint $table) {
            $table->dropColumn('delivery_proof_image');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('user_received_at');
        });
    }
};
