@extends('admin.layout')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Tổng quan hệ thống')

@section('content')
<div class="space-y-6">

    {{-- Dòng 1: Thống kê 4 thẻ --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        {{-- Doanh thu --}}
        <div class="relative overflow-hidden rounded-2xl border border-white/5 bg-[#0f1423] shadow-lg shadow-black/20">
            <div class="p-5 pb-8">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">DOANH THU HÔM NAY</span>
                    <span class="flex size-8 items-center justify-center rounded-lg bg-blue-500/10 text-blue-500">
                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 9h-4.5a1.5 1.5 0 000 3h3a1.5 1.5 0 010 3H9m1.5-6v6" /></svg>
                    </span>
                </div>
                <div class="mt-2">
                    <p class="text-2xl font-bold text-white">{{ number_format($cards['revenue']['value'], 0, ',', '.') }}đ</p>
                    <div class="mt-1 flex items-center text-[11px]">
                        @if($cards['revenue']['delta'] >= 0)
                            <span class="text-emerald-400 font-semibold">+{{ $cards['revenue']['delta'] }}%</span>
                        @else
                            <span class="text-red-400 font-semibold">{{ $cards['revenue']['delta'] }}%</span>
                        @endif
                        <span class="ml-1 text-gray-500">so với hôm qua</span>
                    </div>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-10">
                <div id="chart-spark-revenue"></div>
            </div>
        </div>

        {{-- Đơn hàng --}}
        <div class="relative overflow-hidden rounded-2xl border border-white/5 bg-[#0f1423] shadow-lg shadow-black/20">
            <div class="p-5 pb-8">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">ĐƠN HÀNG</span>
                    <span class="flex size-8 items-center justify-center rounded-lg bg-purple-500/10 text-purple-500">
                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                    </span>
                </div>
                <div class="mt-2">
                    <p class="text-2xl font-bold text-white">{{ number_format($cards['orders']['value'], 0, ',', '.') }}</p>
                    <div class="mt-1 flex items-center text-[11px]">
                        @if($cards['orders']['delta'] >= 0)
                            <span class="text-emerald-400 font-semibold">+{{ $cards['orders']['delta'] }}%</span>
                        @else
                            <span class="text-red-400 font-semibold">{{ $cards['orders']['delta'] }}%</span>
                        @endif
                        <span class="ml-1 text-gray-500">so với hôm qua</span>
                    </div>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-10">
                <div id="chart-spark-orders"></div>
            </div>
        </div>

        {{-- Sản phẩm --}}
        <div class="relative overflow-hidden rounded-2xl border border-white/5 bg-[#0f1423] shadow-lg shadow-black/20">
            <div class="p-5 pb-8">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">SẢN PHẨM</span>
                    <span class="flex size-8 items-center justify-center rounded-lg bg-cyan-500/10 text-cyan-500">
                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                    </span>
                </div>
                <div class="mt-2">
                    <p class="text-2xl font-bold text-white">{{ number_format($cards['products']['value'], 0, ',', '.') }}</p>
                    <div class="mt-1 flex items-center text-[11px]">
                        @if($cards['products']['delta'] >= 0)
                            <span class="text-emerald-400 font-semibold">+{{ $cards['products']['delta'] }}%</span>
                        @else
                            <span class="text-red-400 font-semibold">{{ $cards['products']['delta'] }}%</span>
                        @endif
                        <span class="ml-1 text-gray-500">so với hôm qua</span>
                    </div>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-10">
                <div id="chart-spark-products"></div>
            </div>
        </div>

        {{-- Khách hàng --}}
        <div class="relative overflow-hidden rounded-2xl border border-white/5 bg-[#0f1423] shadow-lg shadow-black/20">
            <div class="p-5 pb-8">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">KHÁCH HÀNG</span>
                    <span class="flex size-8 items-center justify-center rounded-lg bg-orange-500/10 text-orange-500">
                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    </span>
                </div>
                <div class="mt-2">
                    <p class="text-2xl font-bold text-white">{{ number_format($cards['customers']['value'], 0, ',', '.') }}</p>
                    <div class="mt-1 flex items-center text-[11px]">
                        @if($cards['customers']['delta'] >= 0)
                            <span class="text-emerald-400 font-semibold">+{{ $cards['customers']['delta'] }}%</span>
                        @else
                            <span class="text-red-400 font-semibold">{{ $cards['customers']['delta'] }}%</span>
                        @endif
                        <span class="ml-1 text-gray-500">so với hôm qua</span>
                    </div>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-10">
                <div id="chart-spark-customers"></div>
            </div>
        </div>
    </div>

    {{-- Dòng 2: Doanh thu (Area Chart) & Đơn hàng gần đây --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-5">
        
        {{-- Biểu đồ Doanh thu --}}
        <div class="rounded-2xl border border-white/5 bg-[#0f1423] p-5 shadow-lg shadow-black/20 lg:col-span-2 flex flex-col">
            <div class="mb-4 flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">DOANH THU</span>
                <select class="rounded border border-white/10 bg-transparent px-2 py-1 text-xs text-gray-300 focus:outline-none">
                    <option>7 ngày qua</option>
                </select>
            </div>
            <div>
                <p class="text-2xl font-bold text-white">{{ number_format($revenue7d, 0, ',', '.') }}đ 
                    @if($revenue7dDelta >= 0)
                        <span class="text-xs font-semibold text-emerald-400 ml-1">↑{{ $revenue7dDelta }}%</span>
                    @else
                        <span class="text-xs font-semibold text-red-400 ml-1">↓{{ abs($revenue7dDelta) }}%</span>
                    @endif
                </p>
                <p class="text-[10px] text-gray-500">So với 7 ngày trước</p>
            </div>
            <div class="mt-auto pt-4 h-48 w-full -ml-3 -mb-3">
                <div id="chart-main-revenue"></div>
            </div>
        </div>

        {{-- Đơn hàng gần đây --}}
        <div class="rounded-2xl border border-white/5 bg-[#0f1423] p-5 shadow-lg shadow-black/20 lg:col-span-3">
            <div class="mb-4 flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">ĐƠN HÀNG GẦN ĐÂY</span>
                <a href="{{ route('admin.dashboard' ?? '#') }}" class="text-[11px] font-semibold text-blue-400 hover:text-blue-300">Xem tất cả <svg class="inline-block size-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg></a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full whitespace-nowrap text-left text-xs text-gray-300">
                    <thead class="border-b border-white/5 text-gray-500">
                        <tr>
                            <th class="pb-3 font-medium">Mã đơn</th>
                            <th class="pb-3 font-medium">Khách hàng</th>
                            <th class="pb-3 font-medium">Sản phẩm</th>
                            <th class="pb-3 text-right font-medium">Tổng tiền</th>
                            <th class="pb-3 text-center font-medium">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($recentOrders as $order)
                        <tr class="transition hover:bg-white/[0.02]">
                            <td class="py-3 font-medium text-gray-400">#NP{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</td>
                            <td class="py-3">
                                <div class="flex items-center gap-2">
                                    <div class="size-5 overflow-hidden rounded-full bg-white/10 flex items-center justify-center font-bold text-[9px] text-white">
                                        {{ mb_substr($order->user->name ?? 'K', 0, 1) }}
                                    </div>
                                    <span class="text-gray-300">{{ $order->user->name ?? 'Khách' }}</span>
                                </div>
                            </td>
                            <td class="py-3">
                                <div class="flex items-center gap-2">
                                    <div class="size-6 rounded bg-white/5 flex items-center justify-center shrink-0">
                                        <svg class="size-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-gray-300 line-clamp-1 max-w-[120px]">{{ $order->items->first()->product->name ?? 'Sản phẩm' }}</p>
                                        <p class="text-[9px] text-gray-500">1 x {{ $order->items->first()->variant->name ?? 'Mặc định' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 text-right text-gray-300">{{ number_format($order->total_amount, 0, ',', '.') }}đ</td>
                            <td class="py-3 text-center">
                                @php
                                    $statusColor = match($order->status) {
                                        'delivered' => 'bg-emerald-500/10 text-emerald-400',
                                        'shipping'  => 'bg-blue-500/10 text-blue-400',
                                        'processing' => 'bg-orange-500/10 text-orange-400',
                                        'cancelled' => 'bg-red-500/10 text-red-400',
                                        default => 'bg-green-500/10 text-green-400'
                                    };
                                    $statusText = match($order->status) {
                                        'delivered' => 'Hoàn thành',
                                        'shipping'  => 'Đang giao',
                                        'processing' => 'Chờ xác nhận',
                                        'cancelled' => 'Đã hủy',
                                        default => 'Hoàn thành'
                                    };
                                @endphp
                                <span class="inline-flex rounded-full {{ $statusColor }} px-2 py-0.5 text-[9px] font-medium border border-current/20">
                                    {{ $statusText }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                        @if($recentOrders->isEmpty())
                        <tr>
                            <td colspan="5" class="py-6 text-center text-gray-500">Chưa có đơn hàng nào.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Dòng 3: Sản phẩm bán chạy, Tồn kho thấp, Thống kê danh mục --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        
        {{-- Sản phẩm bán chạy --}}
        <div class="rounded-2xl border border-white/5 bg-[#0f1423] p-5 shadow-lg shadow-black/20 flex flex-col">
            <div class="mb-4 flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">SẢN PHẨM BÁN CHẠY</span>
                <a href="{{ route('admin.products.index') }}" class="text-[11px] font-semibold text-blue-400 hover:text-blue-300">Xem tất cả <svg class="inline-block size-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg></a>
            </div>
            <div class="space-y-4 mt-2 flex-1">
                @foreach($bestSellers as $index => $product)
                @php
                    $rankColor = match($index) {
                        0 => 'bg-blue-600 text-white',
                        1 => 'bg-gray-500 text-white',
                        2 => 'bg-orange-500 text-white',
                        default => 'bg-transparent text-gray-500'
                    };
                    $percent = $maxSold > 0 ? ($product->sold_count / $maxSold) * 100 : 0;
                @endphp
                <div class="flex items-center gap-3">
                    <div class="flex size-4 shrink-0 items-center justify-center rounded-full text-[9px] font-bold {{ $rankColor }}">
                        {{ $index + 1 }}
                    </div>
                    <div class="size-7 shrink-0 rounded bg-white/5 flex items-center justify-center">
                         <svg class="size-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </div>
                    <div class="flex-1 min-w-0 pr-2">
                        <p class="text-[11px] text-gray-300 line-clamp-1 mb-1">{{ $product->name }}</p>
                        <div class="h-1 w-full overflow-hidden rounded-full bg-white/5">
                            <div class="h-full bg-blue-600 rounded-full" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                    <div class="shrink-0 text-right w-8">
                        <p class="text-xs font-bold text-gray-200 leading-none">{{ $product->sold_count }}</p>
                        <p class="text-[8px] text-gray-500">đơn</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Tồn kho thấp --}}
        <div class="rounded-2xl border border-white/5 bg-[#0f1423] p-5 shadow-lg shadow-black/20 flex flex-col">
            <div class="mb-4 flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">TỒN KHO THẤP</span>
                <a href="#" class="text-[11px] font-semibold text-blue-400 hover:text-blue-300">Xem tất cả <svg class="inline-block size-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg></a>
            </div>
            <div class="space-y-4 mt-2 flex-1">
                @foreach($lowStock as $inventory)
                <div class="flex items-center gap-3">
                    <div class="size-7 shrink-0 rounded bg-white/5 flex items-center justify-center">
                         <svg class="size-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[11px] text-gray-300 line-clamp-1">{{ $inventory->product->name ?? 'Sản phẩm' }}</p>
                        <p class="text-[9px] text-gray-500">{{ $inventory->variant->name ?? '' }}</p>
                    </div>
                    <div class="shrink-0 text-right">
                        <span class="text-[10px] font-semibold text-red-500">Tồn kho: {{ $inventory->quantity }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Thống kê theo danh mục --}}
        <div class="rounded-2xl border border-white/5 bg-[#0f1423] p-5 shadow-lg shadow-black/20 flex flex-col">
            <div class="mb-4">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">THỐNG KÊ THEO DANH MỤC</span>
            </div>
            <div class="flex items-center flex-1">
                <div class="w-1/2 relative h-32 flex items-center justify-center">
                    <div id="chart-donut-categories" class="w-full h-full -ml-4"></div>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none z-10 -ml-4">
                        <span class="text-[9px] text-gray-400">Tổng</span>
                        <span class="text-base font-bold text-white leading-tight">{{ $categoryTotal }}</span>
                        <span class="text-[8px] text-gray-500">đơn hàng</span>
                    </div>
                </div>
                <div class="w-1/2 pl-2 space-y-2.5">
                    @php
                        $colors = ['#3b82f6', '#8b5cf6', '#f97316', '#22c55e', '#64748b'];
                    @endphp
                    @foreach($categoryStats as $index => $stat)
                        @php
                            $color = $colors[$index % count($colors)];
                            $percent = $categoryTotal > 0 ? round(($stat->value / $categoryTotal) * 100) : 0;
                        @endphp
                        <div class="flex items-center justify-between text-[10px]">
                            <div class="flex items-center gap-1.5">
                                <span class="size-2 rounded-full" style="background-color: {{ $color }}"></span>
                                <span class="text-gray-300 truncate max-w-[50px]">{{ $stat->name }}</span>
                            </div>
                            <span class="text-gray-400">{{ $percent }}% <span class="text-gray-600">({{ $stat->value }} đơn)</span></span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Sparkline options chung
    const sparklineOptions = {
        chart: { type: 'area', height: 40, sparkline: { enabled: true } },
        stroke: { curve: 'smooth', width: 2 },
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0, stops: [0, 100] } },
        tooltip: { fixed: { enabled: false }, x: { show: false }, y: { title: { formatter: function () { return '' } } }, marker: { show: false } }
    };

    // Revenue Sparkline
    new ApexCharts(document.querySelector("#chart-spark-revenue"), {
        ...sparklineOptions,
        colors: ['#3b82f6'],
        series: [{ data: @json($cards['revenue']['series']) }]
    }).render();

    // Orders Sparkline
    new ApexCharts(document.querySelector("#chart-spark-orders"), {
        ...sparklineOptions,
        colors: ['#a855f7'],
        series: [{ data: @json($cards['orders']['series']) }]
    }).render();

    // Products Sparkline
    new ApexCharts(document.querySelector("#chart-spark-products"), {
        ...sparklineOptions,
        colors: ['#06b6d4'],
        series: [{ data: @json($cards['products']['series']) }]
    }).render();

    // Customers Sparkline
    new ApexCharts(document.querySelector("#chart-spark-customers"), {
        ...sparklineOptions,
        colors: ['#f97316'],
        series: [{ data: @json($cards['customers']['series']) }]
    }).render();

    // Main Revenue Chart
    new ApexCharts(document.querySelector("#chart-main-revenue"), {
        series: [{ name: 'Doanh thu', data: @json($revenueSeries) }],
        chart: { type: 'area', height: '100%', width: '100%', toolbar: { show: false }, parentHeightOffset: 0, sparkline: { enabled: true } },
        colors: ['#3b82f6'],
        stroke: { curve: 'smooth', width: 2 },
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0, stops: [0, 100] } },
        markers: { size: 4, colors: ['#0f1423'], strokeColors: '#3b82f6', strokeWidth: 2, hover: { size: 6 } },
        dataLabels: { enabled: false },
        tooltip: { theme: 'dark', y: { formatter: function(val) { return val.toLocaleString('vi-VN') + " đ" } } },
        xaxis: { categories: @json($revenueDays), crosshairs: { show: false } }
    }).render();

    // Donut Chart Categories
    const categoryValues = @json($categoryStats->pluck('value'));
    new ApexCharts(document.querySelector("#chart-donut-categories"), {
        series: categoryValues.length ? categoryValues : [1],
        chart: { type: 'donut', height: 160, sparkline: { enabled: true } },
        colors: ['#3b82f6', '#8b5cf6', '#f97316', '#22c55e', '#64748b'],
        stroke: { show: false },
        plotOptions: { donut: { size: '80%', background: 'transparent' } },
        tooltip: { theme: 'dark', fillSeriesColor: false },
        dataLabels: { enabled: false }
    }).render();
});
</script>
@endpush
@endsection
