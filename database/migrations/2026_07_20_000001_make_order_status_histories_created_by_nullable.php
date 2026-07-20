<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cho phép created_by = null để đại diện cho các thay đổi trạng thái đơn hàng
 * do hệ thống tự động thực hiện (vd. lệnh orders:cancel-stale hủy đơn quá hạn
 * thanh toán mà không có admin/khách hàng nào thao tác).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_status_histories', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
        });

        Schema::table('order_status_histories', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->change();
            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_status_histories', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
        });

        Schema::table('order_status_histories', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable(false)->change();
            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
