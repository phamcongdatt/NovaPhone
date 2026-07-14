<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 1. Get a product
$product = App\Models\Product::first();
if (!$product) { echo "No products\n"; exit; }

// 2. Create an active flash sale
$flashSale = App\Models\FlashSale::create([
    'name' => 'Test Flash Sale',
    'start_time' => now()->subHour(),
    'end_time' => now()->addHour(),
    'is_active' => true,
]);

// 3. Add item to flash sale
$flashSaleItem = App\Models\FlashSaleItem::create([
    'flash_sale_id' => $flashSale->id,
    'product_id' => $product->id,
    'discount_percent' => 50,
    'quantity' => 100,
    'sold' => 0,
    'max_per_user' => 10,
]);

// Reload product
$product = App\Models\Product::find($product->id);
echo "Effective Price: " . $product->effective_price . "\n";
echo "Active Flash Sale Item: " . ($product->activeFlashSaleItem ? "YES" : "NO") . "\n";

// 4. Create coupon
$coupon = App\Models\Coupon::create([
    'code' => 'TEST_FLASH_2',
    'type' => 'percent',
    'value' => 20,
    'is_active' => true,
    'is_apply_flash_sale' => true,
    'is_apply_sale' => false,
    'is_stackable' => true
]);

// 5. Create CartItem
$cartItem = new App\Models\CartItem([
    'product_id' => $product->id,
    'variant_id' => null,
    'quantity' => 1,
    'price' => $product->effective_price
]);
$cartItem->setRelation('product', $product);

// 6. Test CouponService
$service = new App\Services\CouponService();
$result = $service->applyMultiple(['TEST_FLASH_2'], null, collect([$cartItem]), $product->effective_price);

print_r($result);

// Cleanup
$flashSaleItem->delete();
$flashSale->delete();
$coupon->delete();

