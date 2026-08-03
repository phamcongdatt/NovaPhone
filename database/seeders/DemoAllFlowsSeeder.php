<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\FlashSale;
use App\Models\FlashSaleItem;
use App\Models\Inventory;
use App\Models\InventoryHistory;
use App\Models\Order;
use App\Models\OrderCoupon;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\PaymentTransaction;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductPerformance;
use App\Models\ProductVariant;
use App\Models\Review;
use App\Models\Setting;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoAllFlowsSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $users = $this->seedUsers();
            $brands = $this->seedBrands();
            $categories = $this->seedCategories();
            $products = $this->seedProducts($brands, $categories);
            $variants = $this->seedVariants($products);

            $this->seedInventories($products, $variants);
            $this->seedImages($products);
            $this->seedPerformances($products);
            $flashSaleItems = $this->seedFlashSales($products);
            $coupons = $this->seedCoupons($users, $products, $categories);
            $this->seedContent($users['admin']);
            $this->seedAddresses($users);
            $this->seedWishlistAndCart($users['customer'], $products, $variants);

            $orders = $this->seedOrders($users, $products, $variants, $flashSaleItems, $coupons);
            $this->seedReviews($users, $products, $orders);
            $this->seedInventoryHistories($users['admin'], $products, $variants);
            $this->seedSettings();
            $this->seedNotifications($users['customer'], $orders);
        });

        $this->command?->info('DemoAllFlowsSeeder: dữ liệu demo user/admin đã được tạo hoặc cập nhật.');
    }

    /** @return array<string, User> */
    private function seedUsers(): array
    {
        $definitions = [
            'admin' => [
                'name' => 'Admin NovaPhone',
                'email' => 'admin@novaphone.vn',
                'phone' => '0900000001',
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
            ],
            'customer' => [
                'name' => 'Nguyễn Văn An',
                'email' => 'user@novaphone.vn',
                'phone' => '0900000002',
                'role' => 'user',
                'status' => 'active',
                'email_verified_at' => now(),
            ],
            'demo' => [
                'name' => 'Trần Minh Demo',
                'email' => 'demo@novaphone.vn',
                'phone' => '0900000999',
                'role' => 'user',
                'status' => 'active',
                'email_verified_at' => now(),
            ],
            'unverified' => [
                'name' => 'Tài khoản Chưa Xác Thực',
                'email' => 'verify@novaphone.vn',
                'phone' => '0900000998',
                'role' => 'user',
                'status' => 'active',
                'email_verified_at' => null,
            ],
            'blocked' => [
                'name' => 'Tài khoản Bị Khóa',
                'email' => 'blocked@novaphone.vn',
                'phone' => '0900000997',
                'role' => 'user',
                'status' => 'blocked',
                'email_verified_at' => now(),
            ],
            'social' => [
                'name' => 'Google Demo User',
                'email' => 'google-demo@novaphone.vn',
                'phone' => '0900000996',
                'role' => 'user',
                'status' => 'active',
                'provider' => 'google',
                'provider_id' => 'google-demo-001',
                'google_id' => 'google-demo-001',
                'email_verified_at' => now(),
            ],
        ];

        $users = [];
        foreach ($definitions as $key => $data) {
            $users[$key] = User::updateOrCreate(
                ['email' => $data['email']],
                array_merge($data, ['password' => Hash::make('password')]),
            );
        }

        return $users;
    }

    /** @return array<string, Brand> */
    private function seedBrands(): array
    {
        $definitions = [
            'apple' => ['name' => 'Apple', 'slug' => 'apple', 'logo' => 'images/brands/apple.svg'],
            'samsung' => ['name' => 'Samsung', 'slug' => 'samsung', 'logo' => 'images/brands/samsung.svg'],
            'xiaomi' => ['name' => 'Xiaomi', 'slug' => 'xiaomi', 'logo' => 'images/brands/xiaomi.svg'],
            'oppo' => ['name' => 'OPPO', 'slug' => 'oppo', 'logo' => 'images/brands/oppo.svg'],
            'realme' => ['name' => 'Realme', 'slug' => 'realme', 'logo' => 'images/brands/realme.svg'],
            'nokia' => ['name' => 'Nokia Legacy', 'slug' => 'nokia-legacy', 'logo' => 'images/brands/nokia.svg', 'is_active' => false],
        ];

        $brands = [];
        foreach ($definitions as $key => $data) {
            $brands[$key] = Brand::updateOrCreate(
                ['slug' => $data['slug']],
                array_merge(['description' => 'Thương hiệu demo của NovaPhone.', 'is_active' => true], $data),
            );
        }

        return $brands;
    }

    /** @return array<string, int> */
    private function seedCategories(): array
    {
        $rootId = $this->upsertCategory([
            'name' => 'Điện thoại thông minh',
            'slug' => 'dien-thoai-thong-minh-demo',
            'description' => 'Danh mục gốc cho dữ liệu demo.',
            'parent_id' => null,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $categories = ['root' => $rootId];
        foreach ([
            'flagship' => ['name' => 'Điện thoại cao cấp', 'slug' => 'dien-thoai-cao-cap-demo', 'sort_order' => 2],
            'mid' => ['name' => 'Điện thoại tầm trung', 'slug' => 'dien-thoai-tam-trung-demo', 'sort_order' => 3],
            'gaming' => ['name' => 'Điện thoại gaming', 'slug' => 'dien-thoai-gaming-demo', 'sort_order' => 4],
            'inactive' => ['name' => 'Sản phẩm ngừng kinh doanh', 'slug' => 'ngung-kinh-doanh-demo', 'sort_order' => 9, 'is_active' => false],
        ] as $key => $data) {
            $categories[$key] = $this->upsertCategory(array_merge([
                'description' => 'Danh mục demo NovaPhone.',
                'parent_id' => $rootId,
                'is_active' => true,
            ], $data));
        }

        return $categories;
    }

    private function upsertCategory(array $data): int
    {
        $now = now();
        $id = DB::table('categories')->where('slug', $data['slug'])->value('id');
        $payload = array_merge($data, ['updated_at' => $now]);

        if ($id) {
            DB::table('categories')->where('id', $id)->update($payload);
            return (int) $id;
        }

        return (int) DB::table('categories')->insertGetId(array_merge($payload, ['created_at' => $now]));
    }

    /** @return array<string, Product> */
    private function seedProducts(array $brands, array $categories): array
    {
        $definitions = [
            'ip17' => ['name' => 'iPhone 17 Pro Max Demo', 'slug' => 'iphone-17-pro-max-demo', 'brand' => 'apple', 'category' => 'flagship', 'price' => 39990000, 'sale_price' => null, 'sku' => 'DEMO-IP17PM', 'thumbnail' => 'images/products/iphone-17-pro-max.jpg', 'is_featured' => true, 'sold_count' => 35],
            'ip15' => ['name' => 'iPhone 15 Pro Max 256GB Demo', 'slug' => 'iphone-15-pro-max-256gb-demo', 'brand' => 'apple', 'category' => 'flagship', 'price' => 34990000, 'sale_price' => 28990000, 'sku' => 'DEMO-IP15PM', 'thumbnail' => 'images/products/iphone-15-pro-max-256gb.jpg', 'is_featured' => true, 'sold_count' => 238],
            's24' => ['name' => 'Samsung Galaxy S24 Ultra Demo', 'slug' => 'samsung-galaxy-s24-ultra-demo', 'brand' => 'samsung', 'category' => 'flagship', 'price' => 31990000, 'sale_price' => 25990000, 'sku' => 'DEMO-S24U', 'thumbnail' => 'images/products/samsung-galaxy-s24-ultra.jpg', 'is_featured' => true, 'sold_count' => 196],
            'x14' => ['name' => 'Xiaomi 14T Pro 512GB Demo', 'slug' => 'xiaomi-14t-pro-512gb-demo', 'brand' => 'xiaomi', 'category' => 'mid', 'price' => 18990000, 'sale_price' => 14990000, 'sku' => 'DEMO-X14TP', 'thumbnail' => 'images/products/xiaomi-14t-pro-512gb.webp', 'is_featured' => true, 'sold_count' => 215],
            'oppo' => ['name' => 'OPPO Find X7 Ultra Demo', 'slug' => 'oppo-find-x7-ultra-demo', 'brand' => 'oppo', 'category' => 'flagship', 'price' => 21990000, 'sale_price' => 16990000, 'sku' => 'DEMO-OPFX7', 'thumbnail' => 'images/products/oppo-find-x7-ultra.webp', 'is_featured' => false, 'sold_count' => 54],
            'realme' => ['name' => 'Realme GT 6 Demo', 'slug' => 'realme-gt-6-demo', 'brand' => 'realme', 'category' => 'gaming', 'price' => 14990000, 'sale_price' => 11990000, 'sku' => 'DEMO-RMGT6', 'thumbnail' => 'images/products/realme-gt-6-512gb.webp', 'is_featured' => true, 'sold_count' => 47],
            'oos' => ['name' => 'Xiaomi Redmi Note 13 Pro Hết hàng', 'slug' => 'xiaomi-redmi-note-13-pro-oos-demo', 'brand' => 'xiaomi', 'category' => 'mid', 'price' => 6990000, 'sale_price' => null, 'sku' => 'DEMO-RDN13-OOS', 'thumbnail' => 'images/products/xiaomi-redmi-note-13-pro.webp', 'is_featured' => false, 'sold_count' => 12],
            'hidden' => ['name' => 'Máy mẫu ngừng bán Demo', 'slug' => 'may-mau-ngung-ban-demo', 'brand' => 'nokia', 'category' => 'inactive', 'price' => 5990000, 'sale_price' => null, 'sku' => 'DEMO-HIDDEN', 'thumbnail' => 'images/products/iphone-15-128gb.webp', 'is_featured' => false, 'sold_count' => 0, 'is_active' => false],
        ];

        $products = [];
        foreach ($definitions as $key => $data) {
            $products[$key] = Product::updateOrCreate(
                ['sku' => $data['sku']],
                [
                    'name' => $data['name'],
                    'slug' => $data['slug'],
                    'description' => 'Sản phẩm demo chính hãng tại NovaPhone, dùng để kiểm thử đầy đủ luồng mua hàng.',
                    'content' => 'Sản phẩm demo có thông tin chi tiết, ảnh, biến thể và tồn kho.',
                    'category_id' => $categories[$data['category']],
                    'brand_id' => $brands[$data['brand']]->id,
                    'price' => $data['price'],
                    'sale_price' => $data['sale_price'],
                    'thumbnail' => $data['thumbnail'],
                    'is_active' => $data['is_active'] ?? true,
                    'is_featured' => $data['is_featured'],
                    'sold_count' => $data['sold_count'],
                    'view_count' => 250,
                ],
            );
        }

        return $products;
    }

    /** @return array<string, ProductVariant> */
    private function seedVariants(array $products): array
    {
        $definitions = [
            'ip17_256' => ['product' => 'ip17', 'name' => '256GB - Cosmic Orange', 'storage' => '256GB', 'color' => 'Cosmic Orange', 'color_code' => '#f97316', 'additional_price' => 0, 'sku' => 'DEMO-IP17PM-256', 'image' => 'images/products/iphone-17-pro-max.jpg', 'active' => true],
            'ip15_256' => ['product' => 'ip15', 'name' => '256GB - Natural Titanium', 'storage' => '256GB', 'color' => 'Natural Titanium', 'color_code' => '#a7a29a', 'additional_price' => 0, 'sku' => 'DEMO-IP15PM-256', 'image' => 'images/products/iphone-15-pro-max-256gb.jpg', 'active' => true],
            'ip15_512' => ['product' => 'ip15', 'name' => '512GB - Blue Titanium', 'storage' => '512GB', 'color' => 'Blue Titanium', 'color_code' => '#536dfe', 'additional_price' => 3000000, 'sku' => 'DEMO-IP15PM-512', 'image' => 'images/products/iphone-15-pro-max-256gb.jpg', 'active' => true],
            's24_256' => ['product' => 's24', 'name' => '256GB - Titanium Gray', 'storage' => '256GB', 'color' => 'Titanium Gray', 'color_code' => '#8f8f8f', 'additional_price' => 0, 'sku' => 'DEMO-S24U-256', 'image' => 'images/products/samsung-galaxy-s24-ultra.jpg', 'active' => true],
            's24_512' => ['product' => 's24', 'name' => '512GB - Titanium Black', 'storage' => '512GB', 'color' => 'Titanium Black', 'color_code' => '#1f2937', 'additional_price' => 2000000, 'sku' => 'DEMO-S24U-512', 'image' => 'images/products/samsung-galaxy-s24-ultra.jpg', 'active' => true],
            'x14_512' => ['product' => 'x14', 'name' => '512GB - Titan Blue', 'storage' => '512GB', 'color' => 'Titan Blue', 'color_code' => '#67788a', 'additional_price' => 0, 'sku' => 'DEMO-X14TP-512', 'image' => 'images/products/xiaomi-14t-pro-512gb.webp', 'active' => true],
            'oppo_256' => ['product' => 'oppo', 'name' => '256GB - Tailored Black', 'storage' => '256GB', 'color' => 'Tailored Black', 'color_code' => '#1d1d1f', 'additional_price' => 0, 'sku' => 'DEMO-OPFX7-256', 'image' => 'images/products/oppo-find-x7-ultra.webp', 'active' => true],
            'realme_256' => ['product' => 'realme', 'name' => '256GB - Fluid Silver', 'storage' => '256GB', 'color' => 'Fluid Silver', 'color_code' => '#c9c9c9', 'additional_price' => 0, 'sku' => 'DEMO-RMGT6-256', 'image' => 'images/products/realme-gt-6-512gb.webp', 'active' => true],
            'oos_256' => ['product' => 'oos', 'name' => '256GB - Black', 'storage' => '256GB', 'color' => 'Black', 'color_code' => '#111827', 'additional_price' => 0, 'sku' => 'DEMO-RDN13-256', 'image' => 'images/products/xiaomi-redmi-note-13-pro.webp', 'active' => true],
            'hidden_128' => ['product' => 'hidden', 'name' => '128GB - Gray', 'storage' => '128GB', 'color' => 'Gray', 'color_code' => '#6b7280', 'additional_price' => 0, 'sku' => 'DEMO-HIDDEN-128', 'image' => 'images/products/iphone-15-128gb.webp', 'active' => false],
        ];

        $variants = [];
        foreach ($definitions as $key => $data) {
            $variants[$key] = ProductVariant::updateOrCreate(
                ['sku' => $data['sku']],
                [
                    'product_id' => $products[$data['product']]->id,
                    'name' => $data['name'],
                    'storage' => $data['storage'],
                    'color' => $data['color'],
                    'color_code' => $data['color_code'],
                    'additional_price' => $data['additional_price'],
                    'image' => $data['image'],
                    'is_active' => $data['active'],
                ],
            );
        }

        return $variants;
    }

    private function seedInventories(array $products, array $variants): void
    {
        $quantities = [
            'ip17_256' => 24,
            'ip15_256' => 30,
            'ip15_512' => 20,
            's24_256' => 44,
            's24_512' => 0,
            'x14_512' => 72,
            'oppo_256' => 28,
            'realme_256' => 3,
            'oos_256' => 0,
            'hidden_128' => 10,
        ];

        foreach ($quantities as $key => $quantity) {
            $variant = $variants[$key];
            Inventory::updateOrCreate(
                ['product_id' => $variant->product_id, 'variant_id' => $variant->id],
                ['quantity' => $quantity, 'reserved_quantity' => 0, 'low_stock_threshold' => 5],
            );
        }

        // Có một dòng tồn kho product-level để test sản phẩm không có variant.
        Inventory::updateOrCreate(
            ['product_id' => $products['ip17']->id, 'variant_id' => null],
            ['quantity' => 0, 'reserved_quantity' => 0, 'low_stock_threshold' => 5],
        );
    }

    private function seedImages(array $products): void
    {
        $images = [
            'ip17' => ['images/products/iphone-17-pro-max.jpg'],
            'ip15' => ['images/products/iphone-15-pro-max-256gb.jpg', 'images/products/gallery/1784218583_6a5903d7a9a61.jpg'],
            's24' => ['images/products/samsung-galaxy-s24-ultra.jpg', 'images/products/gallery/1784258659_6a59a063ca542.webp'],
            'x14' => ['images/products/xiaomi-14t-pro-512gb.webp'],
            'oppo' => ['images/products/oppo-find-x7-ultra.webp'],
            'realme' => ['images/products/realme-gt-6-512gb.webp'],
            'oos' => ['images/products/xiaomi-redmi-note-13-pro.webp'],
            'hidden' => ['images/products/iphone-15-128gb.webp'],
        ];

        foreach ($images as $productKey => $paths) {
            foreach ($paths as $sortOrder => $path) {
                ProductImage::updateOrCreate(
                    ['product_id' => $products[$productKey]->id, 'sort_order' => $sortOrder],
                    ['image_url' => $path, 'is_primary' => $sortOrder === 0],
                );
            }
        }
    }

    private function seedPerformances(array $products): void
    {
        $specs = [
            'ip15' => ['chipset' => 'Apple A17 Pro', 'cpu_cores' => '6 nhân', 'gpu' => 'Apple GPU 6 nhân', 'antutu_score' => 1730000, 'geekbench_single' => 2950, 'geekbench_multi' => 7400, 'display_size_inch' => 6.7, 'display_type' => 'LTPO Super Retina XDR OLED', 'refresh_rate' => '120Hz ProMotion', 'main_camera_mp' => '48MP', 'ultra_wide_camera_mp' => '12MP', 'front_camera_mp' => '12MP', 'video_recording' => '4K@60fps, ProRes', 'battery_mah' => 4441, 'charging_speed_w' => 27, 'ram' => '8GB', 'os' => 'iOS 18', 'network_support' => '5G, Wi-Fi 6E, NFC'],
            's24' => ['chipset' => 'Snapdragon 8 Gen 3 for Galaxy', 'cpu_cores' => '8 nhân', 'gpu' => 'Adreno 750', 'antutu_score' => 1750000, 'geekbench_single' => 2200, 'geekbench_multi' => 7200, 'display_size_inch' => 6.8, 'display_type' => 'Dynamic AMOLED 2X', 'refresh_rate' => '120Hz', 'main_camera_mp' => '200MP', 'ultra_wide_camera_mp' => '50MP', 'front_camera_mp' => '12MP', 'video_recording' => '8K@30fps', 'battery_mah' => 5000, 'charging_speed_w' => 45, 'ram' => '12GB', 'os' => 'Android 14 / One UI 6.1', 'network_support' => '5G, Wi-Fi 7, NFC'],
            'x14' => ['chipset' => 'Dimensity 9300+', 'cpu_cores' => '8 nhân', 'gpu' => 'Immortalis-G720', 'antutu_score' => 1450000, 'geekbench_single' => 2250, 'geekbench_multi' => 7100, 'display_size_inch' => 6.67, 'display_type' => 'AMOLED', 'refresh_rate' => '144Hz', 'main_camera_mp' => '50MP', 'ultra_wide_camera_mp' => '50MP', 'front_camera_mp' => '32MP', 'video_recording' => '4K@60fps', 'battery_mah' => 5000, 'charging_speed_w' => 120, 'ram' => '12GB', 'os' => 'Android 14', 'network_support' => '5G, Wi-Fi 7, NFC'],
        ];

        foreach ($specs as $productKey => $data) {
            ProductPerformance::updateOrCreate(
                ['product_id' => $products[$productKey]->id],
                $data,
            );
        }
    }

    /** @return array<string, FlashSaleItem> */
    private function seedFlashSales(array $products): array
    {
        $now = now();
        $active = FlashSale::updateOrCreate(
            ['name' => 'Demo Flash Sale Cuối Tuần'],
            ['start_time' => $now->copy()->subDay(), 'end_time' => $now->copy()->addDays(7), 'is_active' => true],
        );
        FlashSale::updateOrCreate(
            ['name' => 'Demo Flash Sale Sắp Tới'],
            ['start_time' => $now->copy()->addDays(3), 'end_time' => $now->copy()->addDays(10), 'is_active' => true],
        );
        FlashSale::updateOrCreate(
            ['name' => 'Demo Flash Sale Đã Kết Thúc'],
            ['start_time' => $now->copy()->subDays(10), 'end_time' => $now->copy()->subDays(2), 'is_active' => true],
        );

        $items = [];
        foreach ([
            's24' => ['discount_percent' => 20, 'quantity' => 20, 'sold' => 3, 'max_per_user' => 1],
            'x14' => ['discount_percent' => 15, 'quantity' => 30, 'sold' => 5, 'max_per_user' => 2],
            'ip15' => ['discount_percent' => 10, 'quantity' => 50, 'sold' => 0, 'max_per_user' => 2],
        ] as $productKey => $data) {
            $items[$productKey] = FlashSaleItem::updateOrCreate(
                ['flash_sale_id' => $active->id, 'product_id' => $products[$productKey]->id],
                $data,
            );
        }

        return $items;
    }

    /** @return array<string, Coupon> */
    private function seedCoupons(array $users, array $products, array $categories): array
    {
        $now = now();
        $definitions = [
            'welcome' => ['code' => 'DEMO-WELCOME10', 'description' => 'Giảm 10% cho đơn demo.', 'type' => 'percent', 'value' => 10, 'max_discount' => 500000, 'min_order_amount' => 1000000, 'usage_limit' => 100, 'per_user_limit' => 1, 'starts_at' => $now->copy()->subDay(), 'expires_at' => $now->copy()->addDays(30), 'is_active' => true, 'is_apply_sale' => true, 'is_apply_flash_sale' => false, 'is_stackable' => false],
            'flash' => ['code' => 'DEMO-FLASH500K', 'description' => 'Giảm cố định cho sản phẩm Flash Sale.', 'type' => 'fixed', 'value' => 500000, 'max_discount' => null, 'min_order_amount' => 10000000, 'usage_limit' => 20, 'per_user_limit' => 1, 'starts_at' => $now->copy()->subDay(), 'expires_at' => $now->copy()->addDays(30), 'is_active' => true, 'is_apply_sale' => false, 'is_apply_flash_sale' => true, 'is_stackable' => true],
            'vip' => ['code' => 'DEMO-VIP15', 'description' => 'Coupon riêng cho khách hàng VIP.', 'type' => 'percent', 'value' => 15, 'max_discount' => 1000000, 'min_order_amount' => 5000000, 'usage_limit' => 10, 'per_user_limit' => 1, 'starts_at' => $now->copy()->subDay(), 'expires_at' => $now->copy()->addDays(30), 'is_active' => true, 'is_apply_sale' => true, 'is_apply_flash_sale' => false, 'is_stackable' => false],
            'expired' => ['code' => 'DEMO-EXPIRED10', 'description' => 'Coupon đã hết hạn để test validation.', 'type' => 'percent', 'value' => 10, 'max_discount' => 300000, 'min_order_amount' => 0, 'usage_limit' => 100, 'per_user_limit' => null, 'starts_at' => $now->copy()->subDays(10), 'expires_at' => $now->copy()->subDay(), 'is_active' => true, 'is_apply_sale' => true, 'is_apply_flash_sale' => false, 'is_stackable' => false],
            'soldout' => ['code' => 'DEMO-SOLDOUT50K', 'description' => 'Coupon hết lượt để test validation.', 'type' => 'fixed', 'value' => 50000, 'max_discount' => null, 'min_order_amount' => 0, 'usage_limit' => 10, 'per_user_limit' => null, 'starts_at' => $now->copy()->subDay(), 'expires_at' => $now->copy()->addDays(30), 'is_active' => true, 'is_apply_sale' => true, 'is_apply_flash_sale' => false, 'is_stackable' => false],
        ];

        $coupons = [];
        foreach ($definitions as $key => $data) {
            $usedCount = $key === 'soldout' ? 10 : ($key === 'vip' ? 1 : 0);
            $coupons[$key] = Coupon::updateOrCreate(
                ['code' => $data['code']],
                array_merge($data, ['used_count' => $usedCount, 'gift_product_id' => null]),
            );
        }

        $coupons['welcome']->categories()->syncWithoutDetaching([$categories['root']]);
        $coupons['flash']->products()->syncWithoutDetaching([$products['s24']->id, $products['x14']->id]);
        $coupons['vip']->eligibleUsers()->syncWithoutDetaching([$users['customer']->id]);
        $users['customer']->savedCoupons()->syncWithoutDetaching([$coupons['welcome']->id]);
        $users['demo']->savedCoupons()->syncWithoutDetaching([$coupons['flash']->id]);

        return $coupons;
    }

    private function seedContent(User $admin): void
    {
        $categories = [];
        foreach ([
            ['name' => 'Tin công nghệ Demo', 'slug' => 'tin-cong-nghe-demo'],
            ['name' => 'Tư vấn mua sắm Demo', 'slug' => 'tu-van-mua-sam-demo'],
            ['name' => 'Nội bộ Demo', 'slug' => 'noi-bo-demo', 'is_active' => false],
        ] as $data) {
            $categories[$data['slug']] = PostCategory::updateOrCreate(
                ['slug' => $data['slug']],
                ['name' => $data['name'], 'description' => 'Danh mục bài viết phục vụ demo.', 'is_active' => $data['is_active'] ?? true],
            );
        }

        Post::updateOrCreate(
            ['slug' => 'demo-5-tieu-chi-chon-smartphone'],
            ['title' => '5 tiêu chí chọn smartphone phù hợp', 'thumbnail' => 'images/posts/1784202577_6a58c5518e480.jpg', 'summary' => 'Bài viết published để kiểm thử trang tin tức.', 'content' => 'Nội dung bài viết demo về hiệu năng, camera, pin và dung lượng.', 'is_published' => true, 'published_at' => now()->subDay(), 'author_id' => $admin->id, 'post_category_id' => $categories['tu-van-mua-sam-demo']->id],
        );
        Post::updateOrCreate(
            ['slug' => 'demo-ban-nhap-flash-sale'],
            ['title' => 'Bản nháp Flash Sale tháng sau', 'thumbnail' => 'images/posts/1784202578_6a58c55220ec2.jpg', 'summary' => 'Bản nháp không hiển thị public.', 'content' => 'Nội dung bản nháp demo.', 'is_published' => false, 'published_at' => null, 'author_id' => $admin->id, 'post_category_id' => $categories['noi-bo-demo']->id],
        );

        foreach ([
            ['image' => 'images/banners/galaxy-s24-ultra.jpg', 'title' => 'Flash Sale S24 Ultra Demo', 'badge' => 'Giảm đến 20%', 'sort_order' => 1],
            ['image' => 'images/banners/oppo-find-x7-ultra.jpeg', 'title' => 'OPPO Find X7 Ultra Demo', 'badge' => 'Flagship chính hãng', 'sort_order' => 2],
        ] as $banner) {
            Banner::updateOrCreate(
                ['image' => $banner['image']],
                array_merge($banner, ['description' => 'Banner demo NovaPhone.', 'highlights' => ['Chính hãng', 'Bảo hành 12 tháng'], 'buy_url' => url('/products'), 'detail_url' => url('/products'), 'is_active' => true]),
            );
        }
    }

    private function seedAddresses(array $users): void
    {
        foreach ([
            ['user' => 'customer', 'full_name' => 'Nguyễn Văn An', 'phone' => '0900000002', 'address' => '12 Nguyễn Huệ', 'ward' => 'Bến Nghé', 'district' => 'Quận 1', 'province' => 'TP Hồ Chí Minh', 'is_default' => true],
            ['user' => 'customer', 'full_name' => 'Nguyễn Văn An', 'phone' => '0900000002', 'address' => '88 Trần Hưng Đạo', 'ward' => 'Cầu Ông Lãnh', 'district' => 'Quận 1', 'province' => 'TP Hồ Chí Minh', 'is_default' => false],
            ['user' => 'demo', 'full_name' => 'Trần Minh Demo', 'phone' => '0900000999', 'address' => '25 Lê Lợi', 'ward' => 'Hải Châu', 'district' => 'Hải Châu', 'province' => 'Đà Nẵng', 'is_default' => true],
        ] as $data) {
            Address::updateOrCreate(
                ['user_id' => $users[$data['user']]->id, 'phone' => $data['phone'], 'address' => $data['address']],
                ['full_name' => $data['full_name'], 'ward' => $data['ward'], 'district' => $data['district'], 'province' => $data['province'], 'is_default' => $data['is_default']],
            );
        }
    }

    private function seedWishlistAndCart(User $customer, array $products, array $variants): void
    {
        foreach ([$products['s24']->id, $products['ip15']->id] as $productId) {
            Wishlist::firstOrCreate(['user_id' => $customer->id, 'product_id' => $productId]);
        }

        $cart = Cart::firstOrCreate(['user_id' => $customer->id]);
        CartItem::updateOrCreate(
            ['cart_id' => $cart->id, 'product_id' => $products['ip15']->id, 'variant_id' => $variants['ip15_256']->id],
            ['quantity' => 1, 'price' => 28990000],
        );
        CartItem::updateOrCreate(
            ['cart_id' => $cart->id, 'product_id' => $products['x14']->id, 'variant_id' => $variants['x14_512']->id],
            ['quantity' => 1, 'price' => 14990000],
        );
    }

    /** @return array<string, Order> */
    private function seedOrders(array $users, array $products, array $variants, array $flashSaleItems, array $coupons): array
    {
        $now = now();
        $definitions = [
            'codPending' => ['code' => 'NVP-DEMO-COD01', 'user' => 'customer', 'status' => 'pending', 'method' => 'cod', 'payment' => 'pending', 'product' => 'ip15', 'variant' => 'ip15_256', 'price' => 28990000, 'subtotal' => 28990000, 'discount' => 0, 'total' => 28990000, 'created_at' => $now->copy()->subHours(2)],
            'vnpayPending' => ['code' => 'NVP-DEMO-VNP01', 'user' => 'customer', 'status' => 'pending', 'method' => 'vnpay', 'payment' => 'pending', 'product' => 's24', 'variant' => 's24_256', 'price' => 25592000, 'subtotal' => 25592000, 'discount' => 0, 'total' => 25592000, 'created_at' => $now->copy()->subMinutes(2), 'flash' => 's24'],
            'confirmed' => ['code' => 'NVP-DEMO-CONF1', 'user' => 'customer', 'status' => 'confirmed', 'method' => 'vnpay', 'payment' => 'paid', 'product' => 'x14', 'variant' => 'x14_512', 'price' => 16141500, 'subtotal' => 16141500, 'discount' => 500000, 'total' => 15641500, 'created_at' => $now->copy()->subDays(3), 'flash' => 'x14', 'coupon' => 'flash'],
            'processing' => ['code' => 'NVP-DEMO-PROC1', 'user' => 'demo', 'status' => 'processing', 'method' => 'cod', 'payment' => 'pending', 'product' => 'realme', 'variant' => 'realme_256', 'price' => 11990000, 'subtotal' => 11990000, 'discount' => 0, 'total' => 11990000, 'created_at' => $now->copy()->subDays(4)],
            'shipping' => ['code' => 'NVP-DEMO-SHIP1', 'user' => 'demo', 'status' => 'shipping', 'method' => 'vnpay', 'payment' => 'paid', 'product' => 'ip15', 'variant' => 'ip15_256', 'price' => 28990000, 'subtotal' => 28990000, 'discount' => 0, 'total' => 28990000, 'created_at' => $now->copy()->subDays(5)],
            'delivered' => ['code' => 'NVP-DEMO-DELI1', 'user' => 'customer', 'status' => 'delivered', 'method' => 'vnpay', 'payment' => 'paid', 'product' => 's24', 'variant' => 's24_256', 'price' => 25990000, 'subtotal' => 25990000, 'discount' => 1000000, 'total' => 24990000, 'created_at' => $now->copy()->subDays(6), 'coupon' => 'vip'],
            'received' => ['code' => 'NVP-DEMO-RECV1', 'user' => 'customer', 'status' => 'received', 'method' => 'vnpay', 'payment' => 'paid', 'product' => 'x14', 'variant' => 'x14_512', 'price' => 16141500, 'subtotal' => 16141500, 'discount' => 500000, 'total' => 15641500, 'created_at' => $now->copy()->subDays(8), 'flash' => 'x14', 'coupon' => 'welcome'],
            'cancelled' => ['code' => 'NVP-DEMO-CAN01', 'user' => 'demo', 'status' => 'cancelled', 'method' => 'vnpay', 'payment' => 'pending', 'product' => 's24', 'variant' => 's24_256', 'price' => 25592000, 'subtotal' => 25592000, 'discount' => 0, 'total' => 25592000, 'created_at' => $now->copy()->subDays(9), 'flash' => 's24'],
            'refunded' => ['code' => 'NVP-DEMO-REF01', 'user' => 'demo', 'status' => 'cancelled', 'method' => 'vnpay', 'payment' => 'refunded', 'product' => 'oppo', 'variant' => 'oppo_256', 'price' => 12000000, 'subtotal' => 12000000, 'discount' => 0, 'total' => 12000000, 'created_at' => $now->copy()->subDays(12)],
            'guest' => ['code' => 'NVP-DEMO-GUEST1', 'user' => null, 'email' => 'guest@novaphone.vn', 'status' => 'pending', 'method' => 'vnpay', 'payment' => 'pending', 'product' => 'x14', 'variant' => 'x14_512', 'price' => 16141500, 'subtotal' => 16141500, 'discount' => 0, 'total' => 16141500, 'created_at' => $now->copy()->subMinutes(3), 'flash' => 'x14'],
            'stale' => ['code' => 'NVP-DEMO-STALE1', 'user' => 'customer', 'status' => 'pending', 'method' => 'vnpay', 'payment' => 'pending', 'product' => 's24', 'variant' => 's24_256', 'price' => 25592000, 'subtotal' => 25592000, 'discount' => 0, 'total' => 25592000, 'created_at' => $now->copy()->subMinutes(15), 'flash' => 's24'],
        ];

        $orders = [];
        foreach ($definitions as $key => $data) {
            $user = $data['user'] ? $users[$data['user']] : null;
            $coupon = isset($data['coupon']) ? $coupons[$data['coupon']] : null;
            $orders[$key] = Order::updateOrCreate(
                ['order_code' => $data['code']],
                [
                    'user_id' => $user?->id,
                    'customer_email' => $user?->email ?? $data['email'],
                    'status' => $data['status'],
                    'payment_method' => $data['method'],
                    'payment_status' => $data['payment'],
                    'subtotal' => $data['subtotal'],
                    'discount_amount' => $data['discount'],
                    'shipping_fee' => 0,
                    'total_amount' => $data['total'],
                    'coupon_id' => $coupon?->id,
                    'coupon_code' => $coupon?->code,
                    'shipping_full_name' => $user?->name ?? 'Lê Khách Vãng Lai',
                    'shipping_phone' => $user?->phone ?? '0900000888',
                    'shipping_address' => '12 Nguyễn Huệ',
                    'shipping_ward' => 'Bến Nghé',
                    'shipping_district' => 'Quận 1',
                    'shipping_province' => 'TP Hồ Chí Minh',
                    'note' => 'Dữ liệu demo NovaPhone.',
                    'cancelled_reason' => in_array($data['status'], ['cancelled'], true) ? 'Đơn demo được tạo để kiểm thử trạng thái hủy.' : null,
                    'cancelled_by' => in_array($data['status'], ['cancelled'], true) ? $users['admin']->id : null,
                    'user_received_at' => $data['status'] === 'received' ? $now->copy()->subDays(7) : null,
                ],
            );
            $orders[$key]->created_at = $data['created_at'];
            $orders[$key]->updated_at = $data['created_at'];
            $orders[$key]->saveQuietly();

            $product = $products[$data['product']];
            $variant = $variants[$data['variant']];
            $flashSaleItem = isset($data['flash']) ? $flashSaleItems[$data['flash']] : null;
            OrderItem::updateOrCreate(
                ['order_id' => $orders[$key]->id, 'product_id' => $product->id, 'variant_id' => $variant->id],
                [
                    'flash_sale_item_id' => $flashSaleItem?->id,
                    'product_name' => $product->name,
                    'variant_name' => $variant->name,
                    'product_thumbnail' => $variant->image ?: $product->thumbnail,
                    'price' => $data['price'],
                    'quantity' => 1,
                    'subtotal' => $data['price'],
                ],
            );

            if ($coupon) {
                OrderCoupon::updateOrCreate(
                    ['order_id' => $orders[$key]->id, 'coupon_id' => $coupon->id],
                    ['coupon_code' => $coupon->code, 'discount_amount' => $data['discount']],
                );
            }

            if ($data['method'] === 'vnpay') {
                $transactionStatus = $data['payment'] === 'paid' ? 'success' : ($data['payment'] === 'refunded' ? 'refunded' : 'pending');
                PaymentTransaction::updateOrCreate(
                    ['order_id' => $orders[$key]->id, 'gateway' => 'vnpay', 'transaction_code' => 'DEMO-' . strtoupper($key)],
                    ['amount' => $data['total'], 'status' => $transactionStatus, 'response_code' => $transactionStatus === 'success' ? '00' : null, 'response_message' => $transactionStatus, 'payload' => ['vnp_TxnRef' => $data['code'], 'vnp_Amount' => (string) ($data['total'] * 100)], 'paid_at' => in_array($transactionStatus, ['success', 'refunded'], true) ? $data['created_at'] : null],
                );
            }

            $this->seedStatusHistories($orders[$key], $data['status'], $users);
        }

        return $orders;
    }

    private function seedStatusHistories(Order $order, string $finalStatus, array $users): void
    {
        $paths = [
            'pending' => ['pending'],
            'confirmed' => ['pending', 'confirmed'],
            'processing' => ['pending', 'confirmed', 'processing'],
            'shipping' => ['pending', 'confirmed', 'processing', 'shipping'],
            'delivered' => ['pending', 'confirmed', 'processing', 'shipping', 'delivered'],
            'received' => ['pending', 'confirmed', 'processing', 'shipping', 'delivered', 'received'],
            'cancelled' => ['pending', 'cancelled'],
        ];

        foreach ($paths[$finalStatus] ?? ['pending'] as $status) {
            $isSystemCancel = $status === 'cancelled' && str_contains($order->order_code, 'STALE');
            $creator = $status === 'received' ? $users['customer']->id : ($isSystemCancel ? null : $users['admin']->id);
            $history = OrderStatusHistory::updateOrCreate(
                ['order_id' => $order->id, 'status' => $status],
                ['note' => 'Timeline demo: ' . $status, 'created_by' => $creator],
            );

            if ($status === 'delivered') {
                $history->update(['delivery_proof_image' => 'deliveries/delivery_34_1784599320.webp']);
            }
        }
    }

    private function seedReviews(array $users, array $products, array $orders): void
    {
        Review::updateOrCreate(
            ['order_id' => $orders['delivered']->id, 'product_id' => $products['s24']->id],
            ['user_id' => $users['customer']->id, 'rating' => 5, 'comment' => 'Ảnh đúng sản phẩm, giao hàng nhanh, máy dùng rất tốt.', 'images' => ['storage/reviews/VmDXYEbun0to543fxfc7kdVl6Qo5Ad98oLg6SwAQ.png'], 'is_visible' => true],
        );
        Review::updateOrCreate(
            ['order_id' => $orders['received']->id, 'product_id' => $products['x14']->id],
            ['user_id' => $users['customer']->id, 'rating' => 4, 'comment' => 'Hiệu năng tốt trong tầm giá.', 'images' => null, 'is_visible' => true],
        );
        Review::updateOrCreate(
            ['order_id' => $orders['processing']->id, 'product_id' => $products['realme']->id],
            ['user_id' => $users['demo']->id, 'rating' => 3, 'comment' => 'Review chờ Admin duyệt để test màn hình quản trị.', 'images' => null, 'is_visible' => false],
        );
    }

    private function seedInventoryHistories(User $admin, array $products, array $variants): void
    {
        $rows = [
            ['product' => 's24', 'variant' => 's24_256', 'type' => 'import', 'quantity' => 10, 'note' => 'Nhập thêm lô hàng demo'],
            ['product' => 'x14', 'variant' => 'x14_512', 'type' => 'export', 'quantity' => 2, 'note' => 'Xuất hàng mẫu demo'],
            ['product' => 'realme', 'variant' => 'realme_256', 'type' => 'adjust', 'quantity' => -2, 'note' => 'Điều chỉnh kiểm kê demo'],
        ];

        foreach ($rows as $row) {
            InventoryHistory::updateOrCreate(
                ['product_id' => $products[$row['product']]->id, 'variant_id' => $variants[$row['variant']]->id, 'type' => $row['type'], 'note' => $row['note']],
                ['quantity' => $row['quantity'], 'user_id' => $admin->id],
            );
        }
    }

    private function seedSettings(): void
    {
        Setting::set('telegram_notify_enabled', '0');
        Setting::set('telegram_bot_token', 'demo-token-not-real');
        Setting::set('telegram_chat_id', '-1000000000000');
    }

    private function seedNotifications(User $customer, array $orders): void
    {
        DB::table('notifications')->updateOrInsert(
            ['id' => '11111111-1111-4111-8111-111111111111'],
            [
                'type' => 'App\\Notifications\\OrderCreatedNotification',
                'notifiable_type' => User::class,
                'notifiable_id' => $customer->id,
                'data' => json_encode(['order_id' => $orders['vnpayPending']->id, 'order_code' => $orders['vnpayPending']->order_code, 'total_amount' => (float) $orders['vnpayPending']->total_amount, 'message' => 'Đơn demo đang chờ thanh toán.'], JSON_UNESCAPED_UNICODE),
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );
        DB::table('notifications')->updateOrInsert(
            ['id' => '22222222-2222-4222-8222-222222222222'],
            [
                'type' => 'App\\Notifications\\OrderCancelledNotification',
                'notifiable_type' => User::class,
                'notifiable_id' => $customer->id,
                'data' => json_encode(['order_id' => $orders['stale']->id, 'order_code' => $orders['stale']->order_code, 'reason' => 'Đơn demo quá hạn thanh toán.', 'is_admin_cancel' => false, 'message' => 'Đơn demo đã bị hủy.'], JSON_UNESCAPED_UNICODE),
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );
    }
}
