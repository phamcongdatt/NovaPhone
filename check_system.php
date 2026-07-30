<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n===========================================\n";
echo "   KIỂM TRA HỆ THỐNG NOVAPHONE\n";
echo "===========================================\n\n";

// 1. Check .env
echo "1️⃣  KIỂM TRA .ENV CONFIGURATION\n";
$sessionDriver = config('session.driver');
$cacheStore = config('cache.default');
$databaseConnection = config('database.default');
$databaseName = config("database.connections.{$databaseConnection}.database");
echo "   SESSION_DRIVER: " . $sessionDriver . " " . ($sessionDriver === 'database' ? '✅' : '❌') . "\n";
echo "   CACHE_STORE: " . $cacheStore . " " . ($cacheStore === 'database' ? '✅' : '❌') . "\n";
echo "   DATABASE: " . $databaseName . " ✅\n\n";

// 2. Check Database Connection
echo "2️⃣  KIỂM TRA KỾT NỐI DATABASE\n";
try {
    $app->make('db')->connection()->getPdo();
    echo "   Database: ✅ Kết nối thành công\n";
} catch (\Exception $e) {
    echo "   Database: ❌ Lỗi - " . $e->getMessage() . "\n";
}
echo "\n";

// 3. Check Sessions Table
echo "3️⃣  KIỂM TRA SESSIONS TABLE\n";
try {
    $count = \Illuminate\Support\Facades\DB::table('sessions')->count();
    echo "   Sessions có: $count bản ghi ✅\n";
} catch (\Exception $e) {
    echo "   Sessions: ❌ Lỗi - " . $e->getMessage() . "\n";
}
echo "\n";

// 4. Check Users
echo "4️⃣  KIỂM TRA USERS\n";
try {
    $count = \App\Models\User::count();
    echo "   Tổng users: $count ✅\n";
    $demo = \App\Models\User::where('email', 'demo@novaphone.vn')->first();
    if ($demo) {
        echo "   Demo user: ✅ Tồn tại (email: demo@novaphone.vn)\n";
    } else {
        echo "   Demo user: ⚠️  Không tồn tại\n";
    }
} catch (\Exception $e) {
    echo "   Users: ❌ Lỗi\n";
}
echo "\n";

// 5. Check Routes
echo "5️⃣  KIỂM TRA ROUTES\n";
try {
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    $routeNames = array_map(fn($r) => $r->getName(), $routes->getRoutes());
    
    $required = ['login', 'register', 'cart.index', 'coupons.index', 'coupons.apply', 'checkout', 'orders.index'];
    foreach ($required as $name) {
        $exists = in_array($name, $routeNames);
        echo "   Route '{$name}': " . ($exists ? '✅' : '❌') . "\n";
    }
} catch (\Exception $e) {
    echo "   Routes: ❌ Lỗi\n";
}
echo "\n";

echo "===========================================\n";
echo "   ✅ KIỂM TRA HOÀN THÀNH\n";
echo "===========================================\n\n";
