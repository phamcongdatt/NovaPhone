<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Sao chép thông số hiệu năng hiện có từ products sang product_performances.
     */
    public function up(): void
    {
        $fields = [
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

        DB::table('products')
            ->where(function ($query) use ($fields) {
                foreach ($fields as $field) {
                    $query->orWhereNotNull($field);
                }
            })
            ->orderBy('id')
            ->chunkById(100, function ($products) use ($fields) {
                $timestamp = now();
                $rows = $products->map(function ($product) use ($fields, $timestamp) {
                    $data = [
                        'product_id' => $product->id,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ];

                    foreach ($fields as $field) {
                        $data[$field] = $product->{$field};
                    }

                    return $data;
                })->all();

                DB::table('product_performances')->upsert(
                    $rows,
                    ['product_id'],
                    array_merge($fields, ['updated_at'])
                );
            });
    }

    /**
     * Không xóa dữ liệu ở bảng mới khi rollback riêng migration backfill.
     */
    public function down(): void
    {
        // Dữ liệu thuộc product_performances; migration create table xử lý việc rollback bảng.
    }
};
