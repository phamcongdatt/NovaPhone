@extends('layouts.app')

@php
    $isAwaitingPayment = $order->payment_method === 'vnpay'
        && $order->status === 'pending'
        && $order->payment_status === 'pending';
    $isCancelled = $order->status === 'cancelled';
    $paymentDeadline = $order->created_at?->copy()->addMinutes((int) config('shop.pending_order_timeout_minutes'));
    $remainingPaymentSeconds = $isAwaitingPayment && $paymentDeadline
        ? max(0, now()->diffInSeconds($paymentDeadline, false))
        : 0;
@endphp

@section('title', $isCancelled ? 'Đơn hàng đã hủy - NovaPhone' : ($isAwaitingPayment ? 'Chờ thanh toán - NovaPhone' : 'Đặt hàng thành công - NovaPhone'))

@section('content')
<div class="mx-auto max-w-3xl space-y-3">
    <div class="flex">
        <span class="rounded-full border px-3 py-1 text-[11px] font-semibold shadow-sm {{ $isCancelled ? 'border-red-200 bg-red-50 text-red-700' : ($isAwaitingPayment ? 'border-amber-200 bg-amber-50 text-amber-800' : 'border-[#e8e4de] bg-white text-[#444]') }}">
            {{ $isCancelled ? 'Đơn hàng đã hủy' : ($isAwaitingPayment ? 'Đang chờ thanh toán' : 'Đặt hàng thành công') }}
        </span>
    </div>

    <section class="rounded-[28px] border bg-white p-6 text-center shadow-[0_10px_35px_rgba(0,0,0,.04)] sm:p-10 {{ $isCancelled ? 'border-red-100' : ($isAwaitingPayment ? 'border-amber-100' : 'border-[#ece8e2]') }}">
        <div class="mx-auto flex size-20 items-center justify-center rounded-full border-4 text-2xl font-bold {{ $isCancelled ? 'border-red-100 text-red-500' : ($isAwaitingPayment ? 'border-amber-100 text-amber-500' : 'border-[#dff2df] text-[#30a24a]') }}">
            @if ($isCancelled)
                ×
            @elseif ($isAwaitingPayment)
                <svg class="size-9" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="12" cy="12" r="8.5" />
                    <path stroke-linecap="round" d="M12 7.5v5l3.25 2" />
                </svg>
            @else
                ✓
            @endif
        </div>

        <h1 class="mt-5 text-2xl font-bold text-[#171717]">
            {{ $isCancelled ? 'Đơn hàng đã bị hủy' : ($isAwaitingPayment ? 'Đơn hàng đã được tạo' : 'Đặt hàng thành công!') }}
        </h1>
        <p class="mx-auto mt-2 max-w-xl text-sm leading-6 text-[#7f7f7f]">
            @if ($isCancelled)
                Đơn hàng <span class="font-semibold text-[#222]">#{{ $order->order_code }}</span> đã bị hủy vì quá thời gian thanh toán.
            @elseif ($isAwaitingPayment)
                Đơn hàng <span class="font-semibold text-[#222]">#{{ $order->order_code }}</span> đã được ghi nhận. Vui lòng hoàn tất thanh toán VNPay trong thời gian quy định để đơn hàng được xác nhận.
            @else
                Cảm ơn bạn đã mua hàng tại NovaPhone. Mã đơn hàng của bạn là <span class="font-semibold">#{{ $order->order_code }}</span>.
            @endif
        </p>

        @if ($isAwaitingPayment)
            <div
                data-payment-countdown
                data-payment-deadline="{{ $paymentDeadline?->timestamp }}"
                class="mx-auto mt-6 max-w-xl rounded-[20px] border border-amber-200 bg-amber-50 p-4 text-left"
            >
                <div class="flex items-start gap-3">
                    <svg class="mt-0.5 size-5 shrink-0 text-amber-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l2.5 1.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <div class="min-w-0 flex-1">
                        <p class="font-semibold text-amber-900">Chưa thanh toán</p>
                        <p class="mt-1 text-sm leading-6 text-amber-800">Bạn còn <strong data-payment-countdown-value>{{ gmdate('H:i:s', $remainingPaymentSeconds) }}</strong> để thanh toán. Sau thời gian này, đơn hàng sẽ tự động bị hủy.</p>
                        <p data-payment-countdown-expired class="mt-1 hidden text-sm font-semibold text-red-700">Đã hết thời gian thanh toán. Đơn hàng sẽ được cập nhật về trạng thái hủy.</p>
                    </div>
                </div>
            </div>
        @elseif ($isCancelled)
            @php
                $cancelledByLabel = $order->cancelledBy?->name
                    ?? (str_starts_with((string) $order->cancelled_reason, 'Tự động hủy') ? 'Hệ thống' : 'Khách hàng');
            @endphp
            <div class="mx-auto mt-6 max-w-xl rounded-[20px] border border-red-100 bg-red-50 p-4 text-left text-sm leading-6 text-red-800">
                <p class="font-semibold">Đơn hàng không còn hiệu lực.</p>
                @if ($order->cancelled_reason)
                    <p class="mt-1"><span class="font-semibold">Lý do hủy:</span> {{ $order->cancelled_reason }}</p>
                @endif
                <p class="mt-1"><span class="font-semibold">Hủy bởi:</span> {{ $cancelledByLabel }}</p>
            </div>
        @endif

        <div class="mx-auto mt-6 max-w-sm space-y-4">
            <div class="rounded-[16px] border border-[#ece8e2] bg-[#fbfaf8] p-4 text-left">
                <p class="text-xs text-[#8b8b8b]">Tổng tiền</p>
                <p class="text-2xl font-bold text-[#111]">{{ number_format($order->total_amount, 0, ',', '.') }}₫</p>
                <p class="text-xs text-[#8b8b8b]">Thời gian: {{ $order->created_at?->format('d/m/Y H:i') }}</p>
            </div>

            <div class="rounded-[16px] border border-[#ece8e2] bg-[#fbfaf8] p-4 text-left">
                <p class="text-xs text-[#8b8b8b]">Phương thức thanh toán</p>
                @php
                    $paymentMethodMap = [
                        'cod' => 'Thanh toán khi nhận hàng (COD)',
                        'vnpay' => 'Thẻ tín dụng / VNPay',
                    ];
                @endphp
                <div class="mt-1 flex items-center justify-between gap-3">
                    <p class="font-semibold text-[#111]">{{ $paymentMethodMap[$order->payment_method] ?? $order->payment_method }}</p>
                    <span class="shrink-0 rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $order->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-700' : ($isCancelled ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-800') }}">
                        {{ $order->payment_status === 'paid' ? 'Đã thanh toán' : ($isCancelled ? 'Đã hủy' : 'Chưa thanh toán') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="mt-6 flex flex-col justify-center gap-3 sm:flex-row">
            @if ($isAwaitingPayment)
                <a data-payment-continue href="{{ $guestPaymentUrl ?? route('checkout.vnpay.create', $order) }}" class="rounded-full bg-[#0a84ff] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#006edb]">
                    Tiếp tục thanh toán VNPay
                </a>
            @endif
            <a href="{{ $guestOrderUrl ?? route('orders.show', $order) }}" class="rounded-full bg-black px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#222]">
                Xem chi tiết đơn hàng
            </a>
            <a href="{{ route('home') }}" class="rounded-full border border-[#e8e4de] px-6 py-3 text-sm font-semibold text-[#111] transition hover:bg-[#fbfaf8]">
                Tiếp tục mua sắm
            </a>
        </div>
        @if ($guestOrderUrl ?? false)
            <p class="mt-4 text-xs text-[#777]">Link quản lý đơn đã được gửi tới email {{ $order->customer_email }}.</p>
        @endif
    </section>
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

                    if (value) value.classList.add('hidden');
                    if (expiredMessage) expiredMessage.classList.remove('hidden');
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
