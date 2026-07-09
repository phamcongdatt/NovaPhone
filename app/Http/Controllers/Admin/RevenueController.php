<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RevenueController extends Controller
{
    private const REVENUE_STATUSES = ['confirmed', 'processing', 'shipping', 'delivered'];

    /**
     * Trang Dashboard thống kê doanh thu.
     */
    public function index(Request $request)
    {
        $period = $request->input('period', '30days');
        
        // Validation thời gian tùy chọn nếu lọc custom
        if ($period === 'custom') {
            $request->validate([
                'start_date' => ['required', 'date'],
                'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            ], [
                'start_date.required' => 'Vui lòng chọn ngày bắt đầu.',
                'start_date.date' => 'Ngày bắt đầu không đúng định dạng.',
                'end_date.required' => 'Vui lòng chọn ngày kết thúc.',
                'end_date.date' => 'Ngày kết thúc không đúng định dạng.',
                'end_date.after_or_equal' => 'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu.',
            ]);
        }

        $dateRange = $this->resolveDateRange($period, $request);
        $start = $dateRange['start'];
        $end = $dateRange['end'];

        // ─── 1. Các thẻ thống kê (Dashboard Cards) ────────────────
        $totalRevenue = (float) Order::whereIn('status', self::REVENUE_STATUSES)
            ->whereBetween('created_at', [$start, $end])
            ->sum('total_amount');

        $totalOrders = Order::whereBetween('created_at', [$start, $end])->count();

        // Tổng số khách hàng đã đăng ký trong kỳ
        $totalCustomers = User::where('role', '!=', 'admin')
            ->whereBetween('created_at', [$start, $end])
            ->count();
        
        // Tổng khách hàng đăng ký toàn hệ thống
        $totalCustomersSystem = User::where('role', '!=', 'admin')->count();

        // Tổng sản phẩm bán được
        $totalProductsSold = (int) OrderItem::whereHas('order', function ($q) use ($start, $end) {
                $q->whereIn('status', self::REVENUE_STATUSES)
                  ->whereBetween('created_at', [$start, $end]);
            })
            ->sum('quantity');

        // Doanh thu & Đơn hàng hôm nay
        $today = Carbon::today();
        $revenueToday = (float) Order::whereIn('status', self::REVENUE_STATUSES)
            ->whereDate('created_at', $today)
            ->sum('total_amount');
        $ordersToday = Order::whereDate('created_at', $today)->count();


        // ─── 2. Biểu đồ doanh thu & đơn hàng theo ngày ───────────
        $dailyRevenueAndOrders = $this->getDailyRevenueAndOrders($start, $end);


        // ─── 3. Biểu đồ doanh thu theo tháng (12 tháng của năm nay) 
        $monthlyRevenue = $this->getMonthlyRevenue();


        // ─── 4. Top 10 sản phẩm bán chạy ──────────────────────────
        $topProducts = OrderItem::select(
                'product_id',
                'product_name',
                'product_thumbnail',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(subtotal) as total_revenue')
            )
            ->whereHas('order', function ($q) use ($start, $end) {
                $q->whereIn('status', self::REVENUE_STATUSES)
                  ->whereBetween('created_at', [$start, $end]);
            })
            ->groupBy('product_id', 'product_name', 'product_thumbnail')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();


        // ─── 5. Thống kê danh mục sản phẩm ───────────────────────
        $categoryStats = OrderItem::select(
                'categories.name as category_name',
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereHas('order', function ($q) use ($start, $end) {
                $q->whereIn('status', self::REVENUE_STATUSES)
                  ->whereBetween('created_at', [$start, $end]);
            })
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_revenue')
            ->get();


        // ─── 6. Thống kê khách hàng (Top mua nhiều nhất) ─────────
        $topCustomers = Order::select(
                'orders.user_id',
                'users.name as customer_name',
                'users.email as customer_email',
                'orders.shipping_full_name',
                'orders.shipping_phone',
                DB::raw('COUNT(orders.id) as total_orders'),
                DB::raw('SUM(orders.total_amount) as total_spent')
            )
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->whereIn('orders.status', self::REVENUE_STATUSES)
            ->whereBetween('orders.created_at', [$start, $end])
            ->groupBy('orders.user_id', 'users.name', 'users.email', 'orders.shipping_full_name', 'orders.shipping_phone')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();


        // ─── 7. Thống kê đơn hàng theo trạng thái ─────────────────
        $ordersCountByStatus = Order::select('status', DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('status')
            ->get()
            ->keyBy('status')
            ->map(fn($item) => $item->count);

        $orderStatusStats = [
            'pending' => $ordersCountByStatus->get('pending', 0),
            'confirmed' => $ordersCountByStatus->get('confirmed', 0),
            'processing' => $ordersCountByStatus->get('processing', 0),
            'shipping' => $ordersCountByStatus->get('shipping', 0),
            'delivered' => $ordersCountByStatus->get('delivered', 0),
            'cancelled' => $ordersCountByStatus->get('cancelled', 0),
        ];

        return view('admin.revenue.index', [
            'period' => $period,
            'dateRange' => [
                'start' => $start->format('Y-m-d'),
                'end' => $end->format('Y-m-d'),
            ],
            'stats' => [
                'total_revenue' => $totalRevenue,
                'total_orders' => $totalOrders,
                'total_customers' => $totalCustomers,
                'total_customers_system' => $totalCustomersSystem,
                'total_products_sold' => $totalProductsSold,
                'revenue_today' => $revenueToday,
                'orders_today' => $ordersToday,
            ],
            'dailyData' => $dailyRevenueAndOrders,
            'monthlyData' => $monthlyRevenue,
            'topProducts' => $topProducts,
            'categoryStats' => $categoryStats,
            'topCustomers' => $topCustomers,
            'orderStatusStats' => $orderStatusStats,
        ]);
    }

    /**
     * Giải quyết khoảng thời gian lọc.
     */
    private function resolveDateRange(string $period, Request $request): array
    {
        $now = Carbon::now();
        $end = $now->endOfDay();

        return match ($period) {
            'today' => [
                'start' => Carbon::today(),
                'end' => $end
            ],
            'yesterday' => [
                'start' => Carbon::yesterday()->startOfDay(),
                'end' => Carbon::yesterday()->endOfDay()
            ],
            '7days' => [
                'start' => Carbon::now()->subDays(6)->startOfDay(),
                'end' => $end
            ],
            '30days' => [
                'start' => Carbon::now()->subDays(29)->startOfDay(),
                'end' => $end
            ],
            'thisMonth' => [
                'start' => Carbon::now()->firstOfMonth()->startOfDay(),
                'end' => $end
            ],
            'thisYear' => [
                'start' => Carbon::now()->firstOfYear()->startOfDay(),
                'end' => $end
            ],
            'custom' => [
                'start' => Carbon::parse($request->input('start_date'))->startOfDay(),
                'end' => Carbon::parse($request->input('end_date'))->endOfDay(),
            ],
            default => [
                'start' => Carbon::now()->subDays(29)->startOfDay(),
                'end' => $end
            ],
        };
    }

    /**
     * Lấy doanh thu và đơn hàng theo từng ngày.
     */
    private function getDailyRevenueAndOrders(Carbon $start, Carbon $end): array
    {
        $dbData = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as revenue'),
                DB::raw('COUNT(*) as order_count')
            )
            ->whereIn('status', self::REVENUE_STATUSES)
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $result = [];
        $current = $start->copy();

        while ($current->lte($end)) {
            $dateStr = $current->format('Y-m-d');
            $dateFormatted = $current->format('d/m');
            
            $dayData = $dbData->get($dateStr);
            
            $result[] = [
                'date' => $dateFormatted,
                'revenue' => $dayData ? (float) $dayData->revenue : 0.0,
                'orders' => $dayData ? (int) $dayData->order_count : 0,
            ];

            $current->addDay();
        }

        return $result;
    }

    /**
     * Lấy doanh thu theo tháng (12 tháng của năm nay).
     */
    private function getMonthlyRevenue(): array
    {
        $currentYear = Carbon::now()->year;
        
        $dbData = Order::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->whereIn('status', self::REVENUE_STATUSES)
            ->whereYear('created_at', $currentYear)
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        $result = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthData = $dbData->get($m);
            $result[] = [
                'month' => 'T' . $m,
                'revenue' => $monthData ? (float) $monthData->revenue : 0.0,
            ];
        }

        return $result;
    }
}
