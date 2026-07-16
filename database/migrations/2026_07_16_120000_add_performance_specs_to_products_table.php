<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Chạy migration: thêm các cột thông số hiệu năng vào bảng products.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // ── Chip & Hiệu năng ──
            $table->string('chipset', 128)->nullable()->after('content');
            $table->string('cpu_cores', 255)->nullable()->after('chipset');
            $table->string('gpu', 128)->nullable()->after('cpu_cores');

            // Điểm benchmark
            $table->decimal('antutu_score', 10, 2)->nullable()->after('gpu');
            $table->decimal('geekbench_single', 10, 2)->nullable()->after('antutu_score');
            $table->decimal('geekbench_multi', 10, 2)->nullable()->after('geekbench_single');

            // ── Màn hình ──
            $table->decimal('display_size_inch', 4, 1)->nullable()->after('geekbench_multi');
            $table->string('display_type', 128)->nullable()->after('display_size_inch');
            $table->string('refresh_rate', 64)->nullable()->after('display_type');

            // ── Camera ──
            $table->string('main_camera_mp', 64)->nullable()->after('refresh_rate');
            $table->string('ultra_wide_camera_mp', 64)->nullable()->after('main_camera_mp');
            $table->string('front_camera_mp', 64)->nullable()->after('ultra_wide_camera_mp');
            $table->string('video_recording', 128)->nullable()->after('front_camera_mp');

            // ── Pin & Sạc ──
            $table->unsignedInteger('battery_mah')->nullable()->after('video_recording');
            $table->unsignedInteger('charging_speed_w')->nullable()->after('battery_mah');

            // ── Bộ nhớ & Kết nối ──
            $table->string('ram', 64)->nullable()->after('charging_speed_w');
            $table->string('os', 128)->nullable()->after('ram');
            $table->string('network_support', 255)->nullable()->after('os');

            // Index cho tiện lọc/sắp xếp sau này
            $table->index(['antutu_score']);
            $table->index(['geekbench_single']);
        });
    }

    /**
     * Hoàn tác migration: xóa các cột vừa thêm.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'chipset',
                'cpu_cores',
                'gpu',
                'antutu_score',
                'geekbench_single',
                'geekbench_multi',
                'display_size_inch',
                'display_type',
                'refresh_rate',
                'main_camera_mp',
                'ultra_wide_camera_mp',
                'front_camera_mp',
                'video_recording',
                'battery_mah',
                'charging_speed_w',
                'ram',
                'os',
                'network_support',
            ]);
        });
    }
};