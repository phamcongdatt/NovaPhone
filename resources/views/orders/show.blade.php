@extends('layouts.app')

@section('title', 'Chi tiết đơn hàng - NovaPhone')

@section('content')
    @php
        $statusMeta = [
            'pending' => ['label' => 'Chờ xác nhận', 'class' => 'border-amber-200 bg-amber-50 text-amber-800'],
            'confirmed' => ['label' => 'Đã xác nhận', 'class' => 'border-blue-200 bg-blue-50 text-blue-800'],
            'processing' => ['label' => 'Đang xử lý', 'class' => 'border-blue-200 bg-blue-50 text-blue-800'],
            'shipping' => ['label' => 'Đang giao hàng', 'class' => 'border-cyan-200 bg-cyan-50 text-cyan-800'],
            'delivered' => ['label' => 'Đã giao hàng', 'class' => 'border-emerald-200 bg-emerald-50 text-emerald-800'],
            'received' => ['label' => 'Đã nhận hàng', 'class' => 'border-emerald-200 bg-emerald-50 text-emerald-800'],
            'cancelled' => ['label' => 'Đã hủy', 'class' => 'border-red-200 bg-red-50 text-red-700'],
        ];
        $status = $statusMeta[$order->status] ?? ['label' => ucfirst($order->status), 'class' => 'border-stone-200 bg-stone-50 text-stone-700'];
        $progressStep = match ($order->status) {
            'pending' => 0,
            'confirmed' => 1,
            'processing' => 2,
            'shipping' => 3,
            'delivered', 'received' => 4,
            default => null,
        };
        $paymentMethods = [
            'cod' => 'Thanh toán khi nhận hàng (COD)',
            'bank' => 'Chuyển khoản ngân hàng',
            'vnpay' => 'VNPay',
            'ewallet' => 'Ví điện tử',
        ];
        $paymentStatuses = [
            'paid' => 'Đã thanh toán',
            'pending' => 'Chờ thanh toán',
            'failed' => 'Thanh toán thất bại',
            'refunded' => 'Đã hoàn tiền',
        ];
        $isAwaitingPayment = $order->payment_method === 'vnpay'
            && $order->status === 'pending'
            && $order->payment_status === 'pending';
        $paymentDeadline = $order->created_at?->copy()->addMinutes((int) config('shop.pending_order_timeout_minutes'));
        $remainingPaymentSeconds = $isAwaitingPayment && $paymentDeadline
            ? max(0, now()->diffInSeconds($paymentDeadline, false))
            : 0;
        $progressStages = [
            ['label' => 'Đặt hàng'],
            ['label' => 'Xác nhận'],
            ['label' => 'Đang xử lý'],
            ['label' => 'Đang giao'],
            ['label' => 'Hoàn tất'],
        ];
    @endphp

    <div class="space-y-5">
        <nav class="flex flex-wrap items-center gap-2 text-xs text-[#7a7a7a]" aria-label="Breadcrumb">
            <a href="{{ route('orders.index') }}" class="transition hover:text-black">Đơn hàng của tôi</a>
            <span aria-hidden="true">/</span>
            <span class="font-semibold text-[#222]">#{{ $order->order_code }}</span>
        </nav>

        <section class="rounded-[28px] border border-[#ece8e2] bg-white p-5 shadow-[0_10px_35px_rgba(0,0,0,.04)] sm:p-7">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[#8b8b8b]">Chi tiết đơn hàng</p>
                    <div class="mt-2 flex flex-wrap items-center gap-3">
                        <h1 class="text-2xl font-bold tracking-tight text-[#171717]">#{{ $order->order_code }}</h1>
                        <span class="rounded-full border px-3 py-1 text-xs font-semibold {{ $status['class'] }}">{{ $status['label'] }}</span>
                    </div>
                    <p class="mt-2 text-sm text-[#777]">Đặt lúc {{ $order->created_at?->format('d/m/Y · H:i') }}</p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('orders.index') }}" class="rounded-full border border-[#e8e4de] bg-white px-4 py-2.5 text-sm font-semibold text-[#222] transition-colors duration-300 hover:border-black hover:bg-[#faf9f7]">Quay lại đơn hàng</a>
                    @if ($isAwaitingPayment)
                        <a data-payment-continue href="{{ route('checkout.vnpay.create', $order) }}" class="rounded-full bg-[#0a84ff] px-4 py-2.5 text-sm font-semibold text-white transition-colors duration-300 hover:bg-[#006edb]">Tiếp tục thanh toán</a>
                    @endif
                    @if ($order->status === 'pending')
                        <form method="POST" action="{{ route('orders.cancel', $order) }}" onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng này?');">
                            @csrf
                            <button type="submit" class="rounded-full border border-red-200 bg-white px-4 py-2.5 text-sm font-semibold text-red-600 transition-colors duration-300 hover:bg-red-50">Hủy đơn hàng</button>
                        </form>
                    @elseif ($order->status === 'delivered')
                        <form method="POST" action="{{ route('orders.confirm-received', $order) }}">
                            @csrf
                            <button type="submit" class="rounded-full bg-black px-4 py-2.5 text-sm font-semibold text-white transition-colors duration-300 hover:bg-[#222]">Xác nhận đã nhận</button>
                        </form>
                    @endif
                </div>
            </div>
        </section>

        <div class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_360px]">
            <div class="space-y-5">
                <section class="rounded-[28px] border border-[#ece8e2] bg-white p-5 shadow-[0_10px_35px_rgba(0,0,0,.04)] sm:p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-bold text-[#171717]">Tiến độ đơn hàng</h2>
                            <p class="mt-1 text-sm text-[#7f7f7f]">Trạng thái được cập nhật theo đơn hàng của bạn.</p>
                        </div>
                    </div>

                    @if ($order->status === 'cancelled')
                        <div class="mt-5 rounded-[18px] border border-red-100 bg-red-50 p-4 text-sm text-red-800">
                            <p class="font-semibold">Đơn hàng đã được hủy.</p>
                            @if ($order->cancelled_reason)
                                <p class="mt-1 leading-6">{{ $order->cancelled_reason }}</p>
                            @endif
                        </div>
                    @else
                        <ol class="mt-6 grid gap-4 sm:grid-cols-5">
                            @foreach ($progressStages as $index => $stage)
                                @php
                                    $completed = $progressStep !== null && $index < $progressStep;
                                    $current = $progressStep === $index;
                                    $active = $completed || $current;
                                @endphp
                                <li class="relative min-w-0 sm:pb-0">
                                    @if (!$loop->last)
                                        <span class="absolute left-8 right-[-1rem] top-4 hidden h-px bg-[#e8e4de] sm:block {{ $completed ? 'bg-[#23a052]' : '' }}"></span>
                                    @endif
                                    <div class="relative flex items-center gap-3 sm:flex-col sm:items-start sm:gap-2">
                                        <span class="grid size-8 shrink-0 place-items-center rounded-full border text-xs font-bold {{ $active ? 'border-[#23a052] bg-[#23a052] text-white' : 'border-[#e2ddd6] bg-white text-[#a3a3a3]' }}">
                                            @if ($completed)
                                                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6"/></svg>
                                            @else
                                                {{ $index + 1 }}
                                            @endif
                                        </span>
                                        <div>
                                            <p class="text-sm font-semibold {{ $active ? 'text-[#171717]' : 'text-[#999]' }}">{{ $stage['label'] }}</p>
                                            @if ($current)
                                                <p class="mt-0.5 text-xs text-[#23a052]">Hiện tại</p>
                                            @endif
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                        <div class="mt-6 flex flex-wrap gap-x-6 gap-y-2 border-t border-[#f0ede8] pt-4 text-xs text-[#777]">
                            <span>Ngày đặt: {{ $order->created_at?->format('d/m/Y H:i') }}</span>
                            @if ($order->user_received_at)
                                <span>Đã xác nhận nhận hàng: {{ \Illuminate\Support\Carbon::parse($order->user_received_at)->format('d/m/Y H:i') }}</span>
                            @endif
                        </div>
                    @endif
                </section>

                <section class="rounded-[28px] border border-[#ece8e2] bg-white p-5 shadow-[0_10px_35px_rgba(0,0,0,.04)] sm:p-6">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-bold text-[#171717]">Sản phẩm trong đơn</h2>
                            <p class="mt-1 text-sm text-[#7f7f7f]">{{ $order->items->sum('quantity') }} sản phẩm</p>
                        </div>
                    </div>

                    <div class="mt-5 divide-y divide-[#eeeae4]">
                        @forelse ($order->items as $item)
                            @php
                                $thumbnail = $item->product_thumbnail;
                                $thumbnailUrl = ! $thumbnail
                                    ? asset('images/placeholder.svg')
                                    : (str_starts_with($thumbnail, 'http')
                                        ? $thumbnail
                                        : (str_starts_with($thumbnail, 'images/') || str_starts_with($thumbnail, 'storage/')
                                            ? asset($thumbnail)
                                            : asset('storage/' . $thumbnail)));
                            @endphp
                            <article class="flex flex-col gap-4 py-5 first:pt-0 sm:flex-row sm:items-center">
                                <div class="flex min-w-0 flex-1 items-center gap-4">
                                    <div class="flex size-20 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-[#eee9e2] bg-[#faf9f7] p-1">
                                        <img src="{{ $thumbnailUrl }}" alt="{{ $item->product_name }}" loading="lazy" decoding="async" class="size-full object-contain">
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="line-clamp-2 text-sm font-semibold text-[#171717]">{{ $item->product_name }}</h3>
                                        @if ($item->variant_name)
                                            <p class="mt-1 text-xs text-[#777]">{{ $item->variant_name }}</p>
                                        @endif
                                        <p class="mt-2 text-sm text-[#555]">{{ number_format($item->price, 0, ',', '.') }}₫ × {{ $item->quantity }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between gap-4 sm:block sm:w-40 sm:text-right">
                                    <p class="text-sm font-bold text-[#171717]">{{ number_format($item->subtotal, 0, ',', '.') }}₫</p>
                                    @if ($item->product)
                                        <a href="{{ route('products.show', $item->product) }}" class="mt-2 inline-flex text-xs font-semibold text-[#1677d2] transition hover:text-[#095ca8]">Xem sản phẩm</a>
                                    @endif
                                    @if ($item->product && $order->status === 'delivered' && $order->payment_status === 'paid')
                                        @if ($order->reviews->contains('product_id', $item->product_id))
                                            <p class="mt-2 text-xs font-semibold text-[#23a052]">Đã đánh giá</p>
                                        @else
                                            <a href="{{ route('products.show', ['product' => $item->product, 'order' => $order->id]) }}#reviews" class="mt-2 inline-flex text-xs font-semibold text-[#1677d2] transition hover:text-[#095ca8]">Đánh giá sản phẩm</a>
                                        @endif
                                    @endif
                                </div>
                            </article>
                        @empty
                            <p class="py-8 text-center text-sm text-[#8b8b8b]">Đơn hàng chưa có sản phẩm.</p>
                        @endforelse
                    </div>
                </section>

                @if ($order->note)
                    <section class="rounded-[28px] border border-[#ece8e2] bg-white p-5 shadow-[0_10px_35px_rgba(0,0,0,.04)] sm:p-6">
                        <h2 class="text-lg font-bold text-[#171717]">Ghi chú đơn hàng</h2>
                        <p class="mt-3 whitespace-pre-line text-sm leading-6 text-[#666]">{{ $order->note }}</p>
                    </section>
                @endif
            </div>

            <aside class="space-y-5 xl:sticky xl:top-5 xl:self-start">
                <section class="rounded-[28px] border border-[#ece8e2] bg-white p-5 shadow-[0_10px_35px_rgba(0,0,0,.04)]">
                    <h2 class="text-base font-bold text-[#171717]">Địa chỉ giao hàng</h2>
                    <div class="mt-4 rounded-[18px] bg-[#fbfaf8] p-4 text-sm leading-6 text-[#5f5f5f]">
                        <p class="font-semibold text-[#171717]">{{ $order->shipping_full_name }}</p>
                        @if ($order->shipping_phone)
                            <p class="mt-1">{{ $order->shipping_phone }}</p>
                        @endif
                        <p class="mt-2">{{ $order->shipping_address }}</p>
                        @if ($order->shipping_ward)
                            <p>{{ $order->shipping_ward }}</p>
                        @endif
                        @if ($order->shipping_district || $order->shipping_province)
                            <p>{{ collect([$order->shipping_district, $order->shipping_province])->filter()->join(', ') }}</p>
                        @endif
                    </div>
                </section>

                <section class="rounded-[28px] border border-[#ece8e2] bg-white p-5 shadow-[0_10px_35px_rgba(0,0,0,.04)]">
                    <h2 class="text-base font-bold text-[#171717]">Thanh toán</h2>
                    @if ($isAwaitingPayment)
                        <div data-payment-countdown data-payment-deadline="{{ $paymentDeadline?->timestamp }}" class="mt-4 rounded-[18px] border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                            <p class="font-semibold">Đơn hàng chưa thanh toán</p>
                            <p class="mt-1 leading-6">Còn <strong data-payment-countdown-value>{{ gmdate('H:i:s', $remainingPaymentSeconds) }}</strong> để thanh toán. Đơn sẽ tự động hủy khi hết thời gian.</p>
                            <p data-payment-countdown-expired class="mt-1 hidden font-semibold text-red-700">Đã hết thời gian thanh toán. Vui lòng tải lại trang.</p>
                        </div>
                    @endif
                    <dl class="mt-4 space-y-3 text-sm">
                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-[#777]">Phương thức</dt>
                            <dd class="max-w-[190px] text-right font-semibold text-[#222]">{{ $paymentMethods[$order->payment_method] ?? ucfirst($order->payment_method) }}</dd>
                        </div>
                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-[#777]">Trạng thái</dt>
                            <dd class="text-right font-semibold {{ $order->payment_status === 'paid' ? 'text-[#23a052]' : 'text-[#222]' }}">{{ $paymentStatuses[$order->payment_status] ?? ucfirst($order->payment_status) }}</dd>
                        </div>
                    </dl>
                </section>

                <section class="rounded-[28px] border border-[#ece8e2] bg-white p-5 shadow-[0_10px_35px_rgba(0,0,0,.04)]">
                    <h2 class="text-base font-bold text-[#171717]">Tóm tắt thanh toán</h2>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-[#777]">Tạm tính</dt>
                            <dd class="font-semibold text-[#222]">{{ number_format($order->subtotal, 0, ',', '.') }}₫</dd>
                        </div>
                        @if ($order->coupon_code)
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-[#777]">Mã giảm giá</dt>
                                <dd class="font-semibold text-[#222]">{{ $order->coupon_code }}</dd>
                            </div>
                        @endif
                        @if ($order->discount_amount > 0)
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-[#777]">Giảm giá</dt>
                                <dd class="font-semibold text-[#d14b4b]">-{{ number_format($order->discount_amount, 0, ',', '.') }}₫</dd>
                            </div>
                        @endif
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-[#777]">Phí vận chuyển</dt>
                            <dd class="font-semibold text-[#222]">{{ $order->shipping_fee > 0 ? number_format($order->shipping_fee, 0, ',', '.') . '₫' : 'Miễn phí' }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 border-t border-[#eeeae4] pt-4 text-base">
                            <dt class="font-bold text-[#171717]">Tổng tiền</dt>
                            <dd class="font-bold text-[#171717]">{{ number_format($order->total_amount, 0, ',', '.') }}₫</dd>
                        </div>
                    </dl>
                </section>
            </aside>
        </div>
    </div>
@endsection

@if ($isAwaitingPayment)
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const countdown = document.querySelector('[data-payment-countdown]');
                if (!countdown) return;

                const value = countdown.querySelector('[data-payment-countdown-value]');
                const expiredMessage = countdown.querySelector('[data-payment-countdown-expired]');
                const continueButton = document.querySelector('[data-payment-continue]');
                const deadline = Number(countdown.dataset.paymentDeadline || 0);

                const render = () => {
                    const remaining = Math.max(0, deadline - Math.floor(Date.now() / 1000));
                    if (remaining > 0) {
                        const hours = String(Math.floor(remaining / 3600)).padStart(2, '0');
                        const minutes = String(Math.floor((remaining % 3600) / 60)).padStart(2, '0');
                        const seconds = String(remaining % 60).padStart(2, '0');
                        if (value) value.textContent = `${hours}:${minutes}:${seconds}`;
                        return;
                    }

                    value?.classList.add('hidden');
                    expiredMessage?.classList.remove('hidden');
                    countdown.classList.remove('border-amber-200', 'bg-amber-50');
                    countdown.classList.add('border-red-200', 'bg-red-50');
                    if (continueButton?.dataset.paymentExpired === 'true') return;
                    continueButton?.setAttribute('data-payment-expired', 'true');
                    continueButton?.classList.add('pointer-events-none', 'opacity-50');
                    continueButton?.setAttribute('aria-disabled', 'true');
                    continueButton?.addEventListener('click', (event) => event.preventDefault(), { once: true });
                };

                render();
                window.setInterval(render, 1000);
            });
        </script>
    @endpush
@endif
