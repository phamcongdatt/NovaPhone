@extends('layouts.app')

@section('title', 'Cổng thanh toán giả lập | NovaPhone')

@section('content')
@php
    $money = fn ($value) => number_format((float) $value, 0, ',', '.').'đ';
    $methodName = $order->payment_method === 'momo' ? 'Momo' : 'VNPay';
    $qrData = "NovaPhone-Code-{$order->order_code}-Amount-{$order->total_amount}";
    $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&color=0a0c12&bgcolor=ffffff&data=" . urlencode($qrData);
@endphp

<div class="hero-glow flex min-h-[calc(100vh-68px-340px)] items-center justify-center px-4 py-16 sm:px-6">
    <div class="w-full max-w-xl overflow-hidden rounded-3xl border border-white/10 bg-night-soft/60 p-6 sm:p-8 shadow-2xl shadow-black/50 backdrop-blur-2xl text-center">
        
        {{-- Logo cổng thanh toán --}}
        <div class="flex items-center justify-center gap-2 mb-6">
            @if ($order->payment_method === 'momo')
                <span class="inline-flex size-10 items-center justify-center rounded-xl bg-pink-500 text-white font-extrabold text-sm shadow-md shadow-pink-500/20">Mo</span>
            @else
                <span class="inline-flex size-10 items-center justify-center rounded-xl bg-sky-600 text-white font-extrabold text-xs shadow-md shadow-sky-600/20">VN</span>
            @endif
            <h2 class="text-xl font-black text-white">Cổng thanh toán giả lập {{ $methodName }}</h2>
        </div>

        {{-- Cảnh báo giả lập --}}
        <div class="mb-6 rounded-xl border border-amber-500/20 bg-amber-500/10 p-4 text-left text-xs text-amber-300 leading-relaxed">
            <strong>[LƯU Ý THỬ NGHIỆM]:</strong> Đây là cổng thanh toán giả lập tích hợp cho Đồ án tốt nghiệp NovaPhone. Vui lòng KHÔNG chuyển tiền thật. Bạn chỉ cần nhấn nút "Xác nhận đã chuyển khoản" để hoàn thành đơn hàng.
        </div>

        <div class="grid gap-6 sm:grid-cols-2 items-center">
            
            {{-- QR Code --}}
            <div class="flex flex-col items-center justify-center border border-white/5 rounded-2xl bg-white p-4 shadow-inner">
                <img src="{{ $qrUrl }}" alt="Mã QR Thanh Toán" class="size-48 object-contain">
                <p class="text-[10px] text-gray-400 mt-3 font-semibold uppercase tracking-wider">Quét mã để thanh toán</p>
            </div>

            {{-- Thông tin giao dịch --}}
            <div class="text-left space-y-4">
                <div>
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-500">Mã đơn hàng</span>
                    <span class="text-base font-extrabold text-white">{{ $order->order_code }}</span>
                </div>
                <div>
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-500">Số tiền cần thanh toán</span>
                    <span class="text-xl font-black text-brand-400">{{ $money($order->total_amount) }}</span>
                </div>
                <div>
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-500">Nội dung chuyển khoản</span>
                    <code class="block rounded-lg bg-white/5 px-3 py-1.5 text-xs text-white border border-white/10 mt-1 font-mono select-all">{{ $order->order_code }}</code>
                </div>
                <div>
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-500">Thời gian còn lại</span>
                    <span id="countdown" class="text-base font-extrabold text-red-400">05:00</span>
                </div>
            </div>

        </div>

        {{-- Actions --}}
        <div class="mt-8 border-t border-white/10 pt-6 flex flex-col sm:flex-row gap-3">
            <form method="POST" action="{{ route('checkout.payment-process', $order) }}" class="flex-1">
                @csrf
                <button 
                    type="submit" 
                    class="w-full flex items-center justify-center rounded-xl bg-gradient-to-r from-brand-600 to-brand-500 py-3.5 text-sm font-bold text-white shadow-lg shadow-brand-600/25 transition hover:-translate-y-0.5"
                >
                    Xác nhận đã chuyển khoản
                </button>
            </form>
            <a 
                href="{{ route('home') }}" 
                class="flex items-center justify-center rounded-xl border border-white/10 bg-white/5 px-6 py-3.5 text-sm font-bold text-gray-400 transition hover:bg-white/10 hover:text-white"
            >
                Hủy giao dịch
            </a>
        </div>

    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        let time = 300; // 5 phút = 300 giây
        const countdownEl = document.getElementById('countdown');

        const timer = setInterval(() => {
            const minutes = Math.floor(time / 60);
            let seconds = time % 60;
            seconds = seconds < 10 ? '0' + seconds : seconds;

            countdownEl.textContent = `0${minutes}:${seconds}`;

            if (time <= 0) {
                clearInterval(timer);
                alert('Hết thời gian giao dịch! Đang chuyển hướng về trang chủ.');
                window.location.href = "{{ route('home') }}";
            }
            time--;
        }, 1000);
    });
</script>
@endpush
@endsection
