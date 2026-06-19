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
    /** Trạng thái đơn được tính là có doanh thu (không gồm đơn hủy). */
    private const REVENUE_STATUSES = ['confirmed', 'processing', 'shipping', 'delivered'];

    public function index()
    {
        $today     = Carbon::today();
        $yesterday = Carbon::yesterday();

        // ─── 4 thẻ thống kê tổng quan ───────────────────────────────
        $revenueToday     = (float) $this->revenueQuery()->whereDate('created_at', $today)->sum('total_amount');
        $revenueYesterday = (float) $this->revenueQuery()->whereDate('created_at', $yesterday)->sum('total_amount');

        $ordersTotal     = Order::count();
        $ordersToday     = Order::whereDate('created_at', $today)->count();
        $ordersYesterday = Order::whereDate('created_at', $yesterday)->count();

        $productsTotal     = Product::count();
        $productsToday     = Product::whereDate('created_at', $today)->count();
        $productsYesterday = Product::whereDate('created_at', $yesterday)->count();

        $customersTotal     = User::where('role', '!=', 'admin')->count();
        $customersToday     = User::where('role', '!=', 'admin')->whereDate('created_at', $today)->count();
        $customersYesterday = User::where('role', '!=', 'admin')->whereDate('created_at', $yesterday)->count();

        // ─── Chuỗi 7 ngày gần nhất (cho biểu đồ + sparkline) ─────────
        $days = collect(range(6, 0))->map(fn ($i) => Carbon::today()->subDays($i));

        $revenueSeries   = $days->map(fn ($d) => (float) $this->revenueQuery()->whereDate('created_at', $d)->sum('total_amount'));
        $ordersSeries    = $days->map(fn ($d) => Order::whereDate('created_at', $d)->count());
        $productsSeries  = $days->map(fn ($d) => Product::whereDate('created_at', $d)->count());
        $customersSeries = $days->map(fn ($d) => User::where('role', '!=', 'admin')->whereDate('created_at', $d)->count());

        $revenue7d     = $revenueSeries->sum();
        $revenuePrev7d = (float) $this->revenueQuery()
            ->whereBetween('created_at', [Carbon::today()->subDays(13)->startOfDay(), Carbon::today()->subDays(7)->endOfDay()])
            ->sum('total_amount');

        // ─── Đơn hàng gần đây ───────────────────────────────────────
        $recentOrders = Order::with(['user', 'items.product'])->latest()->take(5)->get();

        // ─── Sản phẩm bán chạy ──────────────────────────────────────
        $bestSellers = Product::orderByDesc('sold_count')->take(5)->get();

        // ─── Tồn kho thấp ───────────────────────────────────────────
        $lowStock = Inventory::with('product')
            ->whereColumn('quantity', '<=', 'low_stock_threshold')
            ->whereHas('product')
            ->orderBy('quantity')
            ->take(5)
            ->get();

        // ─── Thống kê theo danh mục (theo lượt bán) ─────────────────
        $categoryStats = $this->categoryStats();

        return view('admin.dashboard', [
            'cards' => [
                'revenue' => [
                    'value'  => $revenueToday,
                    'delta'  => $this->growth($revenueToday, $revenueYesterday),
                    'series' => $revenueSeries->all(),
                ],
                'orders' => [
                    'value'  => $ordersTotal,
                    'delta'  => $this->growth($ordersToday, $ordersYesterday),
                    'series' => $ordersSeries->all(),
                ],
                'products' => [
                    'value'  => $productsTotal,
                    'delta'  => $this->growth($productsToday, $productsYesterday),
                    'series' => $productsSeries->all(),
                ],
                'customers' => [
                    'value'  => $customersTotal,
                    'delta'  => $this->growth($customersToday, $customersYesterday),
                    'series' => $customersSeries->all(),
                ],
            ],
            'revenueSeries' => $revenueSeries->all(),
            'revenueDays'   => $days->map(fn ($d) => $d->format('d/m'))->all(),
            'revenue7d'     => $revenue7d,
            'revenue7dDelta' => $this->growth($revenue7d, $revenuePrev7d),
            'recentOrders'  => $recentOrders,
            'bestSellers'   => $bestSellers,
            'maxSold'       => max((int) $bestSellers->max('sold_count'), 1),
            'lowStock'      => $lowStock,
            'categoryStats' => $categoryStats,
            'categoryTotal' => $categoryStats->sum('value'),
            'ordersTotal'   => $ordersTotal,
        ]);
    }

    private function revenueQuery()
    {
        return Order::whereIn('status', self::REVENUE_STATUSES);
    }

    /** Tỉ lệ tăng trưởng (%) so với kỳ trước. */
    private function growth(float $current, float $previous): float
    {
        if ($previous <= 0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    /** Phân bổ lượt bán theo danh mục: top 4 + gộp phần còn lại thành "Khác". */
    private function categoryStats()
    {
        $rows = Category::withSum('products as sold', 'sold_count')
            ->having('sold', '>', 0)
            ->orderByDesc('sold')
            ->get()
            ->map(fn ($c) => (object) ['name' => $c->name, 'value' => (int) $c->sold]);

        if ($rows->count() <= 5) {
            return $rows->values();
        }

        $top   = $rows->take(4);
        $other = $rows->slice(4)->sum('value');

        return $top->push((object) ['name' => 'Khác', 'value' => $other])->values();
    }
}
