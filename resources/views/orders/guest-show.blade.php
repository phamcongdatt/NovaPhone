@extends('layouts.app')

@section('title', 'Quản lý đơn hàng - NovaPhone')

@section('content')
    @php
        $statusLabels = [
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'processing' => 'Đang xử lý',
            'shipping' => 'Đang giao hàng',
            'delivered' => 'Đã giao hàng',
            'received' => 'Đã nhận hàng',
            'cancelled' => 'Đã hủy',
        ];
    @endphp

    <div class="mx-auto max-w-4xl space-y-5">
        <section class="rounded-[28px] border border-[#ece8e2] bg-white p-6 shadow-sm sm:p-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[#8b8b8b]">Đơn hàng của bạn</p>
                    <h1 class="mt-2 text-2xl font-bold text-[#171717]">#{{ $order->order_code }}</h1>
                    <p class="mt-2 text-sm text-[#777]">Trạng thái: <strong>{{ $statusLabels[$order->status] ?? $order->status }}</strong></p>
                </div>

                @if ($guestCancelUrl)
                    <form method="POST" action="{{ $guestCancelUrl }}" onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng này?');">
                        @csrf
                        <button type="submit" class="rounded-full border border-red-200 px-4 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-50">Hủy đơn hàng</button>
                    </form>
                @endif
            </div>
        </section>

        <section class="rounded-[28px] border border-[#ece8e2] bg-white p-6 shadow-sm sm:p-8">
            <h2 class="text-lg font-bold text-[#171717]">Thông tin giao hàng</h2>
            <div class="mt-4 grid gap-2 text-sm text-[#555] sm:grid-cols-2">
                <p><strong>Người nhận:</strong> {{ $order->shipping_full_name }}</p>
                <p><strong>Số điện thoại:</strong> {{ $order->shipping_phone }}</p>
                <p class="sm:col-span-2"><strong>Email:</strong> {{ $order->customer_email }}</p>
                <p class="sm:col-span-2"><strong>Địa chỉ:</strong> {{ collect([$order->shipping_address, $order->shipping_ward, $order->shipping_district, $order->shipping_province])->filter()->join(', ') }}</p>
            </div>
        </section>

        <section class="rounded-[28px] border border-[#ece8e2] bg-white p-6 shadow-sm sm:p-8">
            <h2 class="text-lg font-bold text-[#171717]">Sản phẩm</h2>
            <div class="mt-4 divide-y divide-[#eeeae4]">
                @foreach ($order->items as $item)
                    <div class="flex items-center justify-between gap-4 py-4 first:pt-0">
                        <div>
                            <p class="font-semibold text-[#171717]">{{ $item->product_name }}</p>
                            @if ($item->variant_name)
                                <p class="mt-1 text-xs text-[#777]">{{ $item->variant_name }}</p>
                            @endif
                            <p class="mt-1 text-sm text-[#777]">{{ number_format($item->price, 0, ',', '.') }}₫ × {{ $item->quantity }}</p>
                        </div>
                        <p class="font-bold text-[#171717]">{{ number_format($item->subtotal, 0, ',', '.') }}₫</p>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 flex justify-between border-t border-[#eeeae4] pt-4 font-bold">
                <span>Tổng cộng</span>
                <span>{{ number_format($order->total_amount, 0, ',', '.') }}₫</span>
            </div>
        </section>
    </div>
@endsection
