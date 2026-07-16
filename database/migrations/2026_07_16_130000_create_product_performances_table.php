<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tạo bảng chuyên biệt cho toàn bộ thông số hiệu năng điện thoại.
     */
    public function up(): void
    {
        Schema::create('product_performances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->unique()->constrained()->cascadeOnDelete();

            // ── Chip & hiệu năng ──
            $table->string('chipset', 128)->nullable();
            $table->string('cpu_cores', 255)->nullable();
            $table->string('gpu', 128)->nullable();
            $table->decimal('antutu_score', 10, 2)->nullable()->index();
            $table->decimal('geekbench_single', 10, 2)->nullable()->index();
            $table->decimal('geekbench_multi', 10, 2)->nullable();

            // ── Màn hình ──
            $table->decimal('display_size_inch', 4, 1)->nullable();
            $table->string('display_type', 128)->nullable();
            $table->string('refresh_rate', 64)->nullable();

            // ── Camera ──
            $table->string('main_camera_mp', 64)->nullable();
            $table->string('ultra_wide_camera_mp', 64)->nullable();
            $table->string('front_camera_mp', 64)->nullable();
            $table->string('video_recording', 128)->nullable();

            // ── Pin & kết nối ──
            $table->unsignedInteger('battery_mah')->nullable();
            $table->unsignedInteger('charging_speed_w')->nullable();
            $table->string('ram', 64)->nullable();
            $table->string('os', 128)->nullable();
            $table->string('network_support', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Xóa bảng thông số hiệu năng khi rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_performances');
    }
};
