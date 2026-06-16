<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lam cung khoa ngoai cac bang lich su don hang: CASCADE -> RESTRICT.
     * Truoc day xoa user/san pham se XOA THEO don hang & chi tiet -> mat lich su ke toan.
     * order_items da luu snapshot (product_name, price...) nen RESTRICT la an toan.
     * App chi xoa MEM san pham (SoftDeletes) nen RESTRICT khong chan luong hien co.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign('orders_user_id_foreign');
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign('order_items_product_id_foreign');
            $table->foreign('product_id')->references('id')->on('products')->restrictOnDelete();
        });

        Schema::table('order_status_histories', function (Blueprint $table) {
            $table->dropForeign('order_status_histories_created_by_foreign');
            $table->foreign('created_by')->references('id')->on('users')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign('orders_user_id_foreign');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign('order_items_product_id_foreign');
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
        });

        Schema::table('order_status_histories', function (Blueprint $table) {
            $table->dropForeign('order_status_histories_created_by_foreign');
            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};