<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoProductSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::firstOrCreate(
            ['slug' => 'dien-thoai-thong-minh'],
            ['name' => 'Dien thoai thong minh', 'is_active' => true, 'sort_order' => 1],
        );

        $user = User::firstOrCreate(
            ['email' => 'demo@novaphone.vn'],
            [
                'name' => 'Khach hang NovaPhone',
                'phone' => '0900000999',
                'role' => 'user',
                'status' => 'active',
                'password' => Hash::make('password'),
            ],
        );

        foreach ($this->products() as $item) {
            $brand = Brand::firstOrCreate(
                ['slug' => Str::slug($item['brand'])],
                ['name' => $item['brand'], 'is_active' => true],
            );

            $product = Product::updateOrCreate(
                ['slug' => Str::slug($item['name'])],
                [
                    'name' => $item['name'],
                    'description' => $item['description'] ?? $item['name'].' chinh hang tai NovaPhone.',
                    'content' => $item['content'] ?? $item['name'].' co thiet ke hien dai, hieu nang on dinh, camera chat luong va thoi luong pin phu hop nhu cau hang ngay. San pham duoc bao hanh chinh hang va ho tro tra gop tai NovaPhone.',
                    'category_id' => $category->id,
                    'brand_id' => $brand->id,
                    'price' => $item['old_price'] ?? $item['price'],
                    'sale_price' => $item['old_price'] ? $item['price'] : null,
                    'thumbnail' => $item['image'],
                    'sku' => $item['sku'],
                    'is_active' => true,
                    'is_featured' => true,
                    'sold_count' => $item['sold_count'],
                ],
            );

            $this->syncProductImages($product, $item);

            $variant = ProductVariant::updateOrCreate(
                ['sku' => $item['sku'].'-STD'],
                [
                    'product_id' => $product->id,
                    'name' => $item['variant'],
                    'storage' => $item['storage'],
                    'color' => $item['color'],
                    'color_code' => $item['color_code'],
                    'additional_price' => 0,
                    'is_active' => true,
                ],
            );

            Inventory::updateOrCreate(
                ['product_id' => $product->id, 'variant_id' => $variant->id],
                ['quantity' => $item['stock'], 'reserved_quantity' => 0, 'low_stock_threshold' => 5],
            );

            Review::updateOrCreate(
                ['user_id' => $user->id, 'product_id' => $product->id],
                [
                    'rating' => $item['rating'],
                    'comment' => 'San pham hien thi dung thong tin, hinh anh ro va gia tot trong tam gia.',
                    'is_visible' => true,
                ],
            );
        }
    }

    private function products(): array
    {
        return [
            ['name' => 'iPhone 15 Pro Max 256GB', 'brand' => 'Apple', 'price' => 28990000, 'old_price' => 34990000, 'image' => $this->productImage('iphone-15-pro-max-256gb.jpg'), 'sku' => 'IP15PM256', 'variant' => '256GB - Titan Den', 'storage' => '256GB', 'color' => 'Titan Den', 'color_code' => '#2c2c2e', 'stock' => 50, 'sold_count' => 238, 'rating' => 5],
            ['name' => 'Samsung Galaxy S24 Ultra 256GB', 'brand' => 'Samsung', 'price' => 25990000, 'old_price' => 31990000, 'image' => $this->productImage('samsung-galaxy-s24-ultra-256gb.webp'), 'sku' => 'SGS24U256', 'variant' => '256GB - Titanium Gray', 'storage' => '256GB', 'color' => 'Titanium Gray', 'color_code' => '#8f8f8f', 'stock' => 44, 'sold_count' => 196, 'rating' => 5],
            ['name' => 'Xiaomi 14T Pro 512GB', 'brand' => 'Xiaomi', 'price' => 14990000, 'old_price' => 18990000, 'image' => $this->productImage('xiaomi-14t-pro-512gb.jpg'), 'sku' => 'XM14TP512', 'variant' => '512GB - Black', 'storage' => '512GB', 'color' => 'Black', 'color_code' => '#111827', 'stock' => 72, 'sold_count' => 215, 'rating' => 5],
            ['name' => 'OPPO Find X7 Ultra 256GB', 'brand' => 'OPPO', 'price' => 16990000, 'old_price' => 21990000, 'image' => $this->productImage('oppo-find-x7-ultra.webp'), 'sku' => 'OPFX7U256', 'variant' => '256GB - Blue', 'storage' => '256GB', 'color' => 'Blue', 'color_code' => '#2563eb', 'stock' => 28, 'sold_count' => 54, 'rating' => 4],
            ['name' => 'Vivo X100 Pro 256GB', 'brand' => 'Vivo', 'price' => 17990000, 'old_price' => 21990000, 'image' => $this->productImage('vivo-x100-pro.webp'), 'sku' => 'VVX100P256', 'variant' => '256GB - Startrail Blue', 'storage' => '256GB', 'color' => 'Blue', 'color_code' => '#1d4ed8', 'stock' => 31, 'sold_count' => 64, 'rating' => 4],
            ['name' => 'Realme GT 6 512GB', 'brand' => 'Realme', 'price' => 11990000, 'old_price' => 14990000, 'image' => $this->productImage('realme-gt-6-512gb.webp'), 'sku' => 'RMGT6512', 'variant' => '512GB - Silver', 'storage' => '512GB', 'color' => 'Silver', 'color_code' => '#d1d5db', 'stock' => 35, 'sold_count' => 47, 'rating' => 4],
            ['name' => 'Samsung Galaxy S24 Ultra', 'brand' => 'Samsung', 'price' => 25990000, 'old_price' => null, 'image' => $this->productImage('samsung-galaxy-s24-ultra.jpg'), 'sku' => 'SGS24U', 'variant' => '256GB - Titanium Gray', 'storage' => '256GB', 'color' => 'Titanium Gray', 'color_code' => '#8f8f8f', 'stock' => 40, 'sold_count' => 1800, 'rating' => 5],
            ['name' => 'iPhone 15 128GB', 'brand' => 'Apple', 'price' => 20990000, 'old_price' => null, 'image' => $this->productImage('iphone-15-128gb.webp'), 'sku' => 'IP15128', 'variant' => '128GB - Blue', 'storage' => '128GB', 'color' => 'Blue', 'color_code' => '#93c5fd', 'stock' => 64, 'sold_count' => 1600, 'rating' => 5],
            ['name' => 'Xiaomi Redmi Note 13 Pro', 'brand' => 'Xiaomi', 'price' => 6990000, 'old_price' => null, 'image' => $this->productImage('xiaomi-redmi-note-13-pro.webp'), 'sku' => 'RDN13P', 'variant' => '256GB - Black', 'storage' => '256GB', 'color' => 'Black', 'color_code' => '#111827', 'stock' => 88, 'sold_count' => 1200, 'rating' => 4],
            ['name' => 'iPhone 16 128GB', 'brand' => 'Apple', 'price' => 22990000, 'old_price' => null, 'image' => $this->productImage('iphone-16-128gb.webp'), 'sku' => 'IP16128', 'variant' => '128GB - Ultramarine', 'storage' => '128GB', 'color' => 'Ultramarine', 'color_code' => '#3b82f6', 'stock' => 36, 'sold_count' => 15, 'rating' => 5],
            ['name' => 'Samsung Galaxy Z Flip6', 'brand' => 'Samsung', 'price' => 26990000, 'old_price' => null, 'image' => $this->productImage('samsung-galaxy-z-flip6.webp'), 'sku' => 'SGZFLIP6', 'variant' => '256GB - Mint', 'storage' => '256GB', 'color' => 'Mint', 'color_code' => '#99f6e4', 'stock' => 22, 'sold_count' => 10, 'rating' => 5],
            ['name' => 'Xiaomi 14 Ultra', 'brand' => 'Xiaomi', 'price' => 24990000, 'old_price' => null, 'image' => $this->productImage('xiaomi-14-ultra.webp'), 'sku' => 'XM14U', 'variant' => '512GB - White', 'storage' => '512GB', 'color' => 'White', 'color_code' => '#f8fafc', 'stock' => 18, 'sold_count' => 8, 'rating' => 5],
            ['name' => 'OPPO Reno12 Pro', 'brand' => 'OPPO', 'price' => 12990000, 'old_price' => null, 'image' => $this->productImage('oppo-reno12-pro.webp'), 'sku' => 'OPR12P', 'variant' => '512GB - Nebula Silver', 'storage' => '512GB', 'color' => 'Silver', 'color_code' => '#d1d5db', 'stock' => 26, 'sold_count' => 12, 'rating' => 5],
        ];
    }

    private function syncProductImages(Product $product, array $item): void
    {
        $images = $this->productImages($item);

        foreach ($images as $sortOrder => $imageUrl) {
            ProductImage::updateOrCreate(
                ['product_id' => $product->id, 'sort_order' => $sortOrder],
                [
                    'image_url' => $imageUrl,
                    'is_primary' => $sortOrder === 0,
                ],
            );
        }

        ProductImage::where('product_id', $product->id)
            ->where('sort_order', '>=', count($images))
            ->delete();
    }

    private function productImages(array $item): array
    {
        return [
            $item['image'],
        ];
    }

    private function productImage(string $filename): string
    {
        return '/images/products/'.$filename;
    }
}
