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
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class MassProductSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('vi_VN');

        // Danh sách thương hiệu
        $brandsData = ['Apple', 'Samsung', 'Xiaomi', 'OPPO', 'Vivo', 'Realme', 'Nokia', 'Asus', 'Sony'];
        $brands = [];
        foreach ($brandsData as $b) {
            $brands[] = Brand::firstOrCreate(
                ['slug' => Str::slug($b)],
                ['name' => $b, 'is_active' => true]
            );
        }

        // Danh sách danh mục
        $categoriesData = ['Điện thoại cao cấp', 'Điện thoại tầm trung', 'Điện thoại giá rẻ', 'Điện thoại chơi game', 'Điện thoại chụp ảnh'];
        $categories = [];
        foreach ($categoriesData as $c) {
            $categories[] = Category::firstOrCreate(
                ['slug' => Str::slug($c)],
                ['name' => $c, 'is_active' => true]
            );
        }

        // Danh sách users để đánh giá
        $users = [];
        for ($i = 0; $i < 5; $i++) {
            $users[] = User::firstOrCreate(
                ['email' => "demo{$i}@novaphone.vn"],
                [
                    'name' => $faker->name,
                    'phone' => '0900000' . rand(100, 999),
                    'role' => 'user',
                    'status' => 'active',
                    'password' => bcrypt('password'),
                ]
            );
        }

        $colors = [
            ['name' => 'Đen', 'code' => '#000000'],
            ['name' => 'Trắng', 'code' => '#ffffff'],
            ['name' => 'Xanh dương', 'code' => '#1d4ed8'],
            ['name' => 'Đỏ', 'code' => '#dc2626'],
            ['name' => 'Xám', 'code' => '#6b7280'],
            ['name' => 'Vàng', 'code' => '#eab308']
        ];
        
        $storages = ['64GB', '128GB', '256GB', '512GB', '1TB'];

        // Tạo 100 sản phẩm
        for ($i = 1; $i <= 100; $i++) {
            $brand = $faker->randomElement($brands);
            $category = $faker->randomElement($categories);
            
            // Generate name: Brand + Model + Random suffix
            $name = $brand->name . ' ' . $faker->word() . ' ' . $faker->randomElement(['Pro', 'Max', 'Ultra', 'Plus', 'Lite', '5G', '']);
            $name = trim(preg_replace('/\s+/', ' ', $name));
            $name = ucwords($name);
            $slug = Str::slug($name) . '-' . Str::random(5);

            // Mức giá rải đều từ 2tr đến 40tr
            $price = $faker->numberBetween(20, 400) * 100000; 
            
            // Có 30% cơ hội được giảm giá
            $sale_price = null;
            if ($faker->boolean(30)) {
                $sale_price = $price - ($faker->numberBetween(1, 5) * 100000);
            }

            $product = Product::create([
                'name' => $name,
                'slug' => $slug,
                'description' => 'Sản phẩm ' . $name . ' chính hãng, thiết kế đẹp mắt và hiệu năng mạnh mẽ.',
                'content' => $faker->paragraph(6),
                'category_id' => $category->id,
                'brand_id' => $brand->id,
                'price' => $price,
                'sale_price' => $sale_price,
                // Using placeholder images for demo
                'thumbnail' => 'https://placehold.co/600x600/12151d/93c5fd?text=' . urlencode($brand->name),
                'sku' => strtoupper(Str::random(8)),
                'is_active' => true,
                'is_featured' => $faker->boolean(20), // 20% là sản phẩm nổi bật
                'sold_count' => $faker->numberBetween(0, 500),
                'view_count' => $faker->numberBetween(100, 10000),
            ]);

            // Sinh biến thể (1-3 biến thể cho mỗi sản phẩm)
            $variantCount = $faker->numberBetween(1, 3);
            for ($v = 0; $v < $variantCount; $v++) {
                $color = $faker->randomElement($colors);
                $storage = $faker->randomElement($storages);
                
                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'name' => $storage . ' - ' . $color['name'],
                    'storage' => $storage,
                    'color' => $color['name'],
                    'color_code' => $color['code'],
                    'additional_price' => $faker->numberBetween(0, 3) * 500000,
                    'sku' => $product->sku . '-' . $v,
                    'is_active' => true,
                ]);

                // Tồn kho
                Inventory::create([
                    'product_id' => $product->id,
                    'variant_id' => $variant->id,
                    'quantity' => $faker->numberBetween(0, 100),
                    'low_stock_threshold' => 5,
                ]);
            }

            // Đánh giá ngẫu nhiên (chọn ngẫu nhiên từ danh sách user để tránh trùng lặp)
            $randomUsers = $faker->randomElements($users, $faker->numberBetween(0, count($users)));
            foreach ($randomUsers as $u) {
                Review::create([
                    'user_id' => $u->id,
                    'product_id' => $product->id,
                    'rating' => $faker->numberBetween(3, 5),
                    'comment' => $faker->sentence(),
                    'is_visible' => true,
                ]);
            }
        }
    }
}
