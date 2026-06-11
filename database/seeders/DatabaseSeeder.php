<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin account
        User::create([
            'name'     => 'Admin NovaPhone',
            'email'    => 'admin@novaphone.vn',
            'phone'    => '0900000001',
            'role'     => 'admin',
            'status'   => 'active',
            'password' => Hash::make('password'),
        ]);

        // Test user
        User::create([
            'name'     => 'Nguyễn Văn A',
            'email'    => 'user@novaphone.vn',
            'phone'    => '0900000002',
            'role'     => 'user',
            'status'   => 'active',
            'password' => Hash::make('password'),
        ]);

        // Brands
        $brands = ['Apple', 'Samsung', 'Xiaomi', 'OPPO', 'Vivo', 'Realme'];
        foreach ($brands as $brandName) {
            Brand::create([
                'name'      => $brandName,
                'slug'      => Str::slug($brandName),
                'is_active' => true,
            ]);
        }

        // Categories
        $smartphones = Category::create([
            'name'       => 'Điện thoại thông minh',
            'slug'       => 'dien-thoai-thong-minh',
            'is_active'  => true,
            'sort_order' => 1,
        ]);

        foreach (['Cao cấp', 'Tầm trung', 'Phổ thông'] as $i => $segment) {
            Category::create([
                'name'       => $segment,
                'slug'       => Str::slug($segment),
                'parent_id'  => $smartphones->id,
                'is_active'  => true,
                'sort_order' => $i + 1,
            ]);
        }

        // Sample product
        $apple   = Brand::where('slug', 'apple')->first();
        $product = Product::create([
            'name'        => 'iPhone 15 Pro Max',
            'slug'        => 'iphone-15-pro-max',
            'description' => 'iPhone 15 Pro Max - Chip A17 Pro mạnh mẽ',
            'category_id' => $smartphones->id,
            'brand_id'    => $apple->id,
            'price'       => 34990000,
            'sale_price'  => 32990000,
            'sku'         => 'IP15PM',
            'is_active'   => true,
            'is_featured' => true,
        ]);

        $variant = ProductVariant::create([
            'product_id'       => $product->id,
            'name'             => '256GB - Titan Đen',
            'storage'          => '256GB',
            'color'            => 'Titan Đen',
            'color_code'       => '#2C2C2E',
            'additional_price' => 0,
            'sku'              => 'IP15PM-256-BLK',
        ]);

        Inventory::create([
            'product_id'          => $product->id,
            'variant_id'          => $variant->id,
            'quantity'            => 50,
            'low_stock_threshold' => 5,
        ]);
    }
}
