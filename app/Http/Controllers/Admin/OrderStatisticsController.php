<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class OrderStatisticsController extends Controller
{
    /**
     * Trang thống kê đơn hàng.
     */
    public function index(Request $request)
    {
        // Lấy khoảng thời gian từ query params (mặc định: 30 ngày qua)
        $period = $request->input('period', '30days');
        $dateRange = $this->resolveDateRange($period, $request);

        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];

        // ─── Tổng quan ───────────────────────────────────────
        $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])->count();
        $totalRevenue = (float) Order::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['confirmed', 'processing', 'shipping', 'delivered'])
            ->sum('total_amount');
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        $completedOrders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'delivered')
            ->count();
        $completionRate = $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 1) : 0;

        // ─── Doanh thu theo ngày ─────────────────────────────
        $revenueDaily = $this->getDailyRevenue($startDate, $endDate);

        // ─── Đơn hàng theo trạng thái ───────────────────────
        $ordersByStatus = $this->getOrdersByStatus($startDate, $endDate);

        // ─── Đơn hàng theo phương thức thanh toán ────────────
        $ordersByPayment = $this->getOrdersByPaymentMethod($startDate, $endDate);

        // ─── Top sản phẩm bán chạy (trong khoảng thời gian) ──
        $topProducts = $this->getTopProducts($startDate, $endDate);

        // ─── Đơn hàng theo tháng (12 tháng gần nhất) ─────────
        $monthlyTrend = $this->getMonthlyTrend();

        // ─── Thống kê theo tỉnh/thành ───────────────────────
        $ordersByProvince = $this->getOrdersByProvince($startDate, $endDate);

        // ─── Đơn hàng trung bình theo giờ trong ngày ─────────
        $hourlyDistribution = $this->getHourlyDistribution($startDate, $endDate);

        // ─── Tỷ lệ hủy đơn ──────────────────────────────────
        $cancelledOrders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'cancelled')
            ->count();
        $cancelRate = $totalOrders > 0 ? round(($cancelledOrders / $totalOrders) * 100, 1) : 0;

        // ─── Doanh thu theo danh mục ─────────────────────────
        $revenueByCategory = $this->getRevenueByCategory($startDate, $endDate);

        return view('admin.orders.statistics', [
            'period' => $period,
            'dateRange' => $dateRange,
            'totalOrders' => $totalOrders,
            'totalRevenue' => $totalRevenue,
            'avgOrderValue' => $avgOrderValue,
            'completionRate' => $completionRate,
            'cancelRate' => $cancelRate,
            'revenueDaily' => $revenueDaily,
            'ordersByStatus' => $ordersByStatus,
            'ordersByPayment' => $ordersByPayment,
            'topProducts' => $topProducts,
            'monthlyTrend' => $monthlyTrend,
            'ordersByProvince' => $ordersByProvince,
            'hourlyDistribution' => $hourlyDistribution,
            'revenueByCategory' => $revenueByCategory,
        ]);
    }

    /**
     * Xác định khoảng thời gian từ query params.
     */
    private function resolveDateRange(string $period, Request $request): array
    {
        $end = Carbon::now()->endOfDay();

        return match ($period) {
            'today'     => ['start' => Carbon::today(), 'end' => $end],
            '7days'     => ['start' => Carbon::now()->subDays(6)->startOfDay(), 'end' => $end],
            '30days'    => ['start' => Carbon::now()->subDays(29)->startOfDay(), 'end' => $end],
            '90days'    => ['start' => Carbon::now()->subDays(89)->startOfDay(), 'end' => $end],
            'thisMonth' => ['start' => Carbon::now()->firstOfMonth(), 'end' => $end],
            'lastMonth' => ['start' => Carbon::now()->subMonth()->firstOfMonth(), 'end' => Carbon::now()->subMonth()->endOfMonth()],
            'custom'    => [
                'start' => $request->filled('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : Carbon::now()->subDays(29),
                'end'   => $request->filled('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : $end,
            ],
            default     => ['start' => Carbon::now()->subDays(29)->startOfDay(), 'end' => $end],
        };
    }

    /**
     * Doanh thu theo từng ngày.
     */
    private function getDailyRevenue(Carbon $start, Carbon $end): array
    {
        $days = [];
        $current = $start->copy();

        while ($current->lte($end)) {
            $dateStr = $current->format('d/m');
            $revenue = (float) Order::whereDate('created_at', $current)
                ->whereIn('status', ['confirmed', 'processing', 'shipping', 'delivered'])
                ->sum('total_amount');
            $orderCount = Order::whereDate('created_at', $current)->count();

            $days[] = [
                'date' => $dateStr,
                'revenue' => $revenue,
                'orders' => $orderCount,
            ];

            $current->addDay();
        }

        return $days;
    }

    /**
     * Đơn hàng theo trạng thái.
     */
    private function getOrdersByStatus(Carbon $start, Carbon $end): array
    {
        $statuses = ['pending', 'confirmed', 'processing', 'shipping', 'delivered', 'cancelled'];
        $labels = [
            'pending'    => 'Chờ xác nhận',
            'confirmed'  => 'Đã xác nhận',
            'processing' => 'Đang xử lý',
            'shipping'   => 'Đang giao',
            'delivered'  => 'Đã giao',
            'cancelled'  => 'Đã hủy',
        ];
        $colors = [
            'pending'    => '#f59e0b',
            'confirmed'  => '#3b82f6',
            'processing' => '#8b5cf6',
            'shipping'   => '#06b6d4',
            'delivered'  => '#22c55e',
            'cancelled'  => '#ef4444',
        ];

        $result = [];
        foreach ($statuses as $status) {
            $count = Order::whereBetween('created_at', [$start, $end])
                ->where('status', $status)
                ->count();
            $result[] = [
                'status' => $status,
                'label'  => $labels[$status],
                'count'  => $count,
                'color'  => $colors[$status],
            ];
        }

        return $result;
    }

    /**
     * Đơn hàng theo phương thức thanh toán.
     */
    private function getOrdersByPaymentMethod(Carbon $start, Carbon $end): array
    {
        $methods = [
            'cod'   => ['label' => 'COD', 'color' => '#22c55e'],
            'momo'  => ['label' => 'Momo', 'color' => '#a855f7'],
            'vnpay' => ['label' => 'VNPay', 'color' => '#3b82f6'],
        ];

        $result = [];
        foreach ($methods as $key => $info) {
            $count = Order::whereBetween('created_at', [$start, $end])
                ->where('payment_method', $key)
                ->count();
            $revenue = (float) Order::whereBetween('created_at', [$start, $end])
                ->where('payment_method', $key)
                ->whereIn('status', ['confirmed', 'processing', 'shipping', 'delivered'])
                ->sum('total_amount');

            $result[] = [
                'method'  => $key,
                'label'   => $info['label'],
                'count'   => $count,
                'revenue' => $revenue,
                'color'   => $info['color'],
            ];
        }

        return $result;
    }

    /**
     * Top sản phẩm bán chạy trong khoảng thời gian.
     */
    private function getTopProducts(Carbon $start, Carbon $end, int $limit = 10): array
    {
        return OrderItem::select('product_id', 'product_name', 'product_thumbnail', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
            ->whereHas('order', function ($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end])
                  ->whereIn('status', ['confirmed', 'processing', 'shipping', 'delivered']);
            })
            ->groupBy('product_id', 'product_name', 'product_thumbnail')
            ->orderByDesc('total_qty')
            ->limit($limit)
            ->get()
            ->map(fn ($item) => [
                'product_id'    => $item->product_id,
                'product_name'  => $item->product_name,
                'thumbnail'     => $item->product_thumbnail,
                'total_qty'     => (int) $item->total_qty,
                'total_revenue' => (float) $item->total_revenue,
            ])
            ->toArray();
    }

    /**
     * Xu hướng đơn hàng theo tháng (12 tháng gần nhất).
     */
    private function getMonthlyTrend(): array
    {
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $startOfMonth = $date->copy()->firstOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            $orderCount = Order::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
            $revenue = (float) Order::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->whereIn('status', ['confirmed', 'processing', 'shipping', 'delivered'])
                ->sum('total_amount');

            $months[] = [
                'month'   => $date->format('m/Y'),
                'orders'  => $orderCount,
                'revenue' => $revenue,
            ];
        }

        return $months;
    }

    /**
     * Đơn hàng theo tỉnh/thành phố.
     */
    private function getOrdersByProvince(Carbon $start, Carbon $end): array
    {
        return Order::select('shipping_province', DB::raw('COUNT(*) as order_count'), DB::raw('SUM(total_amount) as total_revenue'))
            ->whereBetween('created_at', [$start, $end])
            ->whereIn('status', ['confirmed', 'processing', 'shipping', 'delivered'])
            ->groupBy('shipping_province')
            ->orderByDesc('order_count')
            ->limit(10)
            ->get()
            ->map(fn ($item) => [
                'province'     => $item->shipping_province,
                'order_count'  => (int) $item->order_count,
                'total_revenue' => (float) $item->total_revenue,
            ])
            ->toArray();
    }

    /**
     * Phân bố đơn hàng theo giờ trong ngày.
     */
    private function getHourlyDistribution(Carbon $start, Carbon $end): array
    {
        $hours = array_fill(0, 24, 0);

        $data = Order::select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('hour')
            ->get();

        foreach ($data as $row) {
            $hours[(int) $row->hour] = (int) $row->count;
        }

        return $hours;
    }

    /**
     * Doanh thu theo danh mục sản phẩm.
     */
    private function getRevenueByCategory(Carbon $start, Carbon $end): array
    {
        return OrderItem::select('product_id', DB::raw('SUM(subtotal) as total_revenue'))
            ->whereHas('order', function ($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end])
                  ->whereIn('status', ['confirmed', 'processing', 'shipping', 'delivered']);
            })
            ->groupBy('product_id')
            ->orderByDesc('total_revenue')
            ->limit(8)
            ->get()
            ->map(function ($item) {
                $product = Product::find($item->product_id);
                return [
                    'category_name' => $product?->category?->name ?? 'Không xác định',
                    'total_revenue' => (float) $item->total_revenue,
                ];
            })
            ->toArray();
    }
}
