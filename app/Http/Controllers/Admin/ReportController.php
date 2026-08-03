<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

class ReportController
{
    /**
     * Hiển thị báo cáo doanh thu ở chế độ in/lưu PDF từ trình duyệt.
     */
    public function revenuePdf(Request $request)
    {
        return view('admin.reports.revenue_pdf', $this->reportData($request));
    }

    /**
     * Xuất dữ liệu báo cáo dạng CSV, mở được bằng Excel.
     */
    public function revenueExcel(Request $request)
    {
        $data = $this->reportData($request);
        $filename = 'bao-cao-doanh-thu-' . $data['dateRange']['start'] . '-' . $data['dateRange']['end'] . '.csv';

        return response()->streamDownload(function () use ($data): void {
            $output = fopen('php://output', 'w');

            // BOM giúp Excel nhận đúng tiếng Việt UTF-8.
            fwrite($output, "\xEF\xBB\xBF");

            fputcsv($output, ['BÁO CÁO DOANH THU NOVAPHONE']);
            fputcsv($output, ['Từ ngày', $data['dateRange']['start'], 'Đến ngày', $data['dateRange']['end']]);
            fputcsv($output, []);

            fputcsv($output, ['TỔNG QUAN']);
            fputcsv($output, ['Tổng doanh thu', $data['stats']['total_revenue']]);
            fputcsv($output, ['Tổng đơn hàng', $data['stats']['total_orders']]);
            fputcsv($output, ['Tổng sản phẩm đã bán', $data['stats']['total_products_sold']]);
            fputcsv($output, ['Khách hàng mới', $data['stats']['total_customers']]);
            fputcsv($output, []);

            fputcsv($output, ['TOP SẢN PHẨM BÁN CHẠY']);
            fputcsv($output, ['STT', 'Sản phẩm', 'Số lượng bán', 'Doanh thu']);
            foreach ($data['topProducts'] as $index => $product) {
                fputcsv($output, [
                    $index + 1,
                    $product->product_name,
                    $product->total_qty,
                    $product->total_revenue,
                ]);
            }

            fputcsv($output, []);
            fputcsv($output, ['DOANH THU THEO DANH MỤC']);
            fputcsv($output, ['Danh mục', 'Số lượng bán', 'Doanh thu']);
            foreach ($data['categoryStats'] as $category) {
                fputcsv($output, [
                    $category->category_name,
                    $category->total_qty,
                    $category->total_revenue,
                ]);
            }

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Dùng chung dữ liệu với trang thống kê doanh thu để báo cáo không bị lệch số liệu.
     */
    private function reportData(Request $request): array
    {
        $view = app(RevenueController::class)->index($request);

        return $view->getData();
    }
}
