@extends('layouts.app')

@section('title', 'Đặt hàng thành công - NovaPhone')

@section('content')
<div class="space-y-3">
    <div class="flex">
        <span class="rounded-full border border-[#e8e4de] bg-white px-3 py-1 text-[11px] font-semibold text-[#444] shadow-sm">Đặt hàng thành công</span>
    </div>
    <section class="rounded-[28px] border border-[#ece8e2] bg-white p-10 text-center shadow-[0_10px_35px_rgba(0,0,0,.04)]">
        <div class="mx-auto flex size-20 items-center justify-center rounded-full border-4 border-[#dff2df] text-2xl font-bold text-[#30a24a]">✓</div>
        <h1 class="mt-5 text-2xl font-bold">Đặt hàng thành công!</h1>
        <p class="mt-2 text-sm text-[#7f7f7f]">Cảm ơn bạn đã mua hàng tại NovaPhone. Mã đơn hàng của bạn là <span class="font-semibold">#{{ $order->order_code }}</span>.</p>

        <div class="mt-6 space-y-4 max-w-sm mx-auto">
            <div class="rounded-[16px] border border-[#ece8e2] bg-[#fbfaf8] p-4 text-left space-y-2">
                <p class="text-xs text-[#8b8b8b]">Tổng tiền</p>
                <p class="text-2xl font-bold text-[#111]">{{ number_format($order->total_amount, 0, ',', '.') }}đ</p>
                <p class="text-xs text-[#8b8b8b]">Thời gian: {{ $order->created_at->format('d/m/Y H:i') }}</p>
            </div>

            <div class="rounded-[16px] border border-[#ece8e2] bg-[#fbfaf8] p-4 text-left">
                <p class="text-xs text-[#8b8b8b]">Phương thức thanh toán</p>
                @php
                    $paymentMethodMap = [
                        'cod' => 'Thanh toán khi nhận hàng (COD)',
                        'vnpay' => 'Thẻ tín dụng / VNPay',
                    ];
                @endphp
                <p class="mt-1 font-semibold text-[#111]">{{ $paymentMethodMap[$order->payment_method] ?? $order->payment_method }}</p>
            </div>
        </div>

        <div class="mt-6 flex justify-center gap-3">
            <a href="{{ route('orders.show', $order) }}" class="rounded-full bg-black px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#222]">
                Xem chi tiết đơn hàng
            </a>
            <a href="{{ route('home') }}" class="rounded-full border border-[#e8e4de] px-6 py-3 text-sm font-semibold text-[#111] transition hover:bg-[#fbfaf8]">
                Tiếp tục mua sắm
            </a>
        </div>
    </section>
</div>
@endsection
