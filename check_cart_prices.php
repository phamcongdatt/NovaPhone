<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$items = App\Models\CartItem::with('product.flashSaleItems.flashSale')->get();

foreach ($items as $item) {
    $product = $item->product;
    $variant = $item->variant;
    $activeSale = $product->activeFlashSaleItem;
    
    if ($activeSale) {
        $flashSalePrice = (float) ($product->price * (1 - $activeSale->discount_percent / 100));
        $basePrice = $flashSalePrice + ($variant ? (float) $variant->additional_price : 0);
        
        $itemPrice = (float) $item->price;
        
        echo "Item ID: {$item->id}, Product ID: {$product->id}\n";
        echo "Item Price: {$itemPrice}\n";
        echo "Base Price: {$basePrice}\n";
        echo "Diff: " . abs($itemPrice - $basePrice) . "\n";
        
        if (abs($itemPrice - $basePrice) >= 0.01) {
            echo "FAILED CHECK!\n";
        } else {
            echo "PASSED CHECK!\n";
        }
        echo "-----------------------\n";
    }
}
echo "Done checking all flash sale items in all carts.\n";
