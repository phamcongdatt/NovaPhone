@extends('admin.layout')

@section('title', 'Thống kê đơn hàng')
@section('page-title', 'Thống kê đơn hàng')
@section('page-subtitle', 'Phân tích và báo cáo doanh thu')

@section('content')
<div class="space-y-5">

    {{-- Bộ lọc thời gian --}}
    <div class="flex flex-wrap items-center gap-3 rounded-2xl border border-blue-400/10 bg-[#081321] p-4 shadow-lg shadow-black/20">
        <span class="text-xs font-semibold text-slate-400">Khoảng thời gian:</span>
        <div class="flex flex-wrap gap-2">
            @foreach([
                'today' => 'Hôm nay',
                '7days' => '7 ngày qua',
                '30days' => '30 ngày qua',
                '90days' => '90 ngày qua',
                'thisMonth' => 'Tháng này',
                'lastMonth' => 'Tháng trước',
            ] as $key => $label)
                <a href="{{ route('admin.orders.statistics', array_merge(request()->except('period'), ['period' => $key])) }}"
                   class="rounded-lg px-3 py-1.5 text-xs font-medium transition-all duration-200
                       {{ $period === $key
                           ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20'
                           : 'bg-white/5 text-slate-400 hover:bg-white/10 hover:text-white'
                       }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
        <form method="GET" class="flex items-center gap-2 ml-auto">
            <input type="hidden" name="period" value="custom">
            <input type="date" name="start_date" value="{{ request('start_date') }}"
                   class="rounded-lg border border-white/10 bg-black/20 px-3 py-1.5 text-xs text-white outline-none focus:border-blue-500/40 focus:ring-2 focus:ring-blue-500/10">
            <span class="text-xs text-slate-500">đến</span>
            <input type="date" name="end_date" value="{{ request('end_date') }}"
                   class="rounded-lg border border-white/10 bg-black/20 px-3 py-1.5 text-xs text-white outline-none focus:border-blue-500/40 focus:ring-2 focus:ring-blue-500/10">
            <button type="submit" class="rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-blue-500 transition-colors">
                Áp dụng
            </button>
        </form>
    </div>

    {{-- Dòng 1: 4 thẻ thống kê --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        {{-- Tổng đơn hàng --}}
        <div class="rounded-2xl border border-blue-400/10 bg-[#081321] p-5 shadow-lg shadow-black/20">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">TỔNG ĐƠN HÀNG</span>
                <span class="flex size-8 items-center justify-center rounded-lg bg-blue-500/10 text-blue-500">
                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </span>
            </div>
            <p class="mt-2 text-2xl font-bold text-white">{{ number_format($totalOrders, 0, ',', '.') }}</p>
            <p class="mt-1 text-[11px] text-gray-500">Đơn hàng trong kỳ</p>
        </div>

        {{-- Tổng doanh thu --}}
        <div class="rounded-2xl border border-emerald-400/10 bg-[#081321] p-5 shadow-lg shadow-black/20">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">TỔNG DOANH THU</span>
                <span class="flex size-8 items-center justify-center rounded-lg bg-emerald-500/10 text-emerald-500">
                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <p class="mt-2 text-2xl font-bold text-white">{{ number_format($totalRevenue, 0, ',', '.') }}đ</p>
            <p class="mt-1 text-[11px] text-gray-500">Doanh thu đã xác nhận</p>
        </div>

        {{-- Giá trị đơn trung bình --}}
        <div class="rounded-2xl border border-violet-400/10 bg-[#081321] p-5 shadow-lg shadow-black/20">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">GIÁ TRỊ ĐƠN TB</span>
                <span class="flex size-8 items-center justify-center rounded-lg bg-violet-500/10 text-violet-500">
                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                </span>
            </div>
            <p class="mt-2 text-2xl font-bold text-white">{{ number_format($avgOrderValue, 0, ',', '.') }}đ</p>
            <p class="mt-1 text-[11px] text-gray-500">Trung bình mỗi đơn</p>
        </div>

        {{-- Tỷ lệ hoàn thành --}}
        <div class="rounded-2xl border border-amber-400/10 bg-[#081321] p-5 shadow-lg shadow-black/20">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">TỶ LỆ HOÀN THÀNH</span>
                <span class="flex size-8 items-center justify-center rounded-lg bg-amber-500/10 text-amber-500">
                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <p class="mt-2 text-2xl font-bold text-white">{{ $completionRate }}%</p>
            <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-white/5">
                <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-emerald-400 transition-all duration-500"
                     style="width: {{ $completionRate }}%"></div>
            </div>
            <p class="mt-1 text-[11px] text-gray-500">Tỷ lệ hủy: {{ $cancelRate }}%</p>
        </div>
    </div>

    {{-- Dòng 2: Biểu đồ doanh thu & Đơn hàng theo trạng thái --}}
    <div class="grid grid-cols-1 gap-5 xl:grid-cols-5">
        {{-- Biểu đồ doanh thu theo ngày --}}
        <div class="flex min-h-[380px] flex-col rounded-2xl border border-blue-400/10 bg-[#081321] p-5 shadow-lg shadow-black/20 xl:col-span-3">
            <div class="mb-4 flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">DOANH THU THEO NGÀY</span>
                <span class="text-[10px] text-slate-500">{{ $dateRange['start']->format('d/m/Y') }} - {{ $dateRange['end']->format('d/m/Y') }}</span>
            </div>
            <div class="mt-auto h-64 w-full">
                <div id="chart-revenue-daily"></div>
            </div>
        </div>

        {{-- Đơn hàng theo trạng thái --}}
        <div class="flex min-h-[380px] flex-col rounded-2xl border border-blue-400/10 bg-[#081321] p-5 shadow-lg shadow-black/20 xl:col-span-2">
            <div class="mb-4">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">ĐƠN HÀNG THEO TRẠNG THÁI</span>
            </div>
            <div class="flex flex-1 items-center">
                <div class="w-1/2">
                    <div id="chart-status-donut"></div>
                </div>
                <div class="w-1/2 space-y-3 pl-2">
                    @foreach($ordersByStatus as $status)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="size-2.5 rounded-full" style="background-color: {{ $status['color'] }}"></span>
                                <span class="text-[11px] text-gray-300">{{ $status['label'] }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-white">{{ $status['count'] }}</span>
                                <span class="text-[9px] text-gray-500">đơn</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Dòng 3: Top sản phẩm bán chạy & Phương thức thanh toán --}}
    <div class="grid grid-cols-1 gap-5 xl:grid-cols-2">
        {{-- Top sản phẩm bán chạy --}}
        <div class="flex min-h-[400px] flex-col rounded-2xl border border-blue-400/10 bg-[#081321] p-5 shadow-lg shadow-black/20">
            <div class="mb-4 flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">TOP SẢN PHẨM BÁN CHẠY</span>
                <span class="text-[10px] text-slate-500">Theo số lượng bán</span>
            </div>
            <div class="space-y-3 flex-1 overflow-y-auto max-h-[350px] pr-1 custom-scrollbar">
                @forelse($topProducts as $index => $product)
                    @php
                        $maxQty = $topProducts[0]['total_qty'] ?? 1;
                        $percent = ($product['total_qty'] / $maxQty) * 100;
                        $rankColors = ['bg-blue-600 text-white', 'bg-gray-500 text-white', 'bg-orange-500 text-white', 'bg-transparent text-gray-500'];
                        $rankColor = $rankColors[$index] ?? $rankColors[3];
                    @endphp
                    <div class="flex items-center gap-3 rounded-xl bg-white/[0.02] p-3 transition hover:bg-white/[0.04]">
                        <div class="flex size-6 shrink-0 items-center justify-center rounded-full text-[10px] font-bold {{ $rankColor }}">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex size-10 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-white/[0.06] bg-white/5">
                            @if($product['thumbnail'])
                                <img src="{{ asset('storage/' . ltrim($product['thumbnail'], '/')) }}" alt="{{ $product['product_name'] }}" class="size-full object-contain p-1">
                            @else
                                <svg class="size-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-gray-200 truncate">{{ $product['product_name'] }}</p>
                            <div class="mt-1 h-1 w-full overflow-hidden rounded-full bg-white/5">
                                <div class="h-full rounded-full bg-blue-600" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                        <div class="shrink-0 text-right">
                            <p class="text-xs font-bold text-gray-200">{{ $product['total_qty'] }}</p>
                            <p class="text-[9px] text-gray-500">sản phẩm</p>
                            <p class="text-[10px] text-emerald-400 font-semibold mt-0.5">{{ number_format($product['total_revenue'], 0, ',', '.') }}đ</p>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-sm text-gray-500">Chưa có dữ liệu bán hàng.</div>
                @endforelse
            </div>
        </div>

        {{-- Phương thức thanh toán --}}
        <div class="flex min-h-[400px] flex-col rounded-2xl border border-blue-400/10 bg-[#081321] p-5 shadow-lg shadow-black/20">
            <div class="mb-4">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">PHƯƠNG THỨC THANH TOÁN</span>
            </div>
            <div class="flex flex-1">
                <div class="w-1/2">
                    <div id="chart-payment-method"></div>
                </div>
                <div class="w-1/2 space-y-4 pl-2">
                    @foreach($ordersByPayment as $method)
                        <div class="rounded-xl bg-white/[0.02] p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="size-3 rounded-full" style="background-color: {{ $method['color'] }}"></span>
                                <span class="text-sm font-bold text-white">{{ $method['label'] }}</span>
                            </div>
                            <div class="space-y-1">
                                <div class="flex justify-between text-[11px]">
                                    <span class="text-gray-400">Số đơn</span>
                                    <span class="font-semibold text-white">{{ $method['count'] }}</span>
                                </div>
                                <div class="flex justify-between text-[11px]">
                                    <span class="text-gray-400">Doanh thu</span>
                                    <span class="font-semibold text-emerald-400">{{ number_format($method['revenue'], 0, ',', '.') }}đ</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Dòng 4: Xu hướng tháng & Doanh thu theo danh mục --}}
    <div class="grid grid-cols-1 gap-5 xl:grid-cols-2">
        {{-- Xu hướng theo tháng --}}
        <div class="flex min-h-[350px] flex-col rounded-2xl border border-blue-400/10 bg-[#081321] p-5 shadow-lg shadow-black/20">
            <div class="mb-4">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">XU HƯỚNG 12 THÁNG GẦN ĐÂY</span>
            </div>
            <div class="mt-auto h-56 w-full">
                <div id="chart-monthly-trend"></div>
            </div>
        </div>

        {{-- Doanh thu theo danh mục --}}
        <div class="flex min-h-[350px] flex-col rounded-2xl border border-blue-400/10 bg-[#081321] p-5 shadow-lg shadow-black/20">
            <div class="mb-4">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">DOANH THU THEO DANH MỤC</span>
            </div>
            <div class="flex flex-1 items-center">
                <div class="w-1/2">
                    <div id="chart-category-revenue"></div>
                </div>
                <div class="w-1/2 space-y-2.5 pl-2">
                    @php
                        $catColors = ['#3b82f6', '#8b5cf6', '#f97316', '#22c55e', '#64748b', '#ec4899', '#14b8a6', '#f59e0b'];
                    @endphp
                    @foreach($revenueByCategory as $index => $cat)
                        @php $color = $catColors[$index % count($catColors)]; @endphp
                        <div class="flex items-center justify-between text-[11px]">
                            <div class="flex items-center gap-2">
                                <span class="size-2 rounded-full" style="background-color: {{ $color }}"></span>
                                <span class="text-gray-300 truncate max-w-[100px]">{{ $cat['category_name'] }}</span>
                            </div>
                            <span class="text-gray-400">{{ number_format($cat['total_revenue'], 0, ',', '.') }}đ</span>
                        </div>
                    @endforeach
                    @if(empty($revenueByCategory))
                        <p class="text-xs text-gray-500 text-center py-4">Chưa có dữ liệu.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Dòng 5: Đơn hàng theo tỉnh & Phân bố theo giờ --}}
    <div class="grid grid-cols-1 gap-5 xl:grid-cols-2">
        {{-- Đơn hàng theo tỉnh/thành --}}
        <div class="flex min-h-[350px] flex-col rounded-2xl border border-blue-400/10 bg-[#081321] p-5 shadow-lg shadow-black/20">
            <div class="mb-4">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">ĐƠN HÀNG THEO TỈNH/THÀNH</span>
            </div>
            <div class="flex-1 overflow-y-auto max-h-[280px] pr-1 custom-scrollbar">
                <table class="w-full text-left text-xs">
                    <thead class="sticky top-0 bg-[#081321]">
                        <tr class="border-b border-white/5">
                            <th class="pb-2 font-medium text-gray-500">Tỉnh/Thành</th>
                            <th class="pb-2 font-medium text-gray-500 text-center">Số đơn</th>
                            <th class="pb-2 font-medium text-gray-500 text-right">Doanh thu</th>
                            <th class="pb-2 font-medium text-gray-500 text-right">Tỷ lệ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @php $provinceTotal = collect($ordersByProvince)->sum('order_count'); @endphp
                        @forelse($ordersByProvince as $province)
                            @php
                                $pct = $provinceTotal > 0 ? round(($province['order_count'] / $provinceTotal) * 100) : 0;
                            @endphp
                            <tr class="transition hover:bg-white/[0.02]">
                                <td class="py-2.5 font-medium text-gray-300">{{ $province['province'] }}</td>
                                <td class="py-2.5 text-center text-gray-400">{{ $province['order_count'] }}</td>
                                <td class="py-2.5 text-right text-emerald-400 font-semibold">{{ number_format($province['total_revenue'], 0, ',', '.') }}đ</td>
                                <td class="py-2.5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <div class="h-1 w-16 overflow-hidden rounded-full bg-white/5">
                                            <div class="h-full rounded-full bg-blue-600" style="width: {{ $pct }}%"></div>
                                        </div>
                                        <span class="text-gray-400 w-8 text-right">{{ $pct }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-6 text-center text-gray-500">Chưa có dữ liệu.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Phân bố đơn hàng theo giờ --}}
        <div class="flex min-h-[350px] flex-col rounded-2xl border border-blue-400/10 bg-[#081321] p-5 shadow-lg shadow-black/20">
            <div class="mb-4">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">PHÂN BỐ ĐƠN HÀNG THEO GIỜ</span>
            </div>
            <div class="mt-auto h-56 w-full">
                <div id="chart-hourly-dist"></div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
novaChart(function (ApexCharts) {
    const moneyFmt = (v) => v.toLocaleString('vi-VN') + ' đ';

    // ── Doanh thu theo ngày (Area Chart) ─────────────────
    const revenueData = @json($revenueDaily);
    const revenueSeries = revenueData.map(d => d.revenue);
    const revenueCategories = revenueData.map(d => d.date);

    new ApexCharts(document.querySelector("#chart-revenue-daily"), {
        series: [{ name: 'Doanh thu', data: revenueSeries }],
        chart: { type: 'area', height: '100%', toolbar: { show: false }, sparkline: { enabled: false } },
        colors: ['#3b82f6'],
        stroke: { curve: 'smooth', width: 2 },
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0, stops: [0, 100] } },
        dataLabels: { enabled: false },
        xaxis: { categories: revenueCategories, labels: { style: { colors: '#64748b', fontSize: '10px' } }, axisBorder: { show: false }, axisTicks: { show: false } },
        yaxis: { labels: { style: { colors: '#64748b', fontSize: '10px' }, formatter: (v) => v >= 1000000 ? (v/1000000).toFixed(1) + 'tr' : (v/1000).toFixed(0) + 'k' } },
        grid: { borderColor: 'rgba(255,255,255,0.04)', strokeDashArray: 4 },
        tooltip: { theme: 'dark', y: { formatter: moneyFmt } },
        markers: { size: 3, hover: { size: 5 } }
    }).render();

    // ── Đơn hàng theo trạng thái (Donut) ─────────────────
    const statusData = @json($ordersByStatus);
    new ApexCharts(document.querySelector("#chart-status-donut"), {
        series: statusData.map(d => d.count),
        chart: { type: 'donut', height: 200, sparkline: { enabled: true } },
        colors: statusData.map(d => d.color),
        stroke: { show: false },
        plotOptions: { donut: { size: '75%', background: 'transparent', labels: { show: false } } },
        legend: { show: false },
        dataLabels: { enabled: false },
        tooltip: { theme: 'dark', y: { formatter: (v) => v + ' đơn hàng' } }
    }).render();

    // ── Top sản phẩm (Horizontal Bar) ────────────────────
    const topProductsData = @json($topProducts);
    if (topProductsData.length > 0) {
        new ApexCharts(document.querySelector("#chart-top-products"), {
            series: [{ name: 'Số lượng', data: topProductsData.map(d => d.total_qty) }],
            chart: { type: 'bar', height: '100%', toolbar: { show: false }, sparkline: { enabled: true } },
            colors: ['#3b82f6'],
            plotOptions: { bar: { borderRadius: 4, horizontal: true, barHeight: '70%' } },
            xaxis: { labels: { style: { colors: '#64748b', fontSize: '10px' } } },
            yaxis: { labels: { style: { colors: '#64748b', fontSize: '10px' } },
                     categories: topProductsData.map(d => d.product_name.length > 20 ? d.product_name.substring(0, 20) + '...' : d.product_name) },
            dataLabels: { enabled: false },
            grid: { borderColor: 'rgba(255,255,255,0.04)', strokeDashArray: 4 },
            tooltip: { theme: 'dark' }
        }).render();
    }

    // ── Phương thức thanh toán (Donut) ───────────────────
    const paymentData = @json($ordersByPayment);
    new ApexCharts(document.querySelector("#chart-payment-method"), {
        series: paymentData.map(d => d.count),
        chart: { type: 'donut', height: 200, sparkline: { enabled: true } },
        colors: paymentData.map(d => d.color),
        stroke: { show: false },
        plotOptions: { donut: { size: '75%', background: 'transparent', labels: { show: false } } },
        legend: { show: false },
        dataLabels: { enabled: false },
        tooltip: { theme: 'dark', y: { formatter: (v) => v + ' đơn hàng' } }
    }).render();

    // ── Xu hướng theo tháng (Combo: Bar + Line) ──────────
    const monthlyData = @json($monthlyTrend);
    new ApexCharts(document.querySelector("#chart-monthly-trend"), {
        series: [
            { name: 'Đơn hàng', type: 'column', data: monthlyData.map(d => d.orders) },
            { name: 'Doanh thu', type: 'line', data: monthlyData.map(d => d.revenue) }
        ],
        chart: { type: 'line', height: '100%', toolbar: { show: false }, sparkline: { enabled: false } },
        colors: ['#3b82f6', '#22c55e'],
        stroke: { curve: 'smooth', width: [0, 2] },
        plotOptions: { bar: { borderRadius: 4, columnWidth: '50%' } },
        dataLabels: { enabled: false },
        xaxis: { categories: monthlyData.map(d => d.month), labels: { style: { colors: '#64748b', fontSize: '10px' } }, axisBorder: { show: false } },
        yaxis: [
            { labels: { style: { colors: '#64748b', fontSize: '10px' } }, title: { text: 'Đơn', style: { color: '#64748b' } } },
            { opposite: true, labels: { style: { colors: '#64748b', fontSize: '10px' }, formatter: (v) => v >= 1000000 ? (v/1000000).toFixed(1) + 'tr' : (v/1000).toFixed(0) + 'k' }, title: { text: 'Doanh thu', style: { color: '#64748b' } } }
        ],
        grid: { borderColor: 'rgba(255,255,255,0.04)', strokeDashArray: 4 },
        tooltip: { theme: 'dark', shared: true, intersect: false, y: [{ formatter: (v) => v + ' đơn' }, { formatter: moneyFmt }] },
        legend: { position: 'top', labels: { colors: '#94a3b8', useSeriesColors: false } }
    }).render();

    // ── Doanh thu theo danh mục (Donut) ──────────────────
    const catData = @json($revenueByCategory);
    if (catData.length > 0) {
        const catColors = ['#3b82f6', '#8b5cf6', '#f97316', '#22c55e', '#64748b', '#ec4899', '#14b8a6', '#f59e0b'];
        new ApexCharts(document.querySelector("#chart-category-revenue"), {
            series: catData.map(d => d.total_revenue),
            chart: { type: 'donut', height: 200, sparkline: { enabled: true } },
            colors: catColors.slice(0, catData.length),
            stroke: { show: false },
            plotOptions: { donut: { size: '75%', background: 'transparent', labels: { show: false } } },
            legend: { show: false },
            dataLabels: { enabled: false },
            tooltip: { theme: 'dark', y: { formatter: moneyFmt } }
        }).render();
    }

    // ── Phân bố theo giờ (Bar Chart) ─────────────────────
    const hourlyData = @json($hourlyDistribution);
    const hourLabels = Array.from({length: 24}, (_, i) => String(i).padStart(2, '0') + 'h');

    new ApexCharts(document.querySelector("#chart-hourly-dist"), {
        series: [{ name: 'Đơn hàng', data: hourlyData }],
        chart: { type: 'bar', height: '100%', toolbar: { show: false }, sparkline: { enabled: false } },
        colors: ['#3b82f6'],
        plotOptions: { bar: { borderRadius: 3, columnWidth: '60%' } },
        dataLabels: { enabled: false },
        xaxis: { categories: hourLabels, labels: { rotate: -45, rotateAlways: true, style: { colors: '#64748b', fontSize: '9px' } }, axisBorder: { show: false }, tickAmount: 12 },
        yaxis: { labels: { style: { colors: '#64748b', fontSize: '10px' } } },
        grid: { borderColor: 'rgba(255,255,255,0.04)', strokeDashArray: 4 },
        tooltip: { theme: 'dark', y: { formatter: (v) => v + ' đơn hàng' } }
    }).render();
});
</script>
@endpush

@endsection
