<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPerformance extends Model
{
    protected $fillable = [
        'product_id',
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

    protected function casts(): array
    {
        return [
            'antutu_score' => 'decimal:2',
            'geekbench_single' => 'decimal:2',
            'geekbench_multi' => 'decimal:2',
            'display_size_inch' => 'decimal:1',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
