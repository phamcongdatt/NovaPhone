<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class PerformanceSpecSeeder extends Seeder
{
    /**
     * Bổ sung dữ liệu hiệu năng cho các sản phẩm demo hiện có.
     */
    public function run(): void
    {
        $specifications = [
            'iphone-15-pro-max' => [
                'chipset' => 'Apple A17 Pro',
                'cpu_cores' => '6 nhân (2 hiệu năng + 4 tiết kiệm điện)',
                'gpu' => 'Apple GPU 6 nhân',
                'antutu_score' => 1730000,
                'geekbench_single' => 2950,
                'geekbench_multi' => 7400,
                'display_size_inch' => 6.7,
                'display_type' => 'LTPO Super Retina XDR OLED',
                'refresh_rate' => '120Hz ProMotion',
                'main_camera_mp' => '48MP',
                'ultra_wide_camera_mp' => '12MP',
                'front_camera_mp' => '12MP',
                'video_recording' => '4K@60fps, ProRes',
                'battery_mah' => 4441,
                'charging_speed_w' => 27,
                'ram' => '8GB',
                'os' => 'iOS 18',
                'network_support' => '5G, Wi-Fi 6E, NFC',
            ],
            'iphone-15-pro-max-256gb' => [
                'chipset' => 'Apple A17 Pro',
                'cpu_cores' => '6 nhân (2 hiệu năng + 4 tiết kiệm điện)',
                'gpu' => 'Apple GPU 6 nhân',
                'antutu_score' => 1730000,
                'geekbench_single' => 2950,
                'geekbench_multi' => 7400,
                'display_size_inch' => 6.7,
                'display_type' => 'LTPO Super Retina XDR OLED',
                'refresh_rate' => '120Hz ProMotion',
                'main_camera_mp' => '48MP',
                'ultra_wide_camera_mp' => '12MP',
                'front_camera_mp' => '12MP',
                'video_recording' => '4K@60fps, ProRes',
                'battery_mah' => 4441,
                'charging_speed_w' => 27,
                'ram' => '8GB',
                'os' => 'iOS 18',
                'network_support' => '5G, Wi-Fi 6E, NFC',
            ],
            'samsung-galaxy-s24-ultra-256gb' => [
                'chipset' => 'Snapdragon 8 Gen 3 for Galaxy',
                'cpu_cores' => '8 nhân, tối đa 3.39GHz',
                'gpu' => 'Adreno 750',
                'antutu_score' => 2100000,
                'geekbench_single' => 2250,
                'geekbench_multi' => 7100,
                'display_size_inch' => 6.8,
                'display_type' => 'Dynamic AMOLED 2X',
                'refresh_rate' => '120Hz LTPO',
                'main_camera_mp' => '200MP',
                'ultra_wide_camera_mp' => '12MP',
                'front_camera_mp' => '12MP',
                'video_recording' => '8K@30fps, 4K@120fps',
                'battery_mah' => 5000,
                'charging_speed_w' => 45,
                'ram' => '12GB LPDDR5X',
                'os' => 'Android 14 / One UI 6.1',
                'network_support' => '5G, Wi-Fi 7, NFC',
            ],
            'xiaomi-14t-pro-512gb' => [
                'chipset' => 'MediaTek Dimensity 9300+',
                'cpu_cores' => '8 nhân, tối đa 3.4GHz',
                'gpu' => 'Immortalis-G720 MC12',
                'antutu_score' => 2100000,
                'geekbench_single' => 2240,
                'geekbench_multi' => 7600,
                'display_size_inch' => 6.67,
                'display_type' => 'AMOLED',
                'refresh_rate' => '144Hz',
                'main_camera_mp' => '50MP',
                'ultra_wide_camera_mp' => '12MP',
                'front_camera_mp' => '32MP',
                'video_recording' => '8K@30fps, 4K@60fps',
                'battery_mah' => 5000,
                'charging_speed_w' => 120,
                'ram' => '12GB LPDDR5X',
                'os' => 'Android 14 / HyperOS',
                'network_support' => '5G, Wi-Fi 7, NFC',
            ],
            'iphone-16-128gb' => [
                'chipset' => 'Apple A18',
                'cpu_cores' => '6 nhân (2 hiệu năng + 4 tiết kiệm điện)',
                'gpu' => 'Apple GPU 5 nhân',
                'antutu_score' => 1670000,
                'geekbench_single' => 3100,
                'geekbench_multi' => 7700,
                'display_size_inch' => 6.1,
                'display_type' => 'Super Retina XDR OLED',
                'refresh_rate' => '60Hz',
                'main_camera_mp' => '48MP',
                'ultra_wide_camera_mp' => '12MP',
                'front_camera_mp' => '12MP',
                'video_recording' => '4K@60fps, Dolby Vision',
                'battery_mah' => 3561,
                'charging_speed_w' => 27,
                'ram' => '8GB',
                'os' => 'iOS 18',
                'network_support' => '5G, Wi-Fi 7, NFC',
            ],
        ];

        foreach ($specifications as $slug => $data) {
            $product = Product::where('slug', $slug)->first();

            if ($product) {
                $product->performance()->updateOrCreate([], $data);
            }
        }
    }
}
