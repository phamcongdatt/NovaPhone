<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Gan coupon vao orders. Cot NULLABLE + co snapshot coupon_code.
     * Checkout hien tai khong set 2 cot nay nen khong pha vo luong cu.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('coupon_id')->nullable()->after('discount_amount')
                  ->constrained('coupons')->nullOnDelete();
            $table->string('coupon_code')->nullable()->after('coupon_id'); // snapshot ma da dung
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['coupon_id']);
            $table->dropColumn(['coupon_id', 'coupon_code']);
        });
    }
};