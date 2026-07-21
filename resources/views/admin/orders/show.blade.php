@extends('admin.layout')

@section('title', 'Chi tiết đơn hàng')
@section('page-title', 'Chi tiết đơn hàng')

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
    $methodLabel  = ['cod' => 'COD (khi nhận hàng)', 'vnpay' => 'VNPay', 'momo' => 'MoMo'];
@endphp

@section('content')

<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-400 transition-colors hover:text-white">
        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Quay lại danh sách
    </a>
    <span class="rounded-full px-4 py-1.5 text-xs font-bold {{ $statusBadge[$order->status] ?? 'bg-white/5 text-gray-400' }}">
        {{ $statusLabels[$order->status] ?? $order->status }}
    </span>
</div>

<div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
    {{-- Cột trái: sản phẩm + tổng tiền --}}
    <div class="space-y-5 lg:col-span-2">
        <div class="rounded-2xl border border-white/5 bg-night-soft p-6">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-bold text-white">{{ $order->order_code }}</h2>
                <span class="text-xs text-gray-500">Đặt lúc {{ $order->created_at?->format('d/m/Y H:i') }}</span>
            </div>

            <div class="divide-y divide-white/5">
                @foreach ($order->items as $item)
                    <div class="flex items-center gap-4 py-3">
                        <span class="flex size-14 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-white/10 bg-white/5">
                            @if ($item->product_thumbnail)
                                <img src="{{ $item->product_thumbnail }}" alt="{{ $item->product_name }}" class="size-full object-cover">
                            @endif
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-medium text-white">{{ $item->product_name }}</p>
                            @if ($item->variant_name)
                                <p class="mt-0.5 text-xs text-gray-500">{{ $item->variant_name }}</p>
                            @endif
                            <p class="mt-0.5 text-xs text-gray-500">{{ number_format($item->price, 0, ',', '.') }}₫ × {{ $item->quantity }}</p>
                        </div>
                        <p class="font-semibold text-white">{{ number_format($item->subtotal, 0, ',', '.') }}₫</p>
                    </div>
                @endforeach
            </div>

            <dl class="mt-4 space-y-2 border-t border-white/5 pt-4 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">Tạm tính</dt><dd class="text-gray-200">{{ number_format($order->subtotal, 0, ',', '.') }}₫</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Giảm giá @if($order->coupon_code)({{ $order->coupon_code }})@endif</dt><dd class="text-gray-200">-{{ number_format($order->discount_amount, 0, ',', '.') }}₫</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Phí vận chuyển</dt><dd class="text-gray-200">{{ number_format($order->shipping_fee, 0, ',', '.') }}₫</dd></div>
                <div class="flex justify-between border-t border-white/5 pt-2 text-base font-bold"><dt class="text-white">Tổng cộng</dt><dd class="text-brand-400">{{ number_format($order->total_amount, 0, ',', '.') }}₫</dd></div>
            </dl>
        </div>

        {{-- Lịch sử trạng thái --}}
        <div class="rounded-2xl border border-white/5 bg-night-soft p-6">
            <h3 class="mb-4 text-sm font-bold uppercase tracking-wider text-gray-500">Lịch sử trạng thái</h3>
            @forelse ($order->statusHistories->sortByDesc('created_at') as $history)
                <div class="flex gap-3 border-l-2 border-white/10 py-2 pl-4">
                    <div>
                        <span class="rounded-full px-2.5 py-0.5 text-xs font-bold {{ $statusBadge[$history->status] ?? 'bg-white/5 text-gray-400' }}">
                            {{ $statusLabels[$history->status] ?? $history->status }}
                        </span>
                        @if ($history->note)
                            <p class="mt-1.5 text-sm text-gray-300">{{ $history->note }}</p>
                        @endif
                        <p class="mt-1 text-xs text-gray-500">
                            {{ $history->created_at?->format('d/m/Y H:i') }}
                            @if ($history->creator) · {{ $history->creator->name }} @endif
                        </p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">Chưa có lịch sử cập nhật.</p>
            @endforelse
        </div>
    </div>

    {{-- Cột phải: khách hàng, giao hàng, thao tác --}}
    <div class="space-y-5">
        <div class="rounded-2xl border border-white/5 bg-night-soft p-6">
            <h3 class="mb-4 text-sm font-bold uppercase tracking-wider text-gray-500">Người nhận</h3>
            <dl class="space-y-3 text-sm">
                <div><dt class="text-xs text-gray-500">Họ tên</dt><dd class="mt-0.5 font-semibold text-white">{{ $order->shipping_full_name }}</dd></div>
                <div><dt class="text-xs text-gray-500">Số điện thoại</dt><dd class="mt-0.5 font-semibold text-white">{{ $order->shipping_phone }}</dd></div>
                <div><dt class="text-xs text-gray-500">Địa chỉ</dt><dd class="mt-0.5 text-gray-200">{{ $order->shipping_address }}, {{ $order->shipping_ward }}, {{ $order->shipping_district }}, {{ $order->shipping_province }}</dd></div>
                @if ($order->note)
                    <div><dt class="text-xs text-gray-500">Ghi chú khách</dt><dd class="mt-0.5 text-gray-200">{{ $order->note }}</dd></div>
                @endif
            </dl>
        </div>

        <div class="rounded-2xl border border-white/5 bg-night-soft p-6">
            <h3 class="mb-4 text-sm font-bold uppercase tracking-wider text-gray-500">Thanh toán</h3>
            <dl class="space-y-3 text-sm">
                <div><dt class="text-xs text-gray-500">Phương thức</dt><dd class="mt-0.5 font-semibold text-white">{{ $methodLabel[$order->payment_method] ?? $order->payment_method }}</dd></div>
                <div><dt class="text-xs text-gray-500">Trạng thái</dt><dd class="mt-0.5 font-semibold text-white">{{ $paymentLabel[$order->payment_status] ?? $order->payment_status }}</dd></div>
            </dl>
        </div>

        {{-- Thao tác --}}
        @if (! empty($nextStatuses))
            <div class="rounded-2xl border border-white/5 bg-night-soft p-6">
                <h3 class="mb-4 text-sm font-bold uppercase tracking-wider text-gray-500">Cập nhật trạng thái</h3>

                @php $advanceStatuses = array_values(array_diff($nextStatuses, ['cancelled'])); @endphp

                @if (! empty($advanceStatuses))
                    <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="space-y-3">
                        @csrf
                        @method('PATCH')
                        <select name="status" required class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 text-sm text-white outline-none focus:border-brand-500">
                            @foreach ($advanceStatuses as $next)
                                <option value="{{ $next }}">{{ $statusLabels[$next] ?? $next }}</option>
                            @endforeach
                        </select>
                        <textarea name="note" rows="2" placeholder="Ghi chú (tùy chọn)..." class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 text-sm text-white outline-none placeholder:text-gray-500 focus:border-brand-500"></textarea>
                        <button type="submit" class="w-full rounded-xl bg-brand-600 py-2.5 text-sm font-bold text-white transition-all duration-200 hover:-translate-y-0.5 hover:bg-brand-500">
                            Cập nhật trạng thái
                        </button>
                    </form>
                @endif

                @if ($order->isCancellable())
                    <form method="POST" action="{{ route('admin.orders.cancel', $order) }}" class="mt-4 space-y-3 border-t border-white/5 pt-4"
                          onsubmit="return confirm('Xác nhận hủy đơn hàng &quot;{{ $order->order_code }}&quot;?')">
                        @csrf
                        @method('PATCH')
                        <textarea name="cancelled_reason" rows="2" required placeholder="Lý do hủy đơn..." class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 text-sm text-white outline-none placeholder:text-gray-500 focus:border-red-500"></textarea>
                        <button type="submit" class="w-full rounded-xl bg-red-600 py-2.5 text-sm font-bold text-white transition-all duration-200 hover:-translate-y-0.5 hover:bg-red-500">
                            Hủy đơn hàng
                        </button>
                    </form>
                @endif
            </div>
        @elseif ($order->status === 'cancelled')
            <div class="rounded-2xl border border-red-500/20 bg-red-500/5 p-6">
                <h3 class="mb-3 text-sm font-bold text-red-400">Đơn đã bị hủy</h3>
                <dl class="space-y-2 text-sm">
                    <div>
                        <dt class="text-xs text-gray-500">Lý do hủy</dt>
                        <dd class="mt-0.5 text-gray-300">{{ $order->cancelled_reason ?? '—' }}</dd>
                    </div>
                    @if ($order->cancelledBy)
                        <div>
                            <dt class="text-xs text-gray-500">Hủy bởi</dt>
                            <dd class="mt-0.5 text-gray-300">{{ $order->cancelledBy->name }}</dd>
                        </div>
                    @endif
                    @if ($order->statusHistories()->where('status', 'cancelled')->first())
                        @php
                            $cancelHistory = $order->statusHistories()->where('status', 'cancelled')->first();
                        @endphp
                        <div>
                            <dt class="text-xs text-gray-500">Thời gian hủy</dt>
                            <dd class="mt-0.5 text-gray-300">{{ $cancelHistory->created_at?->format('d/m/Y H:i') }}</dd>
                        </div>
                    @endif
                </dl>
            </div>
        @else
            <div class="rounded-2xl border border-green-500/20 bg-green-500/5 p-6 text-sm text-green-400">
                Đơn hàng đã hoàn tất.
            </div>
        @endif
    </div>
</div>

@endsection
