<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    private const REVENUE_STATUSES = ['confirmed', 'processing', 'shipping', 'delivered'];

    public function index()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $revenueToday = (float) $this->revenueQuery()->whereDate('created_at', $today)->sum('total_amount');
        $revenueYesterday = (float) $this->revenueQuery()->whereDate('created_at', $yesterday)->sum('total_amount');

        $ordersTotal = Order::count();
        $ordersToday = Order::whereDate('created_at', $today)->count();
        $ordersYesterday = Order::whereDate('created_at', $yesterday)->count();

        $productsTotal = Product::count();
        $productsToday = Product::whereDate('created_at', $today)->count();
        $productsYesterday = Product::whereDate('created_at', $yesterday)->count();

        $customersTotal = User::where('role', '!=', 'admin')->count();
        $customersToday = User::where('role', '!=', 'admin')->whereDate('created_at', $today)->count();
        $customersYesterday = User::where('role', '!=', 'admin')->whereDate('created_at', $yesterday)->count();

        $days = collect(range(6, 0))->map(fn (int $daysAgo) => Carbon::today()->subDays($daysAgo));
        $revenueSeries = $days->map(fn (Carbon $day) => (float) $this->revenueQuery()->whereDate('created_at', $day)->sum('total_amount'));
        $ordersSeries = $days->map(fn (Carbon $day) => Order::whereDate('created_at', $day)->count());
        $productsSeries = $days->map(fn (Carbon $day) => Product::whereDate('created_at', $day)->count());
        $customersSeries = $days->map(fn (Carbon $day) => User::where('role', '!=', 'admin')->whereDate('created_at', $day)->count());

        $revenue7d = $revenueSeries->sum();
        $revenuePrev7d = (float) $this->revenueQuery()
            ->whereBetween('created_at', [Carbon::today()->subDays(13)->startOfDay(), Carbon::today()->subDays(7)->endOfDay()])
            ->sum('total_amount');

        $recentOrders = Order::with(['user', 'items.product'])->latest()->take(5)->get();
        $bestSellers = Product::orderByDesc('sold_count')->take(5)->get();
        $lowStock = Inventory::with('product')
            ->whereColumn('quantity', '<=', 'low_stock_threshold')
            ->whereHas('product')
            ->orderBy('quantity')
            ->take(5)
            ->get();
        $categoryStats = $this->categoryStats();

        return view('admin.dashboard', [
            'cards' => [
                'revenue' => [
                    'value' => $revenueToday,
                    'delta' => $this->growth($revenueToday, $revenueYesterday),
                    'series' => $revenueSeries->all(),
                ],
                'orders' => [
                    'value' => $ordersTotal,
                    'delta' => $this->growth($ordersToday, $ordersYesterday),
                    'series' => $ordersSeries->all(),
                ],
                'products' => [
                    'value' => $productsTotal,
                    'delta' => $this->growth($productsToday, $productsYesterday),
                    'series' => $productsSeries->all(),
                ],
                'customers' => [
                    'value' => $customersTotal,
                    'delta' => $this->growth($customersToday, $customersYesterday),
                    'series' => $customersSeries->all(),
                ],
            ],
            'revenueSeries' => $revenueSeries->all(),
            'revenueDays' => $days->map(fn (Carbon $day) => $day->format('d/m'))->all(),
            'revenue7d' => $revenue7d,
            'revenue7dDelta' => $this->growth($revenue7d, $revenuePrev7d),
            'recentOrders' => $recentOrders,
            'bestSellers' => $bestSellers,
            'maxSold' => max((int) $bestSellers->max('sold_count'), 1),
            'lowStock' => $lowStock,
            'categoryStats' => $categoryStats,
            'categoryTotal' => $categoryStats->sum('value'),
            'ordersTotal' => $ordersTotal,
        ]);
    }

    private function revenueQuery()
    {
        return Order::whereIn('status', self::REVENUE_STATUSES);
    }

    private function growth(float $current, float $previous): float
    {
        if ($previous <= 0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    private function categoryStats()
    {
        $rows = Category::withSum('products as sold', 'sold_count')
            ->having('sold', '>', 0)
            ->orderByDesc('sold')
            ->get()
            ->map(fn (Category $category) => (object) [
                'name' => $category->name,
                'value' => (int) $category->sold,
            ]);

        if ($rows->count() <= 5) {
            return $rows->values();
        }

        return $rows->take(4)
            ->push((object) ['name' => 'Khác', 'value' => $rows->slice(4)->sum('value')])
            ->values();
    }
}
