<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@novaphone.vn'],
            [
                'name' => 'Admin NovaPhone',
                'phone' => '0900000001',
                'role' => 'admin',
                'status' => 'active',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@novaphone.vn'],
            [
                'name' => 'Nguyen Van A',
                'phone' => '0900000002',
                'role' => 'user',
                'status' => 'active',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $brands = ['Apple', 'Samsung', 'Xiaomi', 'OPPO', 'Vivo', 'Realme'];
        foreach ($brands as $brandName) {
            Brand::updateOrCreate(
                ['slug' => Str::slug($brandName)],
                [
                    'name' => $brandName,
                    'is_active' => true,
                ]
            );
        }

        $smartphones = Category::updateOrCreate(
            ['slug' => 'dien-thoai-thong-minh'],
            [
                'name' => 'Dien thoai thong minh',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        foreach (['Cao cấp', 'Tầm trung', 'Phổ thông'] as $i => $segment) {
            Category::updateOrCreate(
                ['slug' => Str::slug($segment)],
                [
                    'name' => $segment,
                    'parent_id' => $smartphones->id,
                    'is_active' => true,
                    'sort_order' => $i + 1,
                ]
            );
        }

        $this->call([
            DemoProductSeeder::class,
            PerformanceSpecSeeder::class,
        ]);
    }
}
