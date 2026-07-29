<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\FlashSale;
use App\Models\FlashSaleItem;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class RecoveryContentSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::where('email', 'admin@novaphone.vn')->first() ?? User::first();

        if (! $author) {
            return;
        }

        $postCategories = [];
        foreach ([
            ['name' => 'Tin công nghệ', 'slug' => 'tin-cong-nghe', 'description' => 'Tin tức và xu hướng công nghệ mới nhất.'],
            ['name' => 'Tư vấn mua sắm', 'slug' => 'tu-van-mua-sam', 'description' => 'Gợi ý chọn thiết bị phù hợp với nhu cầu.'],
            ['name' => 'Mẹo sử dụng', 'slug' => 'meo-su-dung', 'description' => 'Mẹo sử dụng và bảo quản thiết bị hiệu quả.'],
        ] as $category) {
            $postCategories[$category['slug']] = PostCategory::updateOrCreate(
                ['slug' => $category['slug']],
                $category,
            );
        }

        $posts = [
            [
                'slug' => '5-tieu-chi-chon-smartphone-phu-hop',
                'title' => '5 tiêu chí chọn smartphone phù hợp nhu cầu',
                'thumbnail' => 'images/posts/1784202577_6a58c5518e480.jpg',
                'summary' => 'Từ hiệu năng, camera đến thời lượng pin, đây là những yếu tố nên cân nhắc trước khi mua điện thoại mới.',
                'content' => 'Một chiếc smartphone phù hợp cần đáp ứng đúng nhu cầu sử dụng hằng ngày. Hãy bắt đầu từ ngân sách, sau đó cân nhắc hiệu năng, màn hình, camera, pin và chính sách bảo hành.\n\nĐừng chỉ chọn theo thông số cao nhất; trải nghiệm thực tế và dịch vụ hậu mãi cũng quan trọng không kém.',
                'category' => 'tu-van-mua-sam',
            ],
            [
                'slug' => 'iphone-17-pro-max-co-gi-moi',
                'title' => 'iPhone 17 Pro Max có gì mới đáng chú ý?',
                'thumbnail' => 'images/posts/1784202578_6a58c55220ec2.jpg',
                'summary' => 'Những điểm nổi bật trên mẫu iPhone Pro Max mới dành cho người dùng yêu thích hiệu năng và camera.',
                'content' => 'iPhone 17 Pro Max hướng đến nhóm người dùng cần hiệu năng mạnh, màn hình cao cấp và hệ thống camera linh hoạt. Thiết kế mới cùng thời lượng pin được tối ưu giúp máy phù hợp cả công việc lẫn giải trí.\n\nBạn có thể xem thông tin sản phẩm và chương trình ưu đãi đang áp dụng tại NovaPhone.',
                'category' => 'tin-cong-nghe',
            ],
            [
                'slug' => 'so-sanh-camera-galaxy-s24-ultra',
                'title' => 'So sánh camera Galaxy S24 Ultra cho nhu cầu chụp ảnh',
                'thumbnail' => 'images/posts/1784204573_6a58cd1d826fc.jpg',
                'summary' => 'Khả năng chụp ảnh đa tiêu cự và quay video giúp Galaxy S24 Ultra nổi bật trong phân khúc flagship.',
                'content' => 'Galaxy S24 Ultra có hệ thống camera đa dụng, phù hợp chụp ảnh đời thường, chân dung và phong cảnh. Khi chụp thiếu sáng, hãy giữ máy ổn định và ưu tiên tốc độ màn trập phù hợp để có ảnh rõ nét hơn.\n\nNgoài phần cứng, các chế độ xử lý ảnh tích hợp cũng giúp người dùng có thể chia sẻ ảnh ngay sau khi chụp.',
                'category' => 'tin-cong-nghe',
            ],
            [
                'slug' => 'cach-bao-ve-pin-smartphone-ben-lau',
                'title' => 'Cách bảo vệ pin smartphone bền lâu hơn',
                'thumbnail' => 'images/posts/1784997688_6a64e738c16ad.jpg',
                'summary' => 'Một vài thói quen đơn giản giúp hạn chế chai pin và duy trì hiệu suất thiết bị.',
                'content' => 'Hạn chế để pin cạn thường xuyên, tránh nhiệt độ quá cao và sử dụng bộ sạc đạt chuẩn. Khi không cần thiết, bạn có thể tắt các tính năng chạy nền để giảm mức tiêu thụ năng lượng.\n\nNếu thiết bị nóng bất thường hoặc pin tụt nhanh, hãy kiểm tra tại trung tâm bảo hành thay vì tiếp tục sử dụng phụ kiện không rõ nguồn gốc.',
                'category' => 'meo-su-dung',
            ],
            [
                'slug' => 'chon-dung-luong-luu-tru-dien-thoai',
                'title' => 'Nên chọn dung lượng lưu trữ điện thoại bao nhiêu?',
                'thumbnail' => 'images/posts/1784997772_6a64e78cd41f3.jpg',
                'summary' => 'Gợi ý chọn bộ nhớ phù hợp với thói quen chụp ảnh, quay video và chơi game.',
                'content' => 'Người dùng chủ yếu gọi điện, nhắn tin và dùng mạng xã hội có thể bắt đầu từ 128GB. Nếu thường xuyên quay video, cài nhiều game hoặc muốn sử dụng máy lâu dài, 256GB sẽ thoải mái hơn.\n\nHãy tính cả dung lượng hệ điều hành và các bản sao lưu trước khi quyết định.',
                'category' => 'tu-van-mua-sam',
            ],
            [
                'slug' => 'meo-kiem-tra-dien-thoai-chinh-hang',
                'title' => 'Mẹo kiểm tra điện thoại chính hãng trước khi nhận máy',
                'thumbnail' => 'images/posts/1784997773_6a64e78d60953.jpg',
                'summary' => 'Các bước kiểm tra ngoại hình, số serial và phụ kiện để yên tâm khi nhận sản phẩm.',
                'content' => 'Hãy kiểm tra tình trạng seal, ngoại hình, số serial trên hộp và trong phần cài đặt của máy. Đối chiếu phụ kiện, chính sách bảo hành và hóa đơn trước khi rời cửa hàng.\n\nNovaPhone khuyến khích khách hàng giữ lại đầy đủ giấy tờ mua hàng để được hỗ trợ nhanh chóng khi cần.',
                'category' => 'meo-su-dung',
            ],
        ];

        foreach ($posts as $index => $postData) {
            $categorySlug = $postData['category'];
            unset($postData['category']);

            Post::updateOrCreate(
                ['slug' => $postData['slug']],
                array_merge($postData, [
                    'post_category_id' => $postCategories[$categorySlug]->id,
                    'author_id' => $author->id,
                    'is_published' => true,
                    'published_at' => now()->subDays($index + 1),
                ]),
            );
        }

        $banners = [
            ['image' => 'images/banners/1784202361_6a58c479ef04c.jpg', 'title' => 'iPhone 17 Pro Max', 'badge' => 'Flagship mới', 'description' => 'Trải nghiệm hiệu năng mạnh mẽ và camera chuyên nghiệp.', 'highlights' => ['Hiệu năng cao', 'Camera Pro'], 'buy_url' => '/products?search=iPhone', 'detail_url' => '/products?search=iPhone'],
            ['image' => 'images/banners/1784204436_6a58cc942f8a7.jpg', 'title' => 'Galaxy S24 Ultra', 'badge' => 'Samsung', 'description' => 'Sẵn sàng chinh phục mọi khoảnh khắc với Galaxy S24 Ultra.', 'highlights' => ['S Pen', 'Camera 200MP'], 'buy_url' => '/products?search=Samsung', 'detail_url' => '/products?search=Samsung'],
            ['image' => 'images/banners/1784204500_6a58ccd4bdef4.jpg', 'title' => 'Xiaomi 14T Pro', 'badge' => 'Xiaomi', 'description' => 'Flagship hiệu năng cao với mức giá dễ tiếp cận.', 'highlights' => ['Màn hình 144Hz', 'Sạc nhanh'], 'buy_url' => '/products?search=Xiaomi', 'detail_url' => '/products?search=Xiaomi'],
            ['image' => 'images/banners/1784949451_6a642acb7ef08.jpg', 'title' => 'Ưu đãi công nghệ mỗi ngày', 'badge' => 'NovaPhone Deals', 'description' => 'Khám phá các sản phẩm chính hãng với ưu đãi hấp dẫn.', 'highlights' => ['Chính hãng', 'Bảo hành đầy đủ'], 'buy_url' => '/products', 'detail_url' => '/coupons'],
            ['image' => 'images/banners/1784968064_6a647380afdad.jpg', 'title' => 'Săn deal cuối tuần', 'badge' => 'Ưu đãi giới hạn', 'description' => 'Số lượng có hạn, nhanh tay chọn sản phẩm yêu thích.', 'highlights' => ['Giá tốt', 'Giao hàng nhanh'], 'buy_url' => '/products', 'detail_url' => '/coupons'],
            ['image' => 'images/banners/1784968145_6a6473d1e350e.jpg', 'title' => 'Đổi máy mới, nhận ưu đãi', 'badge' => 'NovaPhone', 'description' => 'Mua sắm đơn giản hơn cùng nhiều chương trình hỗ trợ khách hàng.', 'highlights' => ['Thanh toán linh hoạt', 'Hỗ trợ tận tâm'], 'buy_url' => '/products', 'detail_url' => '/coupons'],
        ];

        foreach ($banners as $sortOrder => $bannerData) {
            Banner::updateOrCreate(
                ['image' => $bannerData['image']],
                array_merge($bannerData, [
                    'is_active' => true,
                    'sort_order' => $sortOrder,
                ]),
            );
        }

        $flashSale = FlashSale::updateOrCreate(
            ['name' => 'Flash Sale NovaPhone'],
            [
                'start_time' => now()->subHour(),
                'end_time' => now()->addDays(7),
                'is_active' => true,
            ],
        );

        $flashSaleProducts = [
            ['sku' => 'IP15PM256', 'discount_percent' => 15, 'quantity' => 20, 'max_per_user' => 1],
            ['sku' => 'SGS24U256', 'discount_percent' => 12, 'quantity' => 20, 'max_per_user' => 1],
            ['sku' => 'XM14TP512', 'discount_percent' => 10, 'quantity' => 25, 'max_per_user' => 2],
            ['sku' => 'OPFX7U256', 'discount_percent' => 10, 'quantity' => 15, 'max_per_user' => 1],
            ['sku' => 'RMGT6512', 'discount_percent' => 8, 'quantity' => 20, 'max_per_user' => 2],
        ];

        foreach ($flashSaleProducts as $item) {
            $product = Product::where('sku', $item['sku'])->first();
            if (! $product) {
                continue;
            }

            FlashSaleItem::updateOrCreate(
                ['flash_sale_id' => $flashSale->id, 'product_id' => $product->id],
                [
                    'discount_percent' => $item['discount_percent'],
                    'quantity' => $item['quantity'],
                    'sold' => 0,
                    'max_per_user' => $item['max_per_user'],
                ],
            );
        }

        $coupons = [
            ['code' => 'NOVA10', 'description' => 'Giảm 10% cho đơn hàng từ 500.000đ', 'type' => 'percent', 'value' => 10, 'max_discount' => 500000, 'min_order_amount' => 500000, 'is_apply_sale' => true, 'is_apply_flash_sale' => false],
            ['code' => 'WELCOME50K', 'description' => 'Giảm 50.000đ cho khách hàng mới', 'type' => 'fixed', 'value' => 50000, 'max_discount' => null, 'min_order_amount' => 500000, 'is_apply_sale' => true, 'is_apply_flash_sale' => false],
            ['code' => 'FLASH5', 'description' => 'Giảm thêm 5% cho sản phẩm Flash Sale', 'type' => 'percent', 'value' => 5, 'max_discount' => 300000, 'min_order_amount' => 1000000, 'is_apply_sale' => true, 'is_apply_flash_sale' => true],
        ];

        foreach ($coupons as $couponData) {
            Coupon::updateOrCreate(
                ['code' => $couponData['code']],
                array_merge($couponData, [
                    'used_count' => 0,
                    'usage_limit' => null,
                    'per_user_limit' => 1,
                    'starts_at' => now()->subDay(),
                    'expires_at' => now()->addDays(30),
                    'is_active' => true,
                    'is_stackable' => false,
                    'gift_product_id' => null,
                ]),
            );
        }

        $this->command?->info('Đã khôi phục banner, tin tức, flash sale và mã khuyến mãi.');
    }
}
