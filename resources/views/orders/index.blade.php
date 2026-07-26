@extends('layouts.app')
@section('title', 'Đơn hàng của tôi - NovaPhone')

@section('content')
<div class="space-y-3">
    <div class="flex">
        <span class="rounded-full border border-[#e8e4de] bg-white px-3 py-1 text-[11px] font-semibold text-[#444] shadow-sm">Quản lý đơn hàng</span>
    </div>
    <section class="rounded-[28px] border border-[#ece8e2] bg-white p-5 shadow-[0_10px_35px_rgba(0,0,0,.04)]">
        <h1 class="text-2xl font-bold">Đơn hàng của tôi</h1>
        <form method="GET" action="{{ route('orders.index') }}" class="mt-5 grid gap-3 sm:grid-cols-[1fr_180px_170px_auto]">
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Tìm theo mã đơn" class="rounded-[14px] border border-[#e8e4de] bg-[#fbfaf8] px-4 py-2.5 text-sm outline-none focus:border-black">
            <select name="status" class="rounded-[14px] border border-[#e8e4de] bg-white px-4 py-2.5 text-sm outline-none focus:border-black">
                <option value="">Tất cả trạng thái</option>
                @foreach (['pending' => 'Chờ xác nhận', 'confirmed' => 'Đã xác nhận', 'processing' => 'Đang xử lý', 'shipping' => 'Đang giao', 'delivered' => 'Đã giao', 'received' => 'Đã nhận', 'cancelled' => 'Đã hủy'] as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <input type="date" name="order_date" value="{{ request('order_date') }}" class="rounded-[14px] border border-[#e8e4de] bg-[#fbfaf8] px-4 py-2.5 text-sm outline-none focus:border-black">
            <button type="submit" class="rounded-[14px] bg-black px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#222]">Lọc</button>
        </form>
        <div class="mt-5 overflow-hidden rounded-[22px] border border-[#ece8e2]">
            <table class="w-full min-w-[700px] text-left text-sm">
                <thead class="bg-[#fbfaf8] text-[#7c7c7c]">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Mã đơn</th>
                        <th class="px-4 py-3 font-semibold">Ngày đặt</th>
                        <th class="px-4 py-3 font-semibold">Trạng thái</th>
                        <th class="px-4 py-3 font-semibold">Tổng tiền</th>
                        <th class="px-4 py-3 font-semibold">Xem chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr class="border-t border-[#f1ede8]">
                            <td class="px-4 py-4 font-semibold">#{{ $order->order_code }}</td>
                            <td class="px-4 py-4">{{ $order->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-4">
                                @php
                                    $statusMap = [
                                        'pending' => ['label' => 'Chờ xác nhận', 'color' => 'bg-yellow-100 text-yellow-700'],
                                        'confirmed' => ['label' => 'Đã xác nhận', 'color' => 'bg-blue-100 text-blue-700'],
                                        'processing' => ['label' => 'Đang xử lý', 'color' => 'bg-blue-100 text-blue-700'],
                                        'shipping' => ['label' => 'Đang giao', 'color' => 'bg-cyan-100 text-cyan-700'],
                                        'delivered' => ['label' => 'Đã giao', 'color' => 'bg-green-100 text-green-700'],
                                        'received' => ['label' => 'Đã nhận', 'color' => 'bg-green-100 text-green-700'],
                                        'cancelled' => ['label' => 'Đã hủy', 'color' => 'bg-red-100 text-red-700'],
                                    ];
                                    $status = $statusMap[$order->status] ?? ['label' => ucfirst($order->status), 'color' => 'bg-gray-100 text-gray-700'];
                                @endphp
                                <span class="rounded-full {{ $status['color'] }} px-3 py-1 text-xs font-semibold">
                                    {{ $status['label'] }}
                                </span>
                            </td>
                            <td class="px-4 py-4 font-semibold">{{ number_format($order->total_amount ?? 0, 0, ',', '.') }}đ</td>
                            <td class="px-4 py-4">
                                <div class="flex flex-wrap items-center gap-3">
                                    <a href="{{ route('orders.show', $order) }}" class="text-sm font-semibold text-blue-600 hover:underline">Chi tiết</a>
                                    @if ($order->status === 'pending')
                                        <form method="POST" action="{{ route('orders.cancel', $order) }}" onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng này?');">
                                            @csrf
                                            <button type="submit" class="text-sm font-semibold text-red-600 hover:underline">Hủy đơn</button>
                                        </form>
                                    @elseif ($order->status === 'delivered')
                                        <form method="POST" action="{{ route('orders.confirm-received', $order) }}">
                                            @csrf
                                            <button type="submit" class="text-sm font-semibold text-green-700 hover:underline">Đã nhận hàng</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="border-t border-[#f1ede8]">
                            <td colspan="5" class="px-4 py-8 text-center text-[#8b8b8b]">
                                <p class="mb-4">Chưa có đơn hàng nào</p>
                                <a href="{{ route('products.index') }}" class="inline-block rounded-lg bg-black px-6 py-2 text-sm font-semibold text-white transition hover:bg-[#222]">
                                    Tiếp tục mua sắm
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($orders->hasPages())
            <div class="mt-5">{{ $orders->links() }}</div>
        @endif
    </section>
</div>
@endsection
