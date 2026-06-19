@extends('admin.layout')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Tổng quan hệ thống')

@php
    $fmt   = fn ($n) => number_format((float) $n, 0, ',', '.');
    $money = fn ($n) => number_format((float) $n, 0, ',', '.') . 'đ';

    $img = function ($path) {
        if (! $path) return asset('images/placeholder.png');
        return str_starts_with($path, 'http') ? $path : asset('storage/' . $path);
    };

    // Sparkline: trả về polyline points + area path
    $spark = function (array $data, $w = 120, $h = 38) {
        $data = array_values(array_map('floatval', $data));
        if (count($data) < 2) $data = array_pad($data, 2, $data[0] ?? 0);
        $n = count($data);
        $min = min($data); $max = max($data);
        $range = ($max - $min) ?: 1;
        $step = $w / ($n - 1);
        $pts = [];
        foreach ($data as $i => $v) {
            $x = round($i * $step, 2);
            $y = round($h - (($v - $min) / $range) * ($h - 8) - 4, 2);
            $pts[] = [$x, $y];
        }
        $line = implode(' ', array_map(fn ($p) => "{$p[0]},{$p[1]}", $pts));
        $l = end($pts);
        $area = "M{$pts[0][0]},{$h} L" . implode(' L', array_map(fn ($p) => "{$p[0]},{$p[1]}", $pts)) . " L{$l[0]},{$h} Z";
        return ['line' => $line, 'area' => $area];
    };

    $statusMap = [
        'pending'    => ['Chờ xác nhận', 'bg-amber-500/10 text-amber-400 border-amber-500/20'],
        'confirmed'  => ['Đã xác nhận', 'bg-blue-500/10 text-blue-400 border-blue-500/20'],
        'processing' => ['Đang chuẩn bị', 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20'],
        'shipping'   => ['Đang giao', 'bg-sky-500/10 text-sky-400 border-sky-500/20'],
        'delivered'  => ['Hoàn thành', 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20'],
        'cancelled'  => ['Đã hủy', 'bg-red-500/10 text-red-400 border-red-500/20'],
    ];

    $cardMeta = [
        'revenue' => [
            'label' => 'DOANH THU HÔM NAY', 'display' => $money($cards['revenue']['value']),
            'box' => 'bg-brand-500/15 text-brand-400', 'spark' => 'text-brand-500', 'grad' => 'sb',
            'icon' => 'M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z',
        ],
        'orders' => [
            'label' => 'ĐƠN HÀNG', 'display' => $fmt($cards['orders']['value']),
            'box' => 'bg-purple-500/15 text-purple-400', 'spark' => 'text-purple-500', 'grad' => 'sp',
            'icon' => 'M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z',
        ],
        'products' => [
            'label' => 'SẢN PHẨM', 'display' => $fmt($cards['products']['value']),
            'box' => 'bg-cyan-500/15 text-cyan-400', 'spark' => 'text-cyan-500', 'grad' => 'sc',
            'icon' => 'M21 7.5l-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9.75v9.75',
        ],
        'customers' => [
            'label' => 'KHÁCH HÀNG', 'display' => $fmt($cards['customers']['value']),
            'box' => 'bg-amber-500/15 text-amber-400', 'spark' => 'text-amber-500', 'grad' => 'sa',
            'icon' => 'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z',
        ],
    ];
@endphp

@section('content')

    {{-- ══════════ 4 thẻ thống kê ══════════ --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($cardMeta as $key => $meta)
            @php $sp = $spark($cards[$key]['series']); $delta = $cards[$key]['delta']; $up = $delta >= 0; @endphp
            <div class="rounded-2xl border border-white/5 bg-night-card p-5 transition-all duration-200 hover:border-white/10">
                <div class="flex items-start justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-500">{{ $meta['label'] }}</p>
                    <span class="flex size-10 items-center justify-center rounded-xl {{ $meta['box'] }}">
                        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $meta['icon'] }}"/></svg>
                    </span>
                </div>
                <p class="mt-3 text-3xl font-extrabold tracking-tight text-white">{{ $meta['display'] }}</p>
                <div class="mt-1 flex items-center gap-1.5 text-xs">
                    <span class="inline-flex items-center gap-0.5 font-bold {{ $up ? 'text-emerald-400' : 'text-red-400' }}">
                        <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $up ? 'M4.5 15.75l7.5-7.5 7.5 7.5' : 'M19.5 8.25l-7.5 7.5-7.5-7.5' }}"/></svg>
                        {{ $up ? '+' : '' }}{{ number_format($delta, 1, ',', '.') }}%
                    </span>
                    <span class="text-gray-500">so với hôm qua</span>
                </div>
                <svg class="mt-3 h-10 w-full" viewBox="0 0 120 38" preserveAspectRatio="none">
                    <defs>
                        <linearGradient id="{{ $meta['grad'] }}" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="currentColor" stop-opacity="0.35" class="{{ $meta['spark'] }}"/>
                            <stop offset="100%" stop-color="currentColor" stop-opacity="0" class="{{ $meta['spark'] }}"/>
                        </linearGradient>
                    </defs>
                    <path d="{{ $sp['area'] }}" fill="url(#{{ $meta['grad'] }})" class="{{ $meta['spark'] }}"/>
                    <polyline points="{{ $sp['line'] }}" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ $meta['spark'] }}"/>
                </svg>
            </div>
        @endforeach
    </div>

    {{-- ══════════ Doanh thu + Đơn hàng gần đây ══════════ --}}
    <div class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-5">

        {{-- Biểu đồ doanh thu --}}
        <div class="rounded-2xl border border-white/5 bg-night-card p-5 xl:col-span-2">
            <div class="flex items-center justify-between">
                <h2 class="text-xs font-bold uppercase tracking-wider text-gray-400">Doanh thu</h2>
                <span class="rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-gray-400">7 ngày qua</span>
            </div>
            <p class="mt-3 text-2xl font-extrabold tracking-tight text-white">{{ $money($revenue7d) }}
                <span class="text-sm font-bold {{ $revenue7dDelta >= 0 ? 'text-emerald-400' : 'text-red-400' }}">{{ $revenue7dDelta >= 0 ? '+' : '' }}{{ number_format($revenue7dDelta, 1, ',', '.') }}%</span>
            </p>
            <p class="text-xs text-gray-500">So với 7 ngày trước</p>

            @php
                $plotX0 = 42; $plotX1 = 632; $plotY0 = 14; $plotY1 = 196;
                $pw = $plotX1 - $plotX0; $ph = $plotY1 - $plotY0;
                $rev = array_map('floatval', $revenueSeries);
                $rmax = max($rev); $rmax = $rmax > 0 ? $rmax : 1;
                $niceMax = (int) (ceil($rmax / 50000000) * 50000000); if ($niceMax <= 0) $niceMax = 50000000;
                $n = count($rev); $rstep = $n > 1 ? $pw / ($n - 1) : 0;
                $pts = [];
                foreach ($rev as $i => $v) {
                    $x = round($plotX0 + $i * $rstep, 1);
                    $y = round($plotY1 - ($v / $niceMax) * $ph, 1);
                    $pts[] = [$x, $y, $v];
                }
                $line = implode(' ', array_map(fn ($p) => "{$p[0]},{$p[1]}", $pts));
                $l = end($pts);
                $area = "M{$pts[0][0]},{$plotY1} L" . implode(' L', array_map(fn ($p) => "{$p[0]},{$p[1]}", $pts)) . " L{$l[0]},{$plotY1} Z";
                $peak = $pts[0]; foreach ($pts as $p) { if ($p[2] >= $peak[2]) $peak = $p; }
            @endphp

            <svg class="mt-4 w-full" viewBox="0 0 640 230" preserveAspectRatio="none">
                <defs>
                    <linearGradient id="revgrad" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.4"/>
                        <stop offset="100%" stop-color="#3b82f6" stop-opacity="0"/>
                    </linearGradient>
                </defs>
                {{-- Lưới ngang + nhãn trục Y --}}
                @for ($k = 0; $k <= 4; $k++)
                    @php $gy = $plotY1 - $k / 4 * $ph; $lbl = round($niceMax * $k / 4 / 1000000); @endphp
                    <line x1="{{ $plotX0 }}" y1="{{ $gy }}" x2="{{ $plotX1 }}" y2="{{ $gy }}" stroke="#ffffff" stroke-opacity="0.05" stroke-width="1"/>
                    <text x="{{ $plotX0 - 8 }}" y="{{ $gy + 3 }}" text-anchor="end" fill="#6b7280" font-size="10">{{ $lbl }}M</text>
                @endfor
                {{-- Vùng + đường --}}
                <path d="{{ $area }}" fill="url(#revgrad)"/>
                <polyline points="{{ $line }}" fill="none" stroke="#3b82f6" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                {{-- Nhãn trục X --}}
                @foreach ($pts as $i => $p)
                    <text x="{{ $p[0] }}" y="223" text-anchor="middle" fill="#6b7280" font-size="10">{{ $revenueDays[$i] ?? '' }}</text>
                @endforeach
                {{-- Điểm cao nhất + tooltip --}}
                <circle cx="{{ $peak[0] }}" cy="{{ $peak[1] }}" r="5" fill="#3b82f6" stroke="#0a0c12" stroke-width="2.5"/>
                <g transform="translate({{ min(max($peak[0] - 55, $plotX0), $plotX1 - 110) }}, {{ max($peak[1] - 44, 2) }})">
                    <rect width="110" height="34" rx="7" fill="#12151d" stroke="#ffffff" stroke-opacity="0.1"/>
                    <text x="55" y="14" text-anchor="middle" fill="#9ca3af" font-size="9">Cao nhất</text>
                    <text x="55" y="27" text-anchor="middle" fill="#ffffff" font-size="11" font-weight="bold">{{ $fmt($peak[2]) }}đ</text>
                </g>
            </svg>
        </div>

        {{-- Đơn hàng gần đây --}}
        <div class="rounded-2xl border border-white/5 bg-night-card p-5 xl:col-span-3">
            <div class="flex items-center justify-between">
                <h2 class="text-xs font-bold uppercase tracking-wider text-gray-400">Đơn hàng gần đây</h2>
                <span class="text-xs font-semibold text-brand-400">Xem tất cả</span>
            </div>

            <div class="mt-3 overflow-x-auto no-scrollbar">
                <table class="w-full min-w-[560px] text-sm">
                    <thead>
                        <tr class="border-b border-white/5 text-left text-xs text-gray-500">
                            <th class="pb-3 font-semibold">Mã đơn</th>
                            <th class="pb-3 font-semibold">Khách hàng</th>
                            <th class="pb-3 font-semibold">Sản phẩm</th>
                            <th class="pb-3 text-right font-semibold">Tổng tiền</th>
                            <th class="pb-3 text-right font-semibold">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse ($recentOrders as $order)
                            @php
                                $first = $order->items->first();
                                $prodName = $first ? ($first->product?->name ?? $first->product_name ?? '—') : '—';
                                $more = $order->items->count() - 1;
                                [$stLabel, $stClass] = $statusMap[$order->status] ?? [$order->status, 'bg-white/5 text-gray-400 border-white/10'];
                            @endphp
                            <tr class="transition hover:bg-white/[0.02]">
                                <td class="py-3 font-bold text-brand-400">#{{ $order->order_code }}</td>
                                <td class="py-3">
                                    <div class="flex items-center gap-2.5">
                                        <span class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-white/5 text-xs font-bold text-gray-300">{{ mb_strtoupper(mb_substr($order->user->name ?? 'K', 0, 1)) }}</span>
                                        <span class="truncate text-gray-200">{{ $order->user->name ?? 'Khách' }}</span>
                                    </div>
                                </td>
                                <td class="py-3 text-gray-300">
                                    <span class="block max-w-[180px] truncate">{{ $prodName }}</span>
                                    @if ($more > 0)<span class="text-xs text-gray-500">+{{ $more }} sản phẩm</span>@endif
                                </td>
                                <td class="py-3 text-right font-bold text-white">{{ $money($order->total_amount) }}</td>
                                <td class="py-3 text-right">
                                    <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $stClass }}">{{ $stLabel }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-10 text-center text-sm text-gray-500">Chưa có đơn hàng nào.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ══════════ 3 thẻ dưới ══════════ --}}
    <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-3">

        {{-- Sản phẩm bán chạy --}}
        <div class="rounded-2xl border border-white/5 bg-night-card p-5">
            <div class="flex items-center justify-between">
                <h2 class="text-xs font-bold uppercase tracking-wider text-gray-400">Sản phẩm bán chạy</h2>
                <span class="text-xs font-semibold text-brand-400">Xem tất cả</span>
            </div>
            <div class="mt-4 space-y-4">
                @forelse ($bestSellers as $i => $p)
                    <div class="flex items-center gap-3">
                        <span class="flex size-6 shrink-0 items-center justify-center rounded-lg text-xs font-bold
                            {{ $i === 0 ? 'bg-amber-500/20 text-amber-400' : ($i === 1 ? 'bg-gray-400/20 text-gray-300' : ($i === 2 ? 'bg-orange-700/30 text-orange-400' : 'bg-white/5 text-gray-500')) }}">
                            {{ $i + 1 }}
                        </span>
                        <img src="{{ $img($p->thumbnail) }}" alt="{{ $p->name }}" class="size-10 shrink-0 rounded-lg border border-white/5 bg-white/5 object-contain p-0.5">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-gray-200">{{ $p->name }}</p>
                            <div class="mt-1 h-1.5 w-full overflow-hidden rounded-full bg-white/5">
                                <div class="h-full rounded-full bg-brand-500" style="width: {{ round(($p->sold_count / $maxSold) * 100) }}%"></div>
                            </div>
                        </div>
                        <div class="shrink-0 text-right">
                            <p class="text-sm font-bold text-white">{{ $fmt($p->sold_count) }}</p>
                            <p class="text-[11px] text-gray-500">đơn</p>
                        </div>
                    </div>
                @empty
                    <p class="py-8 text-center text-sm text-gray-500">Chưa có dữ liệu.</p>
                @endforelse
            </div>
        </div>

        {{-- Tồn kho thấp --}}
        <div class="rounded-2xl border border-white/5 bg-night-card p-5">
            <div class="flex items-center justify-between">
                <h2 class="text-xs font-bold uppercase tracking-wider text-gray-400">Tồn kho thấp</h2>
                <span class="text-xs font-semibold text-brand-400">Xem tất cả</span>
            </div>
            <div class="mt-4 space-y-3">
                @forelse ($lowStock as $inv)
                    <div class="flex items-center gap-3">
                        <img src="{{ $img($inv->product->thumbnail ?? null) }}" alt="" class="size-10 shrink-0 rounded-lg border border-white/5 bg-white/5 object-contain p-0.5">
                        <p class="min-w-0 flex-1 truncate text-sm font-semibold text-gray-200">{{ $inv->product->name ?? 'Sản phẩm' }}</p>
                        <span class="shrink-0 rounded-full border border-red-500/20 bg-red-500/10 px-2.5 py-1 text-xs font-bold text-red-400">Tồn kho: {{ $inv->quantity }}</span>
                    </div>
                @empty
                    <p class="py-8 text-center text-sm text-gray-500">Tồn kho ổn định.</p>
                @endforelse
            </div>
        </div>

        {{-- Thống kê theo danh mục --}}
        <div class="rounded-2xl border border-white/5 bg-night-card p-5">
            <h2 class="text-xs font-bold uppercase tracking-wider text-gray-400">Thống kê theo danh mục</h2>
            @php
                $donutColors = ['#3b82f6', '#a855f7', '#f59e0b', '#22c55e', '#9ca3af'];
                $total = max($categoryTotal, 1);
                $acc = 0; $stops = []; $legend = [];
                foreach ($categoryStats as $i => $cat) {
                    $color = $donutColors[$i % count($donutColors)];
                    $start = $acc / $total * 100;
                    $acc += $cat->value;
                    $end = $acc / $total * 100;
                    $stops[] = "{$color} {$start}% {$end}%";
                    $legend[] = ['name' => $cat->name, 'color' => $color, 'pct' => round($cat->value / $total * 100), 'value' => $cat->value];
                }
                $gradient = count($stops) ? 'conic-gradient(' . implode(', ', $stops) . ')' : 'conic-gradient(#1f2937 0% 100%)';
            @endphp

            <div class="mt-4 flex flex-col items-center gap-5 sm:flex-row lg:flex-col xl:flex-row">
                <div class="relative size-36 shrink-0 rounded-full" style="background: {{ $gradient }}">
                    <div class="absolute inset-[18%] flex flex-col items-center justify-center rounded-full bg-night-card">
                        <span class="text-[11px] text-gray-500">Tổng</span>
                        <span class="text-2xl font-extrabold text-white">{{ $fmt($ordersTotal) }}</span>
                        <span class="text-[11px] text-gray-500">đơn hàng</span>
                    </div>
                </div>
                <div class="w-full flex-1 space-y-2.5">
                    @forelse ($legend as $lg)
                        <div class="flex items-center gap-2 text-sm">
                            <span class="size-2.5 shrink-0 rounded-full" style="background: {{ $lg['color'] }}"></span>
                            <span class="flex-1 truncate text-gray-300">{{ $lg['name'] }}</span>
                            <span class="font-semibold text-white">{{ $lg['pct'] }}%</span>
                            <span class="text-xs text-gray-500">({{ $fmt($lg['value']) }})</span>
                        </div>
                    @empty
                        <p class="py-6 text-center text-sm text-gray-500">Chưa có dữ liệu bán hàng.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

@endsection
