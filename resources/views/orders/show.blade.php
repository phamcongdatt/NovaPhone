@extends('layouts.app')

@section('title', 'Chi tiết đơn hàng ' . $order->order_code . ' | NovaPhone')

@section('content')
@php
    $money = fn ($value) => number_format((float) $value, 0, ',', '.').'đ';
    
    $statusSteps = [
        ['status' => 'pending', 'label' => 'Đã đặt', 'desc' => 'Đơn hàng đã được tiếp nhận'],
        ['status' => 'confirmed', 'label' => 'Đã xác nhận', 'desc' => 'Đơn hàng đang được xác nhận'],
        ['status' => 'shipping', 'label' => 'Đang giao', 'desc' => 'Đơn hàng đang được giao tới bạn'],
        ['status' => 'delivered', 'label' => 'Hoàn thành', 'desc' => 'Đơn hàng đã được giao thành công'],
    ];

    $currentStep = match ($order->status) {
        'pending' => 0,
        'confirmed', 'processing' => 1,
        'shipping' => 2,
        'delivered' => 3,
        'cancelled' => -1,
        default => 0
    };

    $methodName = match ($order->payment_method) {
        'cod' => 'Thanh toán COD (Khi nhận hàng)',
        'momo' => 'Ví điện tử MoMo',
        'vnpay' => 'Cổng VNPAY',
        default => $order->payment_method
    };
@endphp

