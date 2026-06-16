<?php
$r = app(App\Services\ProductSearchService::class)->search('iphone', 3);
echo 'Search "iphone" -> '.count($r).' ket qua'.PHP_EOL;
foreach ($r as $p) { echo '   - '.$p->name.PHP_EOL; }

$svc = app(App\Services\CartService::class);
$p = App\Models\Product::find(4);
$v = $p->variants->first();
echo 'getAvailableStock(product 4 / variant '.$v->id.') = '.$svc->getAvailableStock($p, $v).PHP_EOL;