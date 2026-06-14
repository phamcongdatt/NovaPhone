@extends('layouts.app')

@section('title', 'Lịch sử đơn hàng | NovaPhone')

@section('content')
@php
    $money = fn ($value) => number_format((float) $value, 0, ',', '.').'đ';
    
    $statusMap = [
        'pending' => ['label' => 'Chờ xác nhận', 'class' => 'bg-amber-500/10 text-amber-400 border-amber-500/20'],
        'confirmed' => ['label' => 'Đã xác nhận', 'class' => 'bg-blue-500/10 text-blue-400 border-blue-500/20'],
        'processing' => ['label' => 'Đang chuẩn bị hàng', 'class' => 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20'],
        'shipping' => ['label' => 'Đang giao hàng', 'class' => 'bg-sky-500/10 text-sky-400 border-sky-500/20'],
        'delivered' => ['label' => 'Đã giao hàng', 'class' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20'],
        'cancelled' => ['label' => 'Đã hủy', 'class' => 'bg-red-500/10 text-red-400 border-red-500/20'],
    ];
@endphp

<div class="bg-night text-gray-100 min-h-[calc(100vh-68px-340px)] py-8">
    <div class="mx-auto max-w-7xl px-4 sm:px-6">
        
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
            <a href="{{ route('home') }}" class="transition-colors hover:text-brand-400">Trang chủ</a>
            <span>/</span>
            <span class="font-medium text-gray-300">Đơn hàng của tôi</span>
        </nav>

        <h1 class="text-3xl font-black text-white tracking-tight mb-8">Đơn hàng của tôi</h1>

        @if ($orders->isEmpty())
            <div class="rounded-3xl border border-white/5 bg-night-soft p-12 text-center shadow-xl shadow-black/30">
                <div class="mx-auto flex size-16 items-center justify-center rounded-2xl bg-white/5 text-gray-400 mb-4">
                    <svg class="size-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-.621-.504-1.125-1.125-1.125H9.75M12 3v18M3 12h18" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-white mb-2">Bạn chưa có đơn hàng nào</h2>
                <p class="text-gray-400 mb-6 max-w-md mx-auto">Hãy khám phá ngay các sản phẩm hấp dẫn tại cửa hàng để tìm được chiếc điện thoại ưng ý.</p>
                <a href="{{ route('home') }}" class="inline-flex items-center justify-center rounded-xl bg-brand-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-brand-600/20 transition hover:bg-brand-500 hover:-translate-y-0.5">
                    Mua sắm ngay
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($orders as $order)
                    @php
                        $status = $statusMap[$order->status] ?? ['label' => $order->status, 'class' => 'bg-gray-500/10 text-gray-400 border-gray-500/20'];
                    @endphp
                    <div class="rounded-2xl border border-white/5 bg-night-soft p-5 shadow-lg transition duration-200 hover:border-white/10">
                        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 border-b border-white/10 pb-4 mb-4">
                            <div>
                                <span class="text-xs text-gray-500">Mã đơn hàng</span>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-base font-black text-white font-mono">{{ $order->order_code }}</span>
                                    <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-[10px] font-semibold {{ $status['class'] }}">
                                        {{ $status['label'] }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-left sm:text-right">
                                <span class="text-xs text-gray-500 block">Ngày đặt hàng</span>
                                <span class="text-sm font-semibold text-gray-300 mt-0.5 block">{{ $order->created_at->format('H:i d/m/Y') }}</span>
                            </div>
                        </div>

                        {{-- Sản phẩm đại diện --}}
                        <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
                            <div class="space-y-2">
                                <p class="text-xs text-gray-500">Danh sách sản phẩm mua</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($order->items as $item)
                                        <div class="inline-flex items-center gap-2 rounded-xl bg-white/5 border border-white/10 p-1.5 pr-3 text-xs">
                                            <div class="size-6 shrink-0 rounded bg-night p-0.5">
                                                <img src="{{ $item->product_thumbnail ?: 'https://placehold.co/50x50' }}" alt="" class="size-full object-contain">
                                            </div>
                                            <span class="font-bold text-white max-w-[150px] truncate">{{ $item->product_name }}</span>
                                            <span class="text-gray-500">x{{ $item->quantity }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            {{-- Tổng tiền & Xem chi tiết --}}
                            <div class="flex items-center justify-between md:justify-end gap-6 border-t border-white/5 pt-4 md:border-t-0 md:pt-0 shrink-0">
                                <div>
                                    <span class="text-xs text-gray-500 block sm:text-right">Tổng tiền thanh toán</span>
                                    <span class="text-lg font-black text-brand-400 mt-0.5 block">{{ $money($order->total_amount) }}</span>
                                </div>
                                <a 
                                    href="{{ route('orders.show', $order) }}" 
                                    class="rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-xs font-bold text-gray-300 transition hover:bg-white/10 hover:text-white"
                                >
                                    Xem chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Phân trang --}}
                <div class="mt-6">
                    {{ $orders->links() }}
                </div>
            </div>
        @endif

    </div>
</div>
@endsection
