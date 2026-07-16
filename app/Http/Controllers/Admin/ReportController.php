<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    private const REVENUE_STATUSES = ['confirmed', 'processing', 'shipping', 'delivered'];

    /**
     * Xuất báo cáo doanh thu Excel (định dạng CSV UTF-8 BOM).
     */
    public function revenueExcel(Request $request)
    {
        $period = $request->input('period', '30days');
        $dateRange = $this->resolveDateRange($period, $request);
        $start = $dateRange['start'];
        $end = $dateRange['end'];

        // Thu thập dữ liệu
        $totalRevenue = (float) Order::whereIn('status', self::REVENUE_STATUSES)
            ->whereBetween('created_at', [$start, $end])
            ->sum('total_amount');
        $totalOrders = Order::whereBetween('created_at', [$start, $end])->count();
        $totalCustomers = User::where('role', '!=', 'admin')->whereBetween('created_at', [$start, $end])->count();
        $totalProductsSold = (int) OrderItem::whereHas('order', function ($q) use ($start, $end) {
                $q->whereIn('status', self::REVENUE_STATUSES)->whereBetween('created_at', [$start, $end]);
            })->sum('quantity');

        // Doanh thu theo ngày
        $dailyData = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as revenue'),
                DB::raw('COUNT(*) as order_count')
            )
            ->whereIn('status', self::REVENUE_STATUSES)
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top sản phẩm bán chạy
        $topProducts = OrderItem::select(
                'product_name',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(subtotal) as total_revenue')
            )
            ->whereHas('order', function ($q) use ($start, $end) {
                $q->whereIn('status', self::REVENUE_STATUSES)->whereBetween('created_at', [$start, $end]);
            })
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('total_qty')
            ->limit(15)
            ->get();

        // Doanh thu theo danh mục
        $categoryStats = OrderItem::select(
                'categories.name as category_name',
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereHas('order', function ($q) use ($start, $end) {
                $q->whereIn('status', self::REVENUE_STATUSES)->whereBetween('created_at', [$start, $end]);
            })
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_revenue')
            ->get();

        // Top khách hàng mua nhiều nhất
        $topCustomers = Order::select(
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
            ->limit(15)
            ->get();

        // Chuẩn bị file tải về
        $fileName = 'bao-cao-doanh-thu-' . $start->format('Ymd') . '-den-' . $end->format('Ymd') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($start, $end, $totalRevenue, $totalOrders, $totalProductsSold, $totalCustomers, $dailyData, $topProducts, $topCustomers, $categoryStats) {
            $file = fopen('php://output', 'w');
            
            // Viết UTF-8 BOM để Excel hiển thị tiếng Việt chính xác
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Tiêu đề báo cáo
            fputcsv($file, ['BÁO CÁO THỐNG KÊ DOANH THU - NOVAPHONE']);
            fputcsv($file, ['Khoảng thời gian:', $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y')]);
            fputcsv($file, ['Ngày xuất báo cáo:', now()->format('H:i d/m/Y')]);
            fputcsv($file, []);

            // Phần 1: Tóm tắt tổng quan
            fputcsv($file, ['TỔNG QUAN CHỈ SỐ']);
            fputcsv($file, ['Chỉ số', 'Giá trị']);
            fputcsv($file, ['Tổng doanh thu', number_format($totalRevenue, 0, ',', '.') . ' ₫']);
            fputcsv($file, ['Tổng số đơn hàng', $totalOrders]);
            fputcsv($file, ['Tổng sản phẩm bán ra', $totalProductsSold]);
            fputcsv($file, ['Khách hàng đăng ký mới', $totalCustomers]);
            fputcsv($file, []);

            // Phần 2: Doanh thu theo ngày
            fputcsv($file, ['CHI TIẾT DOANH THU THEO NGÀY']);
            fputcsv($file, ['Ngày', 'Số lượng đơn hàng', 'Doanh thu (₫)']);
            foreach ($dailyData as $row) {
                fputcsv($file, [
                    Carbon::parse($row->date)->format('d/m/Y'),
                    $row->order_count,
                    (float) $row->revenue
                ]);
            }
            fputcsv($file, []);

            // Phần 3: Top sản phẩm bán chạy
            fputcsv($file, ['TOP SẢN PHẨM BÁN CHẠY']);
            fputcsv($file, ['STT', 'Tên sản phẩm', 'Số lượng bán', 'Doanh thu (₫)']);
            foreach ($topProducts as $index => $row) {
                fputcsv($file, [
                    $index + 1,
                    $row->product_name,
                    $row->total_qty,
                    (float) $row->total_revenue
                ]);
            }
            fputcsv($file, []);

            // Phần 4: Doanh thu theo danh mục
            fputcsv($file, ['DOANH THU THEO DANH MỤC']);
            fputcsv($file, ['STT', 'Tên danh mục', 'Số sản phẩm bán ra', 'Doanh thu (₫)']);
            foreach ($categoryStats as $index => $row) {
                fputcsv($file, [
                    $index + 1,
                    $row->category_name,
                    $row->total_qty,
                    (float) $row->total_revenue
                ]);
            }
            fputcsv($file, []);

            // Phần 5: Khách hàng tiêu biểu
            fputcsv($file, ['TOP KHÁCH HÀNG TIÊU BIỂU']);
            fputcsv($file, ['STT', 'Họ tên', 'Email', 'Số điện thoại', 'Tổng số đơn hàng', 'Tổng tiền mua sắm (₫)']);
            foreach ($topCustomers as $index => $row) {
                fputcsv($file, [
                    $index + 1,
                    $row->customer_name ?? $row->shipping_full_name,
                    $row->customer_email ?? '—',
                    $row->shipping_phone ?? '—',
                    $row->total_orders,
                    (float) $row->total_spent
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Xuất báo cáo PDF / Bản in báo cáo doanh thu.
     */
    public function revenuePdf(Request $request)
    {
        $period = $request->input('period', '30days');
        $dateRange = $this->resolveDateRange($period, $request);
        $start = $dateRange['start'];
        $end = $dateRange['end'];

        // Thu thập dữ liệu
        $totalRevenue = (float) Order::whereIn('status', self::REVENUE_STATUSES)
            ->whereBetween('created_at', [$start, $end])
            ->sum('total_amount');
        $totalOrders = Order::whereBetween('created_at', [$start, $end])->count();
        $totalCustomers = User::where('role', '!=', 'admin')->whereBetween('created_at', [$start, $end])->count();
        $totalProductsSold = (int) OrderItem::whereHas('order', function ($q) use ($start, $end) {
                $q->whereIn('status', self::REVENUE_STATUSES)->whereBetween('created_at', [$start, $end]);
            })->sum('quantity');

        // Doanh thu theo ngày (Cho biểu đồ)
        $dailyRevenueAndOrders = $this->getDailyRevenueAndOrders($start, $end);

        // Top 10 sản phẩm
        $topProducts = OrderItem::select(
                'product_id',
                'product_name',
                'product_thumbnail',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(subtotal) as total_revenue')
            )
            ->whereHas('order', function ($q) use ($start, $end) {
                $q->whereIn('status', self::REVENUE_STATUSES)->whereBetween('created_at', [$start, $end]);
            })
            ->groupBy('product_id', 'product_name', 'product_thumbnail')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        // Danh mục
        $categoryStats = OrderItem::select(
                'categories.name as category_name',
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereHas('order', function ($q) use ($start, $end) {
                $q->whereIn('status', self::REVENUE_STATUSES)->whereBetween('created_at', [$start, $end]);
            })
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_revenue')
            ->get();

        // Đơn hàng theo trạng thái
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

        return view('admin.reports.revenue_pdf', [
            'period' => $period,
            'dateRange' => [
                'start' => $start->format('d/m/Y'),
                'end' => $end->format('d/m/Y'),
            ],
            'stats' => [
                'total_revenue' => $totalRevenue,
                'total_orders' => $totalOrders,
                'total_customers' => $totalCustomers,
                'total_products_sold' => $totalProductsSold,
            ],
            'dailyData' => $dailyRevenueAndOrders,
            'topProducts' => $topProducts,
            'categoryStats' => $categoryStats,
            'orderStatusStats' => $orderStatusStats,
        ]);
    }

    /**
     * Giải quyết khoảng thời gian.
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
     * Lấy dữ liệu doanh thu & đơn hàng theo ngày.
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
}
