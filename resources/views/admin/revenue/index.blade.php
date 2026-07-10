@extends('admin.layout')

@section('title', 'Thống kê doanh thu')
@section('page-title', 'Thống kê doanh thu')
@section('page-subtitle', 'Phân tích doanh thu, sản phẩm bán chạy, khách hàng và danh mục')

@section('content')
<div class="space-y-5">

    {{-- Bộ lọc thời gian & Xuất báo cáo --}}
    <div class="flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-blue-400/10 bg-[#081321] p-4 shadow-lg shadow-black/20">
        <div class="flex flex-wrap items-center gap-3">
            <span class="text-xs font-semibold text-slate-400">Khoảng thời gian:</span>
            <div class="flex flex-wrap gap-1.5">
                @foreach([
                    'today' => 'Hôm nay',
                    'yesterday' => 'Hôm qua',
                    '7days' => '7 ngày',
                    '30days' => '30 ngày',
                    'thisMonth' => 'Tháng này',
                    'thisYear' => 'Năm nay',
                ] as $key => $label)
                    <a href="{{ route('admin.revenue.index', array_merge(request()->except('period'), ['period' => $key])) }}"
                       class="rounded-lg px-3 py-1.5 text-xs font-semibold transition-all duration-200
                           {{ $period === $key
                               ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20'
                               : 'bg-white/5 text-slate-400 hover:bg-white/10 hover:text-white'
                           }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        <form method="GET" action="{{ route('admin.revenue.index') }}" class="flex items-center gap-2">
            <input type="hidden" name="period" value="custom">
            <input type="date" name="start_date" value="{{ request('start_date', $dateRange['start']) }}"
                   class="rounded-lg border border-white/10 bg-black/20 px-3 py-1.5 text-xs text-white outline-none focus:border-blue-500/40 focus:ring-2 focus:ring-blue-500/10">
            <span class="text-xs text-slate-500">đến</span>
            <input type="date" name="end_date" value="{{ request('end_date', $dateRange['end']) }}"
                   class="rounded-lg border border-white/10 bg-black/20 px-3 py-1.5 text-xs text-white outline-none focus:border-blue-500/40 focus:ring-2 focus:ring-blue-500/10">
            <button type="submit" class="rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-blue-500 transition-colors">
                Lọc tùy chọn
            </button>
        </form>

        <div class="flex items-center gap-2">
            {{-- Xuất Excel --}}
            <a href="{{ route('admin.reports.revenue.excel', request()->all()) }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white shadow-lg shadow-emerald-600/10 transition-all hover:bg-emerald-500">
                <i class="bi bi-file-earmark-excel"></i> Xuất Excel
            </a>
            {{-- Xuất PDF --}}
            <a href="{{ route('admin.reports.revenue.pdf', request()->all()) }}" target="_blank"
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 px-4 py-2 text-xs font-bold text-white shadow-lg shadow-red-600/10 transition-all hover:bg-red-500">
                <i class="bi bi-file-earmark-pdf"></i> Xuất PDF (In)
            </a>
        </div>
    </div>

    {{-- Dòng 2: 4 Thẻ chỉ số tổng hợp --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        {{-- Tổng doanh thu --}}
        <div class="rounded-2xl border border-emerald-400/10 bg-[#081321] p-5 shadow-lg shadow-black/20">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">TỔNG DOANH THU</span>
                <span class="flex size-8 items-center justify-center rounded-lg bg-emerald-500/10 text-emerald-400">
                    <i class="bi bi-currency-dollar text-base"></i>
                </span>
            </div>
            <p class="mt-2 text-2xl font-bold text-white">{{ number_format($stats['total_revenue'], 0, ',', '.') }}₫</p>
            <div class="mt-1 flex items-center justify-between text-[11px] text-gray-500">
                <span>Hôm nay: {{ number_format($stats['revenue_today'], 0, ',', '.') }}₫</span>
            </div>
        </div>

        {{-- Tổng đơn hàng --}}
        <div class="rounded-2xl border border-blue-400/10 bg-[#081321] p-5 shadow-lg shadow-black/20">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">TỔNG ĐƠN HÀNG</span>
                <span class="flex size-8 items-center justify-center rounded-lg bg-blue-500/10 text-blue-400">
                    <i class="bi bi-bag-check text-base"></i>
                </span>
            </div>
            <p class="mt-2 text-2xl font-bold text-white">{{ number_format($stats['total_orders'], 0, ',', '.') }}</p>
            <div class="mt-1 flex items-center justify-between text-[11px] text-gray-500">
                <span>Hôm nay: {{ $stats['orders_today'] }} đơn</span>
            </div>
        </div>

        {{-- Tổng sản phẩm bán ra --}}
        <div class="rounded-2xl border border-violet-400/10 bg-[#081321] p-5 shadow-lg shadow-black/20">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">SẢN PHẨM BÁN ĐƯỢC</span>
                <span class="flex size-8 items-center justify-center rounded-lg bg-violet-500/10 text-violet-400">
                    <i class="bi bi-phone text-base"></i>
                </span>
            </div>
            <p class="mt-2 text-2xl font-bold text-white">{{ number_format($stats['total_products_sold'], 0, ',', '.') }}</p>
            <p class="mt-1 text-[11px] text-gray-500">Thiết bị bán thành công</p>
        </div>

        {{-- Khách hàng đăng ký mới --}}
        <div class="rounded-2xl border border-amber-400/10 bg-[#081321] p-5 shadow-lg shadow-black/20">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">KHÁCH HÀNG MỚI</span>
                <span class="flex size-8 items-center justify-center rounded-lg bg-amber-500/10 text-amber-400">
                    <i class="bi bi-people text-base"></i>
                </span>
            </div>
            <p class="mt-2 text-2xl font-bold text-white">{{ number_format($stats['total_customers'], 0, ',', '.') }}</p>
            <p class="mt-1 text-[11px] text-gray-500">Tổng hệ thống: {{ number_format($stats['total_customers_system'], 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Dòng 3: Biểu đồ doanh thu hàng ngày & số lượng đơn hàng --}}
    <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">
        {{-- Biểu đồ doanh thu hàng ngày (Line chart) --}}
        <div class="rounded-2xl border border-white/5 bg-[#081321] p-5 shadow-lg xl:col-span-2 flex flex-col justify-between">
            <div class="mb-4 flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">BIỂU ĐỒ DOANH THU THEO NGÀY</span>
                <span class="text-[10px] text-slate-500">Doanh thu đã nhận</span>
            </div>
            <div class="h-64 w-full relative">
                <canvas id="chart-daily-revenue"></canvas>
            </div>
        </div>

        {{-- Biểu đồ đơn hàng hàng ngày (Bar chart) --}}
        <div class="rounded-2xl border border-white/5 bg-[#081321] p-5 shadow-lg flex flex-col justify-between">
            <div class="mb-4">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">SỐ LƯỢNG ĐƠN HÀNG HÀNG NGÀY</span>
            </div>
            <div class="h-64 w-full relative">
                <canvas id="chart-daily-orders"></canvas>
            </div>
        </div>
    </div>

    {{-- Dòng 4: Biểu đồ xu hướng tháng (Năm nay) & Trạng thái đơn hàng --}}
    <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">
        {{-- Biểu đồ doanh thu theo tháng --}}
        <div class="rounded-2xl border border-white/5 bg-[#081321] p-5 shadow-lg xl:col-span-2 flex flex-col justify-between">
            <div class="mb-4">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">BIỂU ĐỒ DOANH THU THEO THÁNG (NĂM NAY)</span>
            </div>
            <div class="h-64 w-full relative">
                <canvas id="chart-monthly-revenue"></canvas>
            </div>
        </div>

        {{-- Thống kê đơn hàng (Trạng thái đơn hàng & biểu đồ tròn) --}}
        <div class="rounded-2xl border border-white/5 bg-[#081321] p-5 shadow-lg flex flex-col justify-between">
            <div class="mb-4">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">PHÂN LOẠI TRẠNG THÁI ĐƠN HÀNG</span>
            </div>
            <div class="grid grid-cols-2 items-center gap-4 flex-1">
                <div class="h-40 w-full relative">
                    <canvas id="chart-order-status"></canvas>
                </div>
                <div class="space-y-2 text-[10px]">
                    @foreach([
                        'pending' => ['Chờ xác nhận', '#f59e0b', 'pending'],
                        'confirmed' => ['Đã xác nhận', '#3b82f6', 'confirmed'],
                        'processing' => ['Đang xử lý', '#8b5cf6', 'processing'],
                        'shipping' => ['Đang giao hàng', '#06b6d4', 'shipping'],
                        'delivered' => ['Đã giao thành công', '#10b981', 'delivered'],
                        'cancelled' => ['Đã huỷ', '#ef4444', 'cancelled']
                    ] as $key => [$label, $color])
                        <div class="flex items-center justify-between text-slate-300">
                            <div class="flex items-center gap-1.5">
                                <span class="size-2 rounded-full" style="background-color: {{ $color }}"></span>
                                <span>{{ $label }}</span>
                            </div>
                            <span class="font-bold text-white">{{ $orderStatusStats[$key] ?? 0 }} đơn</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Dòng 5: Top sản phẩm bán chạy & Thống kê danh mục --}}
    <div class="grid grid-cols-1 gap-5 xl:grid-cols-5">
        {{-- Top 10 sản phẩm bán chạy --}}
        <div class="rounded-2xl border border-white/5 bg-[#081321] p-5 shadow-lg xl:col-span-3 flex flex-col">
            <div class="mb-4 flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">TOP 10 SẢN PHẨM BÁN CHẠY NHẤT</span>
                <span class="text-[10px] text-slate-500">Xếp theo số lượng bán</span>
            </div>
            <div class="space-y-2.5 flex-1 overflow-y-auto max-h-[350px] pr-1">
                @forelse($topProducts as $index => $prod)
                    <div class="flex items-center gap-3 rounded-xl bg-white/[0.01] border border-white/[0.03] p-3 transition hover:bg-white/[0.03]">
                        <span class="flex size-6 shrink-0 items-center justify-center rounded-full text-[10px] font-bold 
                            {{ $index == 0 ? 'bg-amber-500 text-black' : ($index == 1 ? 'bg-slate-300 text-black' : ($index == 2 ? 'bg-[#b45309] text-white' : 'bg-white/5 text-slate-400')) }}">
                            {{ $index + 1 }}
                        </span>
                        <img
                            src="{{ $prod->product_thumbnail ? asset('storage/' . $prod->product_thumbnail) : asset('images/placeholder.svg') }}"
                            alt="{{ $prod->product_name }}"
                            class="size-10 shrink-0 rounded-lg border border-white/10 object-cover bg-white/5"
                        >
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-white truncate">{{ $prod->product_name }}</p>
                            <p class="mt-0.5 text-[10px] text-slate-500">Mã: {{ $prod->product_id }}</p>
                        </div>
                        <div class="shrink-0 text-right">
                            <p class="text-xs font-bold text-white">{{ number_format($prod->total_qty) }} <span class="text-[9px] text-slate-500 font-normal">đã bán</span></p>
                            <p class="text-[10px] text-emerald-400 font-semibold mt-0.5">{{ number_format($prod->total_revenue, 0, ',', '.') }}₫</p>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-xs text-slate-500 py-10">Chưa có dữ liệu bán hàng trong kỳ.</p>
                @endforelse
            </div>
        </div>

        {{-- Biểu đồ tròn danh mục & Bảng danh mục --}}
        <div class="rounded-2xl border border-white/5 bg-[#081321] p-5 shadow-lg xl:col-span-2 flex flex-col justify-between">
            <div class="mb-4">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">DOANH THU THEO DANH MỤC</span>
            </div>
            <div class="h-44 w-full relative mb-4">
                <canvas id="chart-categories"></canvas>
            </div>
            <div class="space-y-2 max-h-[160px] overflow-y-auto pr-1">
                @forelse($categoryStats as $index => $cat)
                    <div class="flex items-center justify-between text-[11px]">
                        <div class="flex items-center gap-2 text-slate-300">
                            <span class="size-2 rounded-full" style="background-color: {{ ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#ec4899'][$index % 7] }}"></span>
                            <span class="font-semibold">{{ $cat->category_name }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-slate-400">bán {{ $cat->total_qty }} chiếc</span>
                            <span class="font-bold text-white">{{ number_format($cat->total_revenue, 0, ',', '.') }}₫</span>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-xs text-slate-500 py-6">Chưa có dữ liệu.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Dòng 6: Khách hàng mua nhiều nhất --}}
    <div class="rounded-2xl border border-white/5 bg-[#081321] p-5 shadow-lg">
        <div class="mb-4 flex items-center justify-between">
            <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">TOP KHÁCH HÀNG MUA NHIỀU NHẤT</span>
            <span class="text-[10px] text-slate-500">Xếp theo tổng chi tiêu</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-white/5 text-[10px] uppercase tracking-wider text-gray-500">
                        <th class="pb-2.5">Khách hàng</th>
                        <th class="pb-2.5">Email</th>
                        <th class="pb-2.5">Số điện thoại</th>
                        <th class="pb-2.5 text-center">Số đơn hàng</th>
                        <th class="pb-2.5 text-right">Tổng tiền chi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($topCustomers as $cust)
                        <tr>
                            <td class="py-3 text-white font-semibold flex items-center gap-2">
                                <span class="flex size-6 items-center justify-center rounded-full bg-white/5 text-[9px] font-bold text-slate-300">
                                    {{ mb_strtoupper(mb_substr($cust->customer_name ?? $cust->shipping_full_name ?? 'G', 0, 1)) }}
                                </span>
                                {{ $cust->customer_name ?? $cust->shipping_full_name }}
                            </td>
                            <td class="py-3 text-slate-400">{{ $cust->customer_email ?? 'Khách vãng lai' }}</td>
                            <td class="py-3 text-slate-400">{{ $cust->shipping_phone ?? '—' }}</td>
                            <td class="py-3 text-center text-slate-200 font-bold">{{ $cust->total_orders }} đơn</td>
                            <td class="py-3 text-right text-emerald-400 font-bold">{{ number_format($cust->total_spent, 0, ',', '.') }}₫</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-500">Chưa có thông tin mua sắm trong kỳ.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<script>
    novaChartJs(function (Chart) {
        // Thiết lập cấu hình chung cho các biểu đồ Chart.js (Dark theme style)
        Chart.defaults.color = '#94a3b8'; // text labels color
        Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.05)'; // grid line color
        Chart.defaults.font.size = 10;
        Chart.defaults.font.family = 'Inter, ui-sans-serif, system-ui, sans-serif';

        const moneyFmt = (v) => v.toLocaleString('vi-VN') + ' ₫';

        // ── 1. Biểu đồ doanh thu hàng ngày (Line Chart) ────────────
        const dailyData = @json($dailyData);
        const dailyDates = dailyData.map(d => d.date);
        const dailyRevenue = dailyData.map(d => d.revenue);
        const dailyOrders = dailyData.map(d => d.orders);

        const ctxDailyRev = document.getElementById('chart-daily-revenue').getContext('2d');
        const gradientBlue = ctxDailyRev.createLinearGradient(0, 0, 0, 240);
        gradientBlue.addColorStop(0, 'rgba(59, 130, 246, 0.35)');
        gradientBlue.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

        new Chart(ctxDailyRev, {
            type: 'line',
            data: {
                labels: dailyDates,
                datasets: [{
                    label: 'Doanh thu',
                    data: dailyRevenue,
                    borderColor: '#3b82f6',
                    backgroundColor: gradientBlue,
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    pointBackgroundColor: '#3b82f6',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { show: false, display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#fff',
                        bodyColor: '#3b82f6',
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1,
                        padding: 10,
                        callbacks: {
                            label: function(context) {
                                return 'Doanh thu: ' + moneyFmt(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        ticks: {
                            callback: function(value) {
                                return value >= 1000000 ? (value/1000000).toFixed(1) + 'Tr' : (value/1000).toFixed(0) + 'K';
                            }
                        }
                    }
                }
            }
        });

        // ── 2. Biểu đồ số lượng đơn hàng hàng ngày (Bar Chart) ──────
        const ctxDailyOrd = document.getElementById('chart-daily-orders').getContext('2d');
        new Chart(ctxDailyOrd, {
            type: 'bar',
            data: {
                labels: dailyDates,
                datasets: [{
                    label: 'Đơn hàng',
                    data: dailyOrders,
                    backgroundColor: '#10b981',
                    borderRadius: 4,
                    barPercentage: 0.65,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#fff',
                        bodyColor: '#10b981',
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1,
                        padding: 10,
                    }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: { ticks: { stepSize: 1 } }
                }
            }
        });

        // ── 3. Biểu đồ doanh thu theo tháng (Bar Chart) ───────────
        const monthlyData = @json($monthlyData);
        const monthlyLabels = monthlyData.map(d => d.month);
        const monthlyRevenueVals = monthlyData.map(d => d.revenue);

        const ctxMonthly = document.getElementById('chart-monthly-revenue').getContext('2d');
        new Chart(ctxMonthly, {
            type: 'bar',
            data: {
                labels: monthlyLabels,
                datasets: [{
                    label: 'Doanh thu theo tháng',
                    data: monthlyRevenueVals,
                    backgroundColor: '#8b5cf6',
                    borderRadius: 5,
                    barPercentage: 0.5,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#fff',
                        bodyColor: '#8b5cf6',
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1,
                        padding: 10,
                        callbacks: {
                            label: function(context) {
                                return 'Doanh thu: ' + moneyFmt(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        ticks: {
                            callback: function(value) {
                                return value >= 1000000 ? (value/1000000).toFixed(0) + 'Tr' : (value/1000).toFixed(0) + 'K';
                            }
                        }
                    }
                }
            }
        });

        // ── 4. Biểu đồ trạng thái đơn hàng (Doughnut Chart) ──────
        const orderStatusStats = @json($orderStatusStats);
        const ctxStatus = document.getElementById('chart-order-status').getContext('2d');
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: ['Chờ xác nhận', 'Đã xác nhận', 'Đang xử lý', 'Đang giao hàng', 'Đã giao', 'Đã huỷ'],
                datasets: [{
                    data: [
                        orderStatusStats.pending,
                        orderStatusStats.confirmed,
                        orderStatusStats.processing,
                        orderStatusStats.shipping,
                        orderStatusStats.delivered,
                        orderStatusStats.cancelled
                    ],
                    backgroundColor: ['#f59e0b', '#3b82f6', '#8b5cf6', '#06b6d4', '#10b981', '#ef4444'],
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1,
                        padding: 10,
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.raw + ' đơn';
                            }
                        }
                    }
                },
                cutout: '72%'
            }
        });

        // ── 5. Biểu đồ tròn theo danh mục (Pie Chart) ──────────────
        const categoryData = @json($categoryStats);
        if (categoryData.length > 0) {
            const ctxCat = document.getElementById('chart-categories').getContext('2d');
            const catLabels = categoryData.map(c => c.category_name);
            const catRevenues = categoryData.map(c => c.total_revenue);
            const catColors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#ec4899'];

            new Chart(ctxCat, {
                type: 'pie',
                data: {
                    labels: catLabels,
                    datasets: [{
                        data: catRevenues,
                        backgroundColor: catColors.slice(0, categoryData.length),
                        borderWidth: 0,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            borderColor: 'rgba(255, 255, 255, 0.1)',
                            borderWidth: 1,
                            padding: 10,
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + moneyFmt(context.raw);
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush

@endsection
