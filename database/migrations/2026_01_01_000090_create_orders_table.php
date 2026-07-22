<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('order_code')->unique();
            $table->enum('status', [
                'pending',      // chờ xác nhận
                'confirmed',    // đã xác nhận
                'processing',   // đang xử lý
                'shipping',     // đang giao hàng
                'delivered',    // đã giao (admin xác nhận)
                'received',     // đã nhận (user xác nhận)
                'cancelled',    // đã hủy
            ])->default('pending');
            $table->enum('payment_method', ['cod', 'vnpay', 'momo'])->default('cod');
            $table->enum('payment_status', ['pending', 'paid', 'refunded'])->default('pending');
            $table->decimal('subtotal', 15, 2);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('shipping_fee', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2);
            // Snapshot địa chỉ giao hàng tại thời điểm đặt hàng
            $table->string('shipping_full_name');
            $table->string('shipping_phone', 15);
            $table->string('shipping_address');
            $table->string('shipping_ward');
            $table->string('shipping_district');
            $table->string('shipping_province');
            $table->text('note')->nullable();
            $table->text('cancelled_reason')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
