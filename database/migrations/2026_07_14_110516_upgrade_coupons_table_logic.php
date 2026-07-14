<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Modify the `type` column to string to support more actions.
        DB::statement("ALTER TABLE coupons MODIFY COLUMN type ENUM('fixed', 'percent', 'free_shipping', 'gift', 'reward_points') DEFAULT 'fixed'");

        Schema::table('coupons', function (Blueprint $table) {
            $table->foreignId('gift_product_id')->nullable()->after('value')->constrained('products')->nullOnDelete();
        });

        // 2. Create coupon_category pivot
        Schema::create('coupon_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['coupon_id', 'category_id']);
        });

        // 3. Create coupon_product pivot
        Schema::create('coupon_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['coupon_id', 'product_id']);
        });

        // 4. Create coupon_user_eligibility pivot (dành cho người dùng được phép dùng mã)
        Schema::create('coupon_user_eligibility', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['coupon_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupon_user_eligibility');
        Schema::dropIfExists('coupon_product');
        Schema::dropIfExists('coupon_category');

        Schema::table('coupons', function (Blueprint $table) {
            $table->dropForeign(['gift_product_id']);
            $table->dropColumn('gift_product_id');
        });

        DB::statement("ALTER TABLE coupons MODIFY COLUMN type ENUM('fixed', 'percent') DEFAULT 'fixed'");
    }
};
