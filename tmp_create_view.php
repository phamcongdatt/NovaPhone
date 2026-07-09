<?php
$content = <<<'BLADE'
@extends("admin.layout")

@section("title", "Thống kê đơn hàng")
@section("page-title", "Thống kê đơn hàng")
@section("page-subtitle", "Phân tích và báo cáo doanh thu")

@section("content")
<div class="space-y-5">

    {{-- Bộ lọc thời gian --}}
    <div class="flex flex-wrap items-center gap-3 rounded-2xl border border-blue-400/10 bg-[#081321] p-4 shadow-lg shadow-black/20">
        <span class="text-xs font-semibold text-slate-400">Khoảng thời gian:</span>
        <div class="flex flex-wrap gap-2">
            @foreach(["today" => "Hôm nay", "7days" => "7 ngày qua", "30days" => "30 ngày qua", "90days" => "90 ngày qua", "thisMonth" => "Tháng này", "lastMonth" => "Tháng trước"] as $key => $label)
                <a href="{{ route("admin.orders.statistics", array_merge(request()->except("period"), ["period" => $key])) }}"
                   class="rounded-lg px-3 py-1.5 text-xs font-medium transition-all duration-200
                       {{ $period === $key ? "bg-blue-600 text-white shadow-lg shadow-blue-600/20" : "bg-white/5 text-slate-400 hover:bg-white/10 hover:text-white" }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
        <form method="GET" class="flex items-center gap-2 ml-auto">
            <input type="hidden" name="period" value="custom">
            <input type="date" name="start_date" value="{{ request("start_date") }}"
                   class="rounded-lg border border-white/10 bg-black/20 px-3 py-1.5 text-xs text-white outline-none focus:border-blue-500/40 focus:ring-2 focus:ring-blue-500/10">
            <span class="text-xs text-slate-500">đến</span>
            <input type="date" name="end_date" value="{{ request("end_date") }}"
                   class="rounded-lg border border-white/10 bg-black/20 px-3 py-1.5 text-xs text-white outline-none focus:border-blue-500/40 focus:ring-2 focus:ring-blue-500/10">
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-1.5 text-xs font-bold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-500">
                Áp dụng
            </button>
        </form>
    </div>

    {{-- Dòng 1: 4 thẻ thống kê --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        {{-- Tổng đơn hàng --}}
        <div class="relative overflow-hidden rounded-2xl border border-blue-400/10 bg-[#081321] p-5 shadow-lg shadow-black/20">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">TỔNG ĐƠN HÀNG</span>
                <span class="flex size-8 items-center justify-center rounded-lg bg-blue-500/10 text-blue-500">
                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </span>
            </div>
            <p class="mt-2 text-2xl font-bold text-white">{{ number_format($totalOrders, 0, ",", ".") }}</p>
            <p class="mt-1 text-[10px] text-slate-500">Trong khoảng thời gian đã chọn</p>
        </div>

        {{-- Tổng doanh thu --}}
        <div class="relative overflow-hidden rounded-2xl border border-emerald-400/10 bg-[#081321] p-5 shadow-lg shadow-black/20">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">DOANH THU</span>
                <span class="flex size-8 items-center justify-center rounded-lg bg-emerald-500/10 text-emerald-500">
                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <p class="mt-2 text-2xl font-bold text-white">{{ number_format($totalRevenue, 0, ",", ".") }}đ</p>
            <p class="mt-1 text-[10px] text-slate-500">Từ đơn đã giao/xác nhận</p>
        </div>

        {{-- Giá trị đơn trung bình --}}
        <div class="relative overflow-hidden rounded-2xl border border-violet-400/10 bg-[#081321] p-5 shadow-lg shadow-black/20">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">GIÁ TRỊ TB/ĐƠN</span>
                <span class="flex size-8 items-center justify-center rounded-lg bg-violet-500/10 text-violet-500">
                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                </span>
            </div>
            <p class="mt-2 text-2xl font-bold text-white">{{ number_format($avgOrderValue, 0, ",", ".") }}đ</p>
            <p class="mt-1 text-[10px] text-slate-500">Trung bình mỗi đơn hàng</p>
        </div>

        {{-- Tỷ lệ hoàn thành --}}
        <div class="relative overflow-hidden rounded-2xl border border-amber-400/10 bg-[#081321] p-5 shadow-lg shadow-black/20">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">TỶ LỆ HOÀN THÀNH</span>
                <span class="flex size-8 items-center justify-center rounded-lg bg-amber-500/10 text-amber-500">
                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="mt-2 flex items-end gap-2">
                <p class="text-2xl font-bold text-white">{{ $completionRate }}%</p>
                <span class="mb-1 text-[10px] text-slate-500">({{ $completedOrders }} / {{ $totalOrders }} đơn)</span>
            </div>
            <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-white/5">
                <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-emerald-400 transition-all duration-500" style="width: {{ $completionRate }}%"></div>
            </div>
            <p class="mt-1.5 text-[10px] text-red-400">Tỷ lệ hủy: {{ $cancelRate }}%</p>
        </div>
    </div>

    {{-- Dòng 2: Doanh thu + Trạng thái --}}
    <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">
        <div class="flex min-h-[380px] flex-col rounded-2xl border border-blue-400/10 bg-[#081321] p-5 shadow-lg shadow-black/20 xl:col-span-2">
            <div class="mb-4 flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">DOANH THU THEO NGÀY</span>
                <span class="text-[10px] text-slate-500">{{ $dateRange["start"]->format("d/m/Y") }} - {{ $dateRange["end"]->format("d/m/Y") }}</span>
            </div>
            <div class="mt-auto h-64 w-full">
                <div id="chart-revenue-daily"></div>
            </div>
        </div>

        <div class="flex min-h-[380px] flex-col rounded-2xl border border-blue-400/10 bg-[#081321] p-5 shadow-lg shadow-black/20">
            <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">ĐƠN HÀNG THEO TRẠNG THÁI</span>
            <div class="mt-4 flex-1">
                <div id="chart-orders-status"></div>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-2">
                @foreach($ordersByStatus as $status)
                    <div class="flex items-center gap-2 rounded-lg bg-white/[0.03] px-3 py-2">
                        <span class="size-2.5 rounded-full shrink-0" style="background-color: {{ $status["color"] }}"></span>
                        <span class="text-[11px] text-slate-400 truncate">{{ $status["label"] }}</span>
                        <span class="ml-auto text-[11px] font-bold text-white">{{ $status["count"] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Dòng 3: Thanh toán + Top sản phẩm --}}
    <div class="grid grid-cols-1 gap-5 xl:grid-cols-2">
        <div class="flex min-h-[320px] flex-col rounded-2xl border border-blue-400/10 bg-[#081321] p-5 shadow-lg shadow-black/20">
            <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">PHƯƠNG THỨC THANH TOÁN</span>
            <div class="mt-4 flex-1">
                <div id="chart-payment-method"></div>
            </div>
            <div class="mt-4 space-y-2.5">
                @foreach($ordersByPayment as $method)
                    <div class="flex items-center justify-between rounded-lg bg-white/[0.03] px-4 py-3">
                        <div class="flex items-center gap-3">
                            <span class="size-3 rounded-full shrink-0" style="background-color: {{ $method["color"] }}"></span>
                            <span class="text-xs font-medium text-slate-300">{{ $method["label"] }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-bold text-white">{{ $method["count"] }} đơn</span>
                            <p class="text-[10px] text-slate-500">{{ number_format($method["revenue"], 0, ",", ".") }}đ</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex min-h-[320px] flex-col rounded-2xl border border-blue-400/10 bg-[#081321] p-5 shadow-lg shadow-black/20">
            <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">TOP SẢN PHẨM BÁN CHẠY</span>
            <div class="mt-4 flex-1 overflow-x-auto">
                <table class="w-full whitespace-nowrap text-left text-xs text-gray-300">
                    <thead class="border-b border-white/5 text-gray-500">
                        <tr>
                            <th class="pb-3 font-medium">#</th>
                            <th class="pb-3 font-medium">Sản phẩm</th>
                            <th class="pb-3 text-right font-medium">Đã bán</th>
                            <th class="pb-3 text-right font-medium">Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($topProducts as $index => $product)
                            <tr class="transition hover:bg-white/[0.02]">
                                <td class="py-3">
                                    <span class="flex size-6 items-center justify-center rounded-full text-[10px] font-bold
                                        {{ $index === 0 ? "bg-blue-600 text-white" : ($index === 1 ? "bg-gray-500 text-white" : ($index === 2 ? "bg-orange-500 text-white" : "bg-white/10 text-gray-400")) }}">
                                        {{ $index + 1 }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="flex size-9 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-white/[0.06] bg-white/5">
                                            @if($product["thumbnail"])
                                                <img src="{{ asset("storage/" . ltrim($product["thumbnail"], "/")) }}" alt="{{ $product["product_name"] }}" class="size-full object-contain p-0.5">
                                            @else
                                                <svg class="size-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M8 3h8a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"/></svg>
                                            @endif
                                        </div>
                                        <span class="text-gray-200 line-clamp-1 max-w-[150px]">{{ $product["product_name"] }}</span>
                                    </div>
                                </td>
                                <td class="py-3 text-right font-semibold text-white">{{ $product["total_qty"] }} sp</td>
                                <td class="py-3 text-right text-emerald-400">{{ number_format($product["total_revenue"], 0, ",", ".") }}đ</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-6 text-center text-gray-500">Chưa có dữ liệu</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Dòng 4: Danh mục + Tỉnh thành --}}
    <div class="grid grid-cols-1 gap-5 xl:grid-cols-2">
        <div class="flex min-h-[320px] flex-col rounded-2xl border border-blue-400/10 bg-[#081321] p-5 shadow-lg shadow-black/20">
            <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">DOANH THU THEO DANH MỤC</span>
            <div class="mt-4 flex-1">
                <div id="chart-revenue-category"></div>
            </div>
        </div>

        <div class="flex min-h-[320px] flex-col rounded-2xl border border-blue-400/10 bg-[#081321] p-5 shadow-lg shadow-black/20">
            <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">ĐƠN HÀNG THEO TỈNH/THÀNH</span>
            <div class="mt-4 flex-1 overflow-x-auto">
                <table class="w-full whitespace-nowrap text-left text-xs text-gray-300">
                    <thead class="border-b border-white/5 text-gray-500">
                        <tr>
                            <th class="pb-3 font-medium">Tỉnh/Thành</th>
                            <th class="pb-3 text-center font-medium">Số đơn</th>
                            <th class="pb-3 text-right font-medium">Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($ordersByProvince as $province)
                            <tr class="transition hover:bg-white/[0.02]">
                                <td class="py-2.5 text-gray-200">{{ $province["province"] }}</td>
                                <td class="py-2.5 text-center">
                                    <span class="inline-flex rounded-full bg-blue-500/10 px-2 py-0.5 text-[10px] font-medium text-blue-400">
                                        {{ $province["order_count"] }}
                                    </span>
                                </td>
                                <td class="py-2.5 text-right text-emerald-400">{{ number_format($province["total_revenue"], 0, ",", ".") }}đ</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-6 text-center text-gray-500">Chưa có dữ liệu</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Dòng 5: Xu hướng 12 tháng --}}
    <div class="flex min-h-[380px] flex-col rounded-2xl border border-blue-400/10 bg-[#081321] p-5 shadow-lg shadow-black/20">
        <div class="mb-4 flex items-center justify-between">
            <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">XU HƯỚNG 12 THÁNG GẦN ĐÂY</span>
        </div>
        <div class="mt-auto h-64 w-full">
            <div id="chart-monthly-trend"></div>
        </div>
    </div>

</div>

@push("scripts")
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {

    const revenueDailyData = @json($revenueDaily);
    if (revenueDailyData.length > 0) {
        new ApexCharts(document.querySelector("#chart-revenue-daily"), {
            series: [{ name: "Doanh thu", data: revenueDailyData.map(d => d.revenue) }, { name: "Số đơn", data: revenueDailyData.map(d => d.orders) }],
            chart: { type: "area", height: "100%", toolbar: { show: false } },
            colors: ["#3b82f6", "#a855f7"],
            stroke: { curve: "smooth", width: 2 },
            fill: { type: "gradient", gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0, stops: [0, 100] } },
            dataLabels: { enabled: false },
            xaxis: { categories: revenueDailyData.map(d => d.date), labels: { style: { fontSize: "10px", colors: "#64748b" } }, axisBorder: { show: false } },
            yaxis: [
                { title: { text: "Doanh thu", style: { fontSize: "10px", color: "#64748b" } }, labels: { style: { fontSize: "10px", colors: "#64748b" }, formatter: function(val) { return (val / 1e6).toFixed(1) + "tr"; } } },
                { opposite: true, title: { text: "Đơn", style: { fontSize: "10px", color: "#64748b" } }, labels: { style: { fontSize: "10px", colors: "#64748b" } } }
            ],
            tooltip: { theme: "dark", y: [{ formatter: function(val) { return val.toLocaleString("vi-VN") + " đ"; } }, { formatter: function(val) { return val + " đơn"; } }] },
            legend: { position: "top", fontSize: "11px", labels: { colors: "#94a3b8" } },
            grid: { borderColor: "rgba(255,255,255,0.04)", strokeDashArray: 3 }
        }).render();
    }

    const statusData = @json($ordersByStatus);
    if (statusData.length > 0) {
        new ApexCharts(document.querySelector("#chart-orders-status"), {
            series: statusData.map(s => s.count),
            chart: { type: "donut", height: 260, sparkline: { enabled: true } },
            colors: statusData.map(s => s.color),
            stroke: { show: false },
            plotOptions: { donut: { size: "75%", background: "transparent", labels: { show: true, total: { show: true, label: "Tổng", color: "#94a3b8", fontSize: "12px", formatter: function(w) { return w.globals.seriesTotals.reduce((a, b) => a + b, 0); } } } } },
            tooltip: { theme: "dark" },
            dataLabels: { enabled: false },
            legend: { show: false }
        }).render();
    }

    const paymentData = @json($ordersByPayment);
    if (paymentData.length > 0) {
        new ApexCharts(document.querySelector("#chart-payment-method"), {
            series: [{ name: "Số đơn", data: paymentData.map(m => m.count) }],
            chart: { type: "bar", height: "100%", toolbar: { show: false } },
            colors: paymentData.map(m => m.color),
            plotOptions: { bar: { borderRadius: 8, columnWidth: "50%", distributed: true } },
            dataLabels: { enabled: false },
            xaxis: { categories: paymentData.map(m => m.label), labels: { style: { fontSize: "11px", colors: "#94a3b8" } }, axisBorder: { show: false } },
            yaxis: { labels: { style: { fontSize: "10px", colors: "#64748b" } } },
            tooltip: { theme: "dark" },
            grid: { borderColor: "rgba(255,255,255,0.04)", strokeDashArray: 3 }
        }).render();
    }

    const categoryData = @json($revenueByCategory);
    if (categoryData.length > 0) {
        const catColors = ["#3b82f6", "#8b5cf6", "#f97316", "#22c55e", "#06b6d4", "#ec4899", "#eab308", "#64748b"];
        new ApexCharts(document.querySelector("#chart-revenue-category"), {
            series: [{ name: "Doanh thu", data: categoryData.map(c => c.total_revenue) }],
            chart: { type: "bar", height: "100%", toolbar: { show: false } },
            colors: catColors,
            plotOptions: { bar: { borderRadius: 6, columnWidth: "60%", horizontal: true } },
            dataLabels: { enabled: false },
            xaxis: { labels: { style: { fontSize: "10px", colors: "#64748b" }, formatter: function(val) { return (val / 1e6).toFixed(1) + "tr"; } } },
            yaxis: { labels: { style: { fontSize: "10px", colors: "#94a3b8" } } },
            tooltip: { theme: "dark", y: { formatter: function(val) { return val.toLocaleString("vi-VN") + " đ"; } } },
            grid: { borderColor: "rgba(255,255,255,0.04)", strokeDashArray: 3 }
        }).render();
    }

    const monthlyData = @json($monthlyTrend);
    if (monthlyData.length > 0) {
        new ApexCharts(document.querySelector("#chart-monthly-trend"), {
            series: [{ name: "Doanh thu", type: "column", data: monthlyData.map(m => m.revenue) }, { name: "Đơn hàng", type: "line", data: monthlyData.map(m => m.orders) }],
            chart: { type: "line", height: "100%", toolbar: { show: false } },
            colors: ["#3b82f6", "#f97316"],
            stroke: { curve: "smooth", width: [0, 3] },
            plotOptions: { bar: { borderRadius: 6, columnWidth: "40%" } },
            dataLabels: { enabled: false },
            xaxis: { categories: monthlyData.map(m => m.month), labels: { style: { fontSize: "10px", colors: "#64748b" } }, axisBorder: { show: false } },
            yaxis: [
                { title: { text: "Doanh thu", style: { fontSize: "10px", color: "#64748b" } }, labels: { style: { fontSize: "10px", colors: "#64748b" }, formatter: function(val) { return (val / 1e6).toFixed(0) + "tr"; } } },
                { opposite: true, title: { text: "Đơn", style: { fontSize: "10px", color: "#64748b" } }, labels: { style: { fontSize: "10px", colors: "#64748b" } } }
            ],
            tooltip: { theme: "dark", y: [{ formatter: function(val) { return val.toLocaleString("vi-VN") + " đ"; } }, { formatter: function(val) { return val + " đơn"; } }] },
            legend: { position: "top", fontSize: "11px", labels: { colors: "#94a3b8" } },
            grid: { borderColor: "rgba(255,255,255,0.04)", strokeDashArray: 3 }
        }).render();
    }
});
</script>
@endpush
@endsection
BLADE;

file_put_contents("NovaPhone/resources/views/admin/orders/statistics.blade.php", $content);
echo "Done: statistics.blade.php created\n";