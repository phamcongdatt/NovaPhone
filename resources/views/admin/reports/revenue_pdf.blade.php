<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo cáo doanh thu NovaPhone - {{ $dateRange['start'] }} đến {{ $dateRange['end'] }}</title>
    <!-- Bootstrap 5 Light CSS (Cho in ấn) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: 'Times New Roman', Times, serif, 'Inter', sans-serif;
            color: #1e293b;
            background-color: #ffffff;
            padding: 20px;
            font-size: 13px;
        }
        .report-header {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }
        .report-title {
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .stat-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 15px;
            background-color: #f8fafc;
            text-align: center;
        }
        .stat-label {
            font-size: 10px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .stat-value {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            margin-top: 5px;
        }
        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            border-left: 4px solid #2563eb;
            padding-left: 10px;
            margin-top: 30px;
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        .chart-container {
            position: relative;
            height: 200px;
            width: 100%;
            margin-bottom: 15px;
            border: 1px solid #f1f5f9;
            padding: 10px;
            border-radius: 8px;
        }
        .table {
            font-size: 11px;
        }
        .table th {
            background-color: #f1f5f9 !important;
            color: #334155;
            font-weight: 700;
        }
        .signature-section {
            margin-top: 50px;
            text-align: right;
            padding-right: 40px;
        }
        .signature-title {
            font-style: italic;
            color: #64748b;
            margin-bottom: 60px;
        }
        .signature-name {
            font-weight: 700;
        }
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            .no-print {
                display: none !important;
            }
            .page-break {
                page-break-before: always;
            }
            .stat-card {
                background-color: #f8fafc !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .table th {
                background-color: #f1f5f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    <!-- Thanh công cụ in ấn (Không in ra) -->
    <div class="container-fluid no-print mb-4 p-3 bg-light border rounded d-flex justify-content-between align-items-center">
        <div>
            <i class="bi bi-info-circle-fill text-primary"></i>
            <span class="ms-1 text-muted">Hộp thoại in sẽ tự động mở sau khi tải xong biểu đồ. Nếu không, hãy click nút bên cạnh.</span>
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-primary btn-sm me-2">
                <i class="bi bi-printer"></i> Tiến hành In / Lưu PDF
            </button>
            <button onclick="window.close()" class="btn btn-secondary btn-sm">
                Đóng trang
            </button>
        </div>
    </div>

    <!-- Header Báo cáo -->
    <div class="container-fluid">
        <div class="row report-header align-items-center">
            <div class="col-8">
                <h3 class="report-title m-0">BÁO CÁO THỐNG KÊ DOANH THU</h3>
                <p class="text-muted m-0 mt-1" style="font-size: 11px;">
                    Hệ thống bán lẻ điện thoại di động NovaPhone
                </p>
                <p class="m-0 mt-1 text-secondary" style="font-size: 11px;">
                    Khoảng thời gian báo cáo: <strong>{{ $dateRange['start'] }}</strong> đến <strong>{{ $dateRange['end'] }}</strong>
                </p>
            </div>
            <div class="col-4 text-end" style="font-size: 11px;">
                <p class="m-0">Mã báo cáo: <strong>RP-{{ time() }}</strong></p>
                <p class="m-0">Ngày lập báo cáo: {{ now()->format('H:i d/m/Y') }}</p>
                <p class="m-0">Người lập báo cáo: {{ Auth::user()->name }}</p>
            </div>
        </div>

        <!-- Tóm tắt số liệu (Cards) -->
        <div class="row g-3">
            <div class="col-3">
                <div class="stat-card">
                    <div class="stat-label">Tổng doanh thu</div>
                    <div class="stat-value text-success">{{ number_format($stats['total_revenue'], 0, ',', '.') }}₫</div>
                </div>
            </div>
            <div class="col-3">
                <div class="stat-card">
                    <div class="stat-label">Tổng đơn hàng</div>
                    <div class="stat-value text-primary">{{ number_format($stats['total_orders'], 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="col-3">
                <div class="stat-card">
                    <div class="stat-label">Sản phẩm bán ra</div>
                    <div class="stat-value text-info">{{ number_format($stats['total_products_sold'], 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="col-3">
                <div class="stat-card">
                    <div class="stat-label">Khách hàng mới</div>
                    <div class="stat-value text-warning">{{ number_format($stats['total_customers'], 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <!-- Các biểu đồ chính (Doanh thu ngày & Danh mục) -->
        <div class="row mt-4">
            <div class="col-8">
                <div class="section-title">Doanh thu & đơn hàng theo ngày</div>
                <div class="chart-container">
                    <canvas id="chart-pdf-revenue"></canvas>
                </div>
            </div>
            <div class="col-4">
                <div class="section-title">Cơ cấu doanh thu danh mục</div>
                <div class="chart-container">
                    <canvas id="chart-pdf-categories"></canvas>
                </div>
            </div>
        </div>

        <!-- Bảng danh sách trạng thái đơn hàng & Doanh thu danh mục -->
        <div class="row mt-4">
            <div class="col-6">
                <div class="section-title">Phân tích đơn hàng</div>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Trạng thái đơn</th>
                            <th class="text-center">Số lượng</th>
                            <th class="text-end">Tỷ lệ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $total = max(array_sum($orderStatusStats), 1);
                        @endphp
                        @foreach([
                            'pending' => 'Chờ xác nhận',
                            'confirmed' => 'Đã xác nhận',
                            'processing' => 'Đang xử lý',
                            'shipping' => 'Đang giao hàng',
                            'delivered' => 'Đã giao thành công',
                            'cancelled' => 'Đã huỷ'
                        ] as $key => $label)
                            <tr>
                                <td>{{ $label }}</td>
                                <td class="text-center font-monospace">{{ $orderStatusStats[$key] ?? 0 }}</td>
                                <td class="text-end font-monospace">{{ round((($orderStatusStats[$key] ?? 0) / $total) * 100, 1) }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="col-6">
                <div class="section-title">Doanh thu danh mục sản phẩm</div>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Tên danh mục</th>
                            <th class="text-center">Số lượng bán</th>
                            <th class="text-end">Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categoryStats as $cat)
                            <tr>
                                <td class="fw-bold">{{ $cat->category_name }}</td>
                                <td class="text-center font-monospace">{{ $cat->total_qty }}</td>
                                <td class="text-end font-monospace">{{ number_format($cat->total_revenue, 0, ',', '.') }}₫</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">Không có dữ liệu.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Ngắt trang sang trang 2 cho Top sản phẩm và Top khách hàng (Yêu cầu báo cáo chuyên nghiệp) -->
        <div class="page-break"></div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="section-title">Top 10 sản phẩm bán chạy nhất</div>
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th style="width: 5%;" class="text-center">STT</th>
                            <th style="width: 55%;">Tên sản phẩm</th>
                            <th style="width: 15%;" class="text-center">Số lượng bán</th>
                            <th style="width: 25%;" class="text-end">Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topProducts as $index => $prod)
                            <tr>
                                <td class="text-center font-monospace">{{ $index + 1 }}</td>
                                <td class="fw-bold">{{ $prod->product_name }}</td>
                                <td class="text-center font-monospace">{{ $prod->total_qty }}</td>
                                <td class="text-end font-monospace">{{ number_format($prod->total_revenue, 0, ',', '.') }}₫</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Không có dữ liệu.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="section-title">Top khách hàng mua nhiều nhất</div>
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th style="width: 5%;" class="text-center">STT</th>
                            <th style="width: 30%;">Họ tên khách hàng</th>
                            <th style="width: 25%;">Email liên hệ</th>
                            <th style="width: 20%;" class="text-center">Số đơn đặt</th>
                            <th style="width: 20%;" class="text-end">Tổng tiền mua</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topCustomers as $index => $cust)
                            <tr>
                                <td class="text-center font-monospace">{{ $index + 1 }}</td>
                                <td class="fw-bold">{{ $cust->customer_name ?? $cust->shipping_full_name }}</td>
                                <td>{{ $cust->customer_email ?? 'Khách vãng lai' }}</td>
                                <td class="text-center font-monospace">{{ $cust->total_orders }} đơn</td>
                                <td class="text-end font-monospace text-success fw-bold">{{ number_format($cust->total_spent, 0, ',', '.') }}₫</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Không có dữ liệu.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Chữ ký xác thực báo cáo -->
        <div class="row mt-5">
            <div class="col-6"></div>
            <div class="col-6 signature-section">
                <p class="m-0">Hà Nội, Ngày {{ date('d') }} tháng {{ date('m') }} năm {{ date('Y') }}</p>
                <p class="signature-title">Người lập báo cáo (ký tên)</p>
                <div style="height: 60px;"></div>
                <p class="signature-name m-0">{{ Auth::user()->name }}</p>
                <p class="text-muted m-0" style="font-size: 11px;">Quản trị viên hệ thống NovaPhone</p>
            </div>
        </div>

    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Cấu hình phông chữ nhẹ nhàng cho bản in
            Chart.defaults.color = '#334155';
            Chart.defaults.borderColor = '#e2e8f0';
            Chart.defaults.font.size = 9;

            const moneyFmt = (v) => v.toLocaleString('vi-VN') + ' ₫';

            // ── 1. Biểu đồ doanh thu hàng ngày (Line Chart) ────────────
            const dailyData = @json($dailyData);
            const dailyDates = dailyData.map(d => d.date);
            const dailyRevenue = dailyData.map(d => d.revenue);

            const ctxDaily = document.getElementById('chart-pdf-revenue').getContext('2d');
            new Chart(ctxDaily, {
                type: 'line',
                data: {
                    labels: dailyDates,
                    datasets: [{
                        label: 'Doanh thu (₫)',
                        data: dailyRevenue,
                        borderColor: '#2563eb',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.25,
                        pointRadius: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: { grid: { display: false } },
                        y: {
                            ticks: {
                                callback: function(value) {
                                    return value >= 1000000 ? (value/1000000).toFixed(1) + 'M' : (value/1000).toFixed(0) + 'K';
                                }
                            }
                        }
                    }
                }
            });

            // ── 2. Biểu đồ tròn danh mục (Pie Chart) ──────────────
            const categoryData = @json($categoryStats);
            if (categoryData.length > 0) {
                const ctxCat = document.getElementById('chart-pdf-categories').getContext('2d');
                const catLabels = categoryData.map(c => c.category_name);
                const catRevenues = categoryData.map(c => c.total_revenue);
                const catColors = ['#2563eb', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#ec4899'];

                new Chart(ctxCat, {
                    type: 'pie',
                    data: {
                        labels: catLabels,
                        datasets: [{
                            data: catRevenues,
                            backgroundColor: catColors.slice(0, categoryData.length),
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            }

            // Tự động mở hộp thoại in sau 1.5 giây
            setTimeout(function () {
                window.print();
            }, 1500);
        });
    </script>
</body>
</html>
