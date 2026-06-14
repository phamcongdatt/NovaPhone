@extends('layouts.app')

@section('title', 'Đặt hàng thành công | NovaPhone')

@section('content')
@php
    $money = fn ($value) => number_format((float) $value, 0, ',', '.').'đ';
    $methodName = match ($order->payment_method) {
        'cod' => 'Thanh toán khi nhận hàng (COD)',
        'momo' => 'Ví điện tử MoMo',
        'vnpay' => 'Ví điện tử VNPAY',
        default => $order->payment_method
    };
    $paymentStatusName = $order->payment_status === 'paid' ? 'Đã thanh toán' : 'Chờ thanh toán';
@endphp

<div class="hero-glow flex min-h-[calc(100vh-68px-340px)] items-center justify-center px-4 py-16 sm:px-6">
    <div class="w-full max-w-2xl overflow-hidden rounded-3xl border border-white/10 bg-night-soft/60 p-8 shadow-2xl shadow-black/50 backdrop-blur-2xl text-center">
        
        {{-- Checkmark Icon --}}
        <div class="mx-auto flex size-20 items-center justify-center rounded-full bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 mb-6 animate-bounce">
            <svg class="size-10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
        </div>

        <h1 class="text-3xl font-black text-white tracking-tight">Đặt hàng thành công!</h1>
        <p class="mt-2 text-sm text-gray-400">Cảm ơn bạn đã tin tưởng mua sắm tại NovaPhone. Mã đơn hàng của bạn là:</p>
        <span class="inline-block mt-3 rounded-full bg-brand-600/20 border border-brand-500/30 px-6 py-2 text-lg font-black text-brand-300 font-mono">{{ $order->order_code }}</span>

        {{-- Chi tiết đơn hàng --}}
        <div class="mt-8 rounded-2xl border border-white/5 bg-white/[0.02] p-6 text-left space-y-4">
            <h2 class="text-base font-bold text-white border-b border-white/10 pb-3">Chi tiết thông tin đơn hàng</h2>
            
            <div class="grid gap-4 sm:grid-cols-2 text-sm">
                <div>
                    <span class="block text-xs text-gray-500">Khách hàng nhận hàng</span>
                    <span class="font-bold text-white mt-1 block">{{ $order->shipping_full_name }}</span>
                    <span class="text-xs text-gray-400">{{ $order->shipping_phone }}</span>
                </div>
                <div>
                    <span class="block text-xs text-gray-500">Hình thức thanh toán</span>
                    <span class="font-bold text-white mt-1 block">{{ $methodName }}</span>
                    <span class="text-xs font-semibold {{ $order->payment_status === 'paid' ? 'text-emerald-400' : 'text-amber-400' }}">{{ $paymentStatusName }}</span>
                </div>
            </div>

            <div class="text-sm">
                <span class="block text-xs text-gray-500">Địa chỉ giao hàng</span>
                <span class="text-gray-300 mt-1 block leading-relaxed">
                    {{ $order->shipping_address }}, {{ $order->shipping_ward }}, {{ $order->shipping_district }}, {{ $order->shipping_province }}
                </span>
            </div>

            <div class="flex justify-between items-baseline border-t border-white/10 pt-4 text-sm">
                <span class="font-bold text-white">Tổng cộng đã thanh toán</span>
                <span class="text-xl font-black text-brand-400">{{ $money($order->total_amount) }}</span>
            </div>
        </div>

        {{-- Lời khuyên --}}
        <div class="mt-6 rounded-xl border border-white/5 bg-white/[0.01] p-4 text-xs text-gray-400 leading-relaxed text-left">
            @if ($order->payment_method === 'cod')
                <strong>Bước tiếp theo:</strong> NovaPhone sẽ gọi điện thoại xác nhận đơn hàng của bạn trong vòng 15 phút. Vui lòng chuẩn bị sẵn số tiền thanh toán khi nhân viên giao hàng liên hệ.
            @else
                <strong>Bước tiếp theo:</strong> Đơn hàng đã được thanh toán trực tuyến thành công. Hệ thống đang tiến hành đóng gói và giao đi trong thời gian sớm nhất.
            @endif
        </div>

        {{-- Buttons --}}
        <div class="mt-8 flex flex-col sm:flex-row gap-3">
            <a 
                href="{{ route('orders.show', $order) }}" 
                class="flex-1 flex items-center justify-center rounded-xl bg-gradient-to-r from-brand-600 to-brand-500 py-3.5 text-sm font-bold text-white shadow-lg shadow-brand-600/20 transition hover:-translate-y-0.5"
            >
                Theo dõi đơn hàng
            </a>
            <a 
                href="{{ route('home') }}" 
                class="flex-1 flex items-center justify-center rounded-xl border border-white/10 bg-white/5 py-3.5 text-sm font-bold text-gray-300 transition hover:bg-white/10 hover:text-white"
            >
                Tiếp tục mua sắm
            </a>
        </div>

    </div>
</div>
@endsection
