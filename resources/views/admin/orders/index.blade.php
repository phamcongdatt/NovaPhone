@extends('admin.layout')

@section('title', 'Đơn hàng')
@section('page-title', 'Quản lý đơn hàng')

@php
    $statusBadge = [
        'pending'    => 'bg-amber-500/15 text-amber-300',
        'confirmed'  => 'bg-blue-500/15 text-blue-300',
        'processing' => 'bg-indigo-500/15 text-indigo-300',
        'shipping'   => 'bg-cyan-500/15 text-cyan-300',
        'delivered'  => 'bg-green-500/15 text-green-400',
        'cancelled'  => 'bg-red-500/15 text-red-400',
    ];
    $paymentLabel = ['pending' => 'Chưa thanh toán', 'paid' => 'Đã thanh toán', 'refunded' => 'Đã hoàn tiền'];
    $paymentBadge = [
        'pending'  => 'bg-white/5 text-gray-400',
        'paid'     => 'bg-green-500/15 text-green-400',
        'refunded' => 'bg-orange-500/15 text-orange-300',
    ];
@endphp

@section('content')

{{-- Thanh công cụ: tìm kiếm + lọc --}}
<div class="mb-5 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
    <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-1 flex-wrap items-center gap-2">
        <div class="relative min-w-[220px] flex-1">
            <input
                type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                placeholder="Tìm theo mã đơn, tên hoặc SĐT người nhận..."
                class="w-full rounded-xl border border-white/10 bg-white/5 py-2.5 pl-10 pr-4 text-sm text-white outline-none transition-all duration-200 placeholder:text-gray-500 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/25"
            >
            <svg class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 10.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z"/></svg>
        </div>

        <select name="status" class="rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 text-sm text-white outline-none focus:border-brand-500">
            <option value="">Tất cả trạng thái</option>
            @foreach ($statusLabels as $value => $label)
                <option value="{{ $value }}" @selected(($filters['status'] ?? null) === $value)>{{ $label }}</option>
            @endforeach
        </select>

        <select name="payment_status" class="rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 text-sm text-white outline-none focus:border-brand-500">
            <option value="">Tất cả thanh toán</option>
            @foreach ($paymentLabel as $value => $label)
                <option value="{{ $value }}" @selected(($filters['payment_status'] ?? null) === $value)>{{ $label }}</option>
            @endforeach
        </select>

        <button type="submit" class="rounded-xl bg-white/5 px-4 py-2.5 text-sm font-semibold text-white transition-all duration-200 hover:bg-white/10">
            Lọc
        </button>

        @if (collect($filters)->filter()->isNotEmpty())
            <a href="{{ route('admin.orders.index') }}" class="text-sm text-gray-500 transition-colors hover:text-gray-300">Xoá lọc</a>
        @endif
    </form>
</div>

{{-- Bảng đơn hàng --}}
<div class="overflow-x-auto rounded-2xl border border-white/5 bg-night-soft">
    <table class="w-full text-left text-sm">
        <thead>
            <tr class="border-b border-white/5 text-xs uppercase tracking-wider text-gray-500">
                <th class="px-4 py-3">Mã đơn</th>
                <th class="px-4 py-3">Khách hàng</th>
                <th class="px-4 py-3 text-right">Tổng tiền</th>
                <th class="px-4 py-3 text-center">Thanh toán</th>
                <th class="px-4 py-3 text-center">Trạng thái</th>
                <th class="px-4 py-3">Ngày đặt</th>
                <th class="px-4 py-3 text-right">Hành động</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-white/5">
            @forelse ($orders as $order)
                <tr class="transition-colors duration-150 hover:bg-white/[0.03]">
                    <td class="px-4 py-3">
                        <p class="font-semibold text-white">{{ $order->order_code }}</p>
                        <p class="mt-0.5 text-xs text-gray-500">{{ $order->items_count ?? $order->items()->count() }} sản phẩm</p>
                    </td>
                    <td class="px-4 py-3">
                        <p class="font-medium text-gray-200">{{ $order->shipping_full_name }}</p>
                        <p class="mt-0.5 text-xs text-gray-500">{{ $order->shipping_phone }}</p>
                    </td>
                    <td class="px-4 py-3 text-right font-semibold text-white">
                        {{ number_format($order->total_amount, 0, ',', '.') }}₫
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="rounded-full px-3 py-1 text-xs font-bold {{ $paymentBadge[$order->payment_status] ?? 'bg-white/5 text-gray-400' }}">
                            {{ $paymentLabel[$order->payment_status] ?? $order->payment_status }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="rounded-full px-3 py-1 text-xs font-bold {{ $statusBadge[$order->status] ?? 'bg-white/5 text-gray-400' }}">
                            {{ $statusLabels[$order->status] ?? $order->status }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-400">{{ $order->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.orders.show', $order) }}"
                           class="inline-flex size-8 items-center justify-center rounded-lg bg-white/5 text-gray-400 transition-all duration-200 hover:bg-brand-600/20 hover:text-brand-400"
                           title="Xem chi tiết">
                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                        Không tìm thấy đơn hàng nào.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
<div class="mt-5">
    {{ $orders->links() }}
</div>

@endsection