<div class="bg-night text-gray-100 min-h-[calc(100vh-68px-340px)] py-8">
    <div class="mx-auto max-w-7xl px-4 sm:px-6">
        
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
            <a href="{{ route('home') }}" class="transition-colors hover:text-brand-400">Trang chủ</a>
            <span>/</span>
            <a href="{{ route('orders.index') }}" class="transition-colors hover:text-brand-400">Đơn hàng của tôi</a>
            <span>/</span>
            <span class="font-medium text-gray-300">{{ $order->order_code }}</span>
        </nav>

        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-black text-white tracking-tight">Chi tiết đơn hàng</h1>
                <p class="text-sm text-gray-500 mt-1">Đặt ngày {{ $order->created_at->format('H:i d/m/Y') }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @if ($order->status === 'pending' && $order->payment_method === 'vnpay' && $order->payment_status === 'pending')
                    <a
                        href="{{ route('checkout.vnpay.create', $order) }}"
                        class="rounded-xl bg-gradient-to-r from-amber-400 to-amber-500 px-4 py-2.5 text-sm font-bold text-gray-950 shadow-lg shadow-amber-500/20 transition hover:-translate-y-0.5"
                    >
                        Tiếp tục thanh toán
                    </a>
                @endif

                @if ($order->status === 'pending')
                    <form method="POST" action="{{ route('orders.cancel', $order) }}" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?');">
                        @csrf
                        <button type="submit" class="rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-2.5 text-sm font-bold text-red-300 transition hover:bg-red-500/20 hover:text-red-100">
                            Hủy đơn
                        </button>
                    </form>
                @endif

                <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-brand-400 hover:text-brand-300 transition-colors">
                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    Quay lại danh sách
                </a>
            </div>
        </div>

        {{-- Sơ đồ Timeline hành trình đơn hàng --}}
        <div class="rounded-3xl border border-white/5 bg-night-soft p-6 sm:p-8 shadow-xl shadow-black/30 mb-8">
            @if ($order->status === 'cancelled')
                <div class="rounded-2xl border border-red-500/20 bg-red-500/10 p-5 flex items-start gap-4">
                    <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-red-500/20 text-red-400 border border-red-500/30">
                        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-white text-base">Đơn hàng đã bị hủy</h3>
                        <p class="text-sm text-red-400 mt-1">Lý do hủy: {{ $order->cancelled_reason ?: 'Không có lý do chi tiết.' }}</p>
                    </div>
                </div>
            @else
                <div class="relative">
                    {{-- Đường thẳng nối --}}
                    <div class="absolute top-5 left-8 right-8 h-1 bg-white/10 hidden md:block z-0">
                        <div class="h-full bg-gradient-to-r from-brand-600 to-cyan-500 transition-all duration-500" style="width: {{ ($currentStep / 3) * 100 }}%"></div>
                    </div>

                    {{-- Các bước timeline --}}
                    <div class="grid gap-6 md:grid-cols-4 relative z-10">
                        @foreach ($statusSteps as $index => $step)
                            @php
                                $isCompleted = $index <= $currentStep;
                                $isActive = $index === $currentStep;
                            @endphp
                            <div class="flex md:flex-col items-center md:text-center gap-4 md:gap-3">
                                
                                {{-- Nút chấm tròn --}}
                                <div class="flex size-10 shrink-0 items-center justify-center rounded-full border transition-all duration-300 {{ 
                                    $isCompleted 
                                        ? 'bg-brand-600 text-white border-brand-500 shadow-lg shadow-brand-600/30' 
                                        : 'bg-night border-white/10 text-gray-500' 
                                }}">
                                    @if ($isCompleted && !$isActive)
                                        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                        </svg>
                                    @else
                                        <span class="text-xs font-bold">{{ $index + 1 }}</span>
                                    @endif
                                </div>

                                {{-- Text --}}
                                <div>
                                    <h4 class="font-bold text-sm transition {{ $isCompleted ? 'text-white' : 'text-gray-500' }}">{{ $step['label'] }}</h4>
                                    <p class="text-xs text-gray-500 mt-0.5 md:max-w-[160px] md:mx-auto">{{ $step['desc'] }}</p>
                                </div>

                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Bố cục chính --}}
        <div class="grid gap-8 lg:grid-cols-3">
            
            {{-- Danh sách sản phẩm --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Chi tiết sản phẩm --}}
                <div class="rounded-3xl border border-white/5 bg-night-soft p-6 shadow-xl shadow-black/30">
                    <h2 class="text-lg font-extrabold text-white mb-4">Sản phẩm trong đơn hàng</h2>
                    
                    <div class="divide-y divide-white/10">
                        @foreach ($order->items as $item)
                            @php
                                $thumbnail = $item->product_thumbnail ?: asset('images/placeholder.svg');
                            @endphp
                            <div class="flex items-center gap-4 py-4 first:pt-0 last:pb-0">
                                
                                {{-- Thumbnail --}}
                                <div class="size-16 shrink-0 overflow-hidden rounded-xl bg-night-card p-1.5 border border-white/5">
                                    <img src="{{ $thumbnail }}" alt="{{ $item->product_name }}" class="size-full object-contain">
                                </div>

                                {{-- Details --}}
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-white truncate text-sm">
                                        @if ($item->product)
                                            <a href="{{ route('products.show', $item->product->slug) }}" class="hover:text-brand-400 transition-colors">
                                                {{ $item->product_name }}
                                            </a>
                                        @else
                                            {{ $item->product_name }}
                                        @endif
                                    </h3>
                                    @if ($item->variant_name)
                                        <p class="text-xs text-brand-300 font-semibold mt-1">Mẫu mã: {{ $item->variant_name }}</p>
                                    @endif
                                    <p class="text-xs text-gray-500 mt-1 block sm:hidden">
                                        {{ $item->quantity }} x {{ $money($item->price) }}
                                    </p>
                                </div>

                                {{-- Price / Qty --}}
                                <div class="text-right hidden sm:block">
                                    <p class="text-sm font-semibold text-gray-400">{{ $money($item->price) }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Số lượng: x{{ $item->quantity }}</p>
                                </div>

                                {{-- Subtotal --}}
                                <div class="text-right shrink-0 min-w-[80px]">
                                    <p class="text-sm font-bold text-white">{{ $money($item->subtotal) }}</p>
                                </div>

                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            {{-- Thông tin khách nhận hàng & Hoá đơn --}}
            <div class="space-y-6">
                
                {{-- Thông tin nhận hàng --}}
                <div class="rounded-3xl border border-white/5 bg-night-soft p-6 shadow-xl shadow-black/30 text-sm">
                    <h2 class="text-base font-extrabold text-white mb-4 border-b border-white/10 pb-3">Thông tin nhận hàng</h2>
                    
                    <div class="space-y-3">
                        <div>
                            <span class="text-xs text-gray-500 block">Họ và tên người nhận</span>
                            <span class="font-bold text-white mt-0.5 block">{{ $order->shipping_full_name }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">Số điện thoại liên hệ</span>
                            <span class="font-bold text-white mt-0.5 block">{{ $order->shipping_phone }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">Địa chỉ nhận hàng</span>
                            <span class="text-gray-300 mt-0.5 block leading-relaxed">
                                {{ $order->shipping_address }}, {{ $order->shipping_ward }}, {{ $order->shipping_district }}, {{ $order->shipping_province }}
                            </span>
                        </div>
                        @if ($order->note)
                            <div>
                                <span class="text-xs text-gray-500 block">Ghi chú</span>
                                <span class="text-gray-400 mt-0.5 block leading-relaxed italic">"{{ $order->note }}"</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Hóa đơn thanh toán --}}
                <div class="rounded-3xl border border-white/5 bg-night-soft p-6 shadow-xl shadow-black/30 text-sm">
                    <h2 class="text-base font-extrabold text-white mb-4 border-b border-white/10 pb-3">Hoá đơn thanh toán</h2>
                    
                    <div class="space-y-3 border-b border-white/10 pb-3 mb-3">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Hình thức</span>
                            <span class="font-semibold text-white">{{ $methodName }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Trạng thái</span>
                            <span class="font-semibold {{ $order->payment_status === 'paid' ? 'text-emerald-400' : 'text-amber-400' }}">
                                {{ $order->payment_status === 'paid' ? 'Đã thanh toán' : 'Chờ thanh toán' }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Tiền hàng</span>
                            <span class="font-semibold text-white">{{ $money($order->subtotal) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Phí giao hàng</span>
                            <span class="font-semibold text-emerald-400">Miễn phí</span>
                        </div>
                    </div>

                    <div class="flex justify-between items-baseline pt-1">
                        <span class="font-bold text-white">Tổng tiền</span>
                        <span class="text-lg font-black text-brand-400">{{ $money($order->total_amount) }}</span>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>
@endsection
