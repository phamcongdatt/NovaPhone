<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $fields = [
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
    ];

    /**
     * Xóa các cột hiệu năng cũ sau khi migration backfill đã hoàn tất.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['antutu_score']);
            $table->dropIndex(['geekbench_single']);
            $table->dropColumn($this->fields);
        });
    }

    /**
     * Phục hồi cột cũ và copy dữ liệu lại từ bảng product_performances.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('chipset', 128)->nullable()->after('content');
            $table->string('cpu_cores', 255)->nullable()->after('chipset');
            $table->string('gpu', 128)->nullable()->after('cpu_cores');
            $table->decimal('antutu_score', 10, 2)->nullable()->after('gpu');
            $table->decimal('geekbench_single', 10, 2)->nullable()->after('antutu_score');
            $table->decimal('geekbench_multi', 10, 2)->nullable()->after('geekbench_single');
            $table->decimal('display_size_inch', 4, 1)->nullable()->after('geekbench_multi');
            $table->string('display_type', 128)->nullable()->after('display_size_inch');
            $table->string('refresh_rate', 64)->nullable()->after('display_type');
            $table->string('main_camera_mp', 64)->nullable()->after('refresh_rate');
            $table->string('ultra_wide_camera_mp', 64)->nullable()->after('main_camera_mp');
            $table->string('front_camera_mp', 64)->nullable()->after('ultra_wide_camera_mp');
            $table->string('video_recording', 128)->nullable()->after('front_camera_mp');
            $table->unsignedInteger('battery_mah')->nullable()->after('video_recording');
            $table->unsignedInteger('charging_speed_w')->nullable()->after('battery_mah');
            $table->string('ram', 64)->nullable()->after('charging_speed_w');
            $table->string('os', 128)->nullable()->after('ram');
            $table->string('network_support', 255)->nullable()->after('os');
            $table->index(['antutu_score']);
            $table->index(['geekbench_single']);
        });

        DB::table('product_performances')
            ->orderBy('product_id')
            ->chunkById(100, function ($performances) {
                foreach ($performances as $performance) {
                    $data = [];

                    foreach ($this->fields as $field) {
                        $data[$field] = $performance->{$field};
                    }

                    DB::table('products')
                        ->where('id', $performance->product_id)
                        ->update($data);
                }
            }, 'product_id');
    }
};
