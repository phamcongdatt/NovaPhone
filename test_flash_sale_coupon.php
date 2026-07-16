<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$product = App\Models\Product::whereHas('activeFlashSaleItem')->first();
if(!$product) { echo 'No active flash sale products'; exit; }

echo "Product ID: " . $product->id . "\n";
echo "Effective Price: " . $product->effective_price . "\n";
echo "Sale Price: " . $product->sale_price . "\n";
echo "Price: " . $product->price . "\n";

$activeSale = $product->activeFlashSaleItem;
echo "Flash Sale Discount: " . $activeSale->discount_percent . "%\n";

$flashSalePrice = (float) ($product->price * (1 - $activeSale->discount_percent / 100));
echo "Calculated Flash Sale Price: " . $flashSalePrice . "\n";

$cartItem = new App\Models\CartItem(['product_id' => $product->id, 'variant_id' => null, 'quantity' => 1, 'price' => $product->effective_price]);
$cartItem->setRelation('product', $product);

$coupon = App\Models\Coupon::firstOrCreate(
    ['code' => 'TEST_FLASH'],
    ['type' => 'percent', 'value' => 10, 'is_active' => true, 'is_apply_flash_sale' => true, 'is_apply_sale' => false]
);
$coupon->update(['is_apply_flash_sale' => true, 'is_apply_sale' => false, 'is_active' => true]);

$service = new App\Services\CouponService();
$result = $service->apply('TEST_FLASH', null, collect([$cartItem]), $product->effective_price);

print_r($result);
