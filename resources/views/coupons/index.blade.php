@extends('layouts.app')

@section('title', 'Kho Voucher | NovaPhone')

@section('content')
<div class="bg-night text-gray-100 min-h-[calc(100vh-68px-340px)] py-8">
    <div class="mx-auto max-w-7xl px-4 sm:px-6">
        
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
            <a href="{{ route('home') }}" class="transition-colors hover:text-brand-400">Trang chủ</a>
            <span>/</span>
            <span class="font-medium text-gray-300">Kho Voucher</span>
        </nav>

        <h1 class="text-3xl font-black text-white tracking-tight mb-2">Kho Voucher</h1>
        <p class="text-gray-400 mb-8">Lưu ngay các mã giảm giá hấp dẫn để sử dụng khi thanh toán.</p>

        @if ($coupons->isEmpty())
            <div class="rounded-3xl border border-white/5 bg-night-soft p-12 text-center shadow-xl shadow-black/30">
                <div class="mx-auto flex size-16 items-center justify-center rounded-2xl bg-white/5 text-gray-400 mb-4">
                    <svg class="size-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" /></svg>
                </div>
                <h2 class="text-xl font-bold text-white mb-2">Hiện chưa có mã giảm giá nào</h2>
                <p class="text-gray-400 max-w-md mx-auto">Vui lòng quay lại sau để nhận các ưu đãi mới nhất từ NovaPhone.</p>
            </div>
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($coupons as $coupon)
                    <div class="relative flex flex-col justify-between overflow-hidden rounded-2xl border border-white/10 bg-night-soft shadow-lg transition-all hover:border-brand-500/30 hover:shadow-brand-500/10">
                        {{-- Gờ cắt coupon --}}
                        <div class="absolute -left-3 top-1/2 h-6 w-6 -translate-y-1/2 rounded-full bg-night"></div>
                        <div class="absolute -right-3 top-1/2 h-6 w-6 -translate-y-1/2 rounded-full bg-night"></div>
                        
                        <div class="flex-1 p-6 border-b border-dashed border-white/10 relative">
                            <div class="flex items-start justify-between gap-4 mb-4">
                                <div class="rounded-xl bg-brand-500/10 p-3 text-brand-400">
                                    <svg class="size-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" /></svg>
                                </div>
                                <div class="text-right">
                                    <span class="inline-block rounded bg-brand-600 px-2.5 py-1 text-xs font-black tracking-wider text-white">
                                        {{ $coupon->code }}
                                    </span>
                                </div>
                            </div>
                            
                            <h3 class="text-lg font-bold text-white">
                                @if ($coupon->type === 'fixed')
                                    Giảm {{ number_format($coupon->value, 0, ',', '.') }}đ
                                @else
                                    Giảm {{ floatval($coupon->value) }}%
                                @endif
                            </h3>
                            
                            @if ($coupon->description)
                                <p class="text-sm text-gray-400 mt-2">{{ $coupon->description }}</p>
                            @endif
                            
                            <ul class="text-xs text-gray-500 mt-4 space-y-1.5">
                                @if ($coupon->min_order_amount > 0)
                                    <li>• Đơn tối thiểu: {{ number_format($coupon->min_order_amount, 0, ',', '.') }}đ</li>
                                @endif
                                @if ($coupon->type === 'percent' && $coupon->max_discount > 0)
                                    <li>• Giảm tối đa: {{ number_format($coupon->max_discount, 0, ',', '.') }}đ</li>
                                @endif
                                @if ($coupon->expires_at)
                                    <li>• HSD: {{ $coupon->expires_at->format('d/m/Y H:i') }}</li>
                                @endif
                            </ul>
                        </div>
                        
                        <div class="p-4 bg-white/[0.02]">
                            @if (in_array($coupon->id, $savedCouponIds))
                                <button disabled class="w-full rounded-xl bg-white/5 py-3 text-sm font-bold text-gray-400 cursor-not-allowed border border-white/5">
                                    Đã lưu
                                </button>
                            @else
                                <button type="button" onclick="saveCoupon({{ $coupon->id }}, this)" class="w-full rounded-xl bg-brand-600 py-3 text-sm font-bold text-white hover:bg-brand-500 transition shadow-lg shadow-brand-600/20">
                                    Lưu mã
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $coupons->links() }}
            </div>
        @endif
        
    </div>
</div>

{{-- Popup Alert / Toast --}}
<div id="toast-container" class="fixed bottom-5 right-5 z-50 flex flex-col gap-3"></div>

@push('scripts')
<script>
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `flex items-center gap-3 rounded-2xl border px-5 py-3.5 shadow-2xl backdrop-blur-xl transition duration-300 transform translate-y-5 opacity-0 ${
            type === 'success' 
                ? 'border-emerald-500/20 bg-emerald-950/80 text-emerald-300' 
                : 'border-red-500/20 bg-red-950/80 text-red-300'
        }`;
        
        const icon = type === 'success' 
            ? `<svg class="size-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>`
            : `<svg class="size-5 text-red-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>`;
            
        toast.innerHTML = `${icon}<span class="text-sm font-semibold">${message}</span>`;
        document.getElementById('toast-container').appendChild(toast);
        
        setTimeout(() => toast.classList.remove('translate-y-5', 'opacity-0'), 10);
        
        setTimeout(() => {
            toast.classList.add('translate-y-5', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    function saveCoupon(couponId, buttonEl) {
        fetch(`/coupons/${couponId}/save`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(async response => {
            const data = await response.json();
            if (response.ok || data.success) {
                showToast(data.message || 'Đã lưu mã giảm giá thành công!');
                // Đổi trạng thái button
                buttonEl.textContent = 'Đã lưu';
                buttonEl.disabled = true;
                buttonEl.className = 'w-full rounded-xl bg-white/5 py-3 text-sm font-bold text-gray-400 cursor-not-allowed border border-white/5';
                buttonEl.removeAttribute('onclick');
            } else {
                if (response.status === 401) {
                    showToast('Vui lòng đăng nhập để lưu mã.', 'error');
                } else {
                    showToast(data.message || 'Có lỗi xảy ra', 'error');
                }
            }
        })
        .catch(err => {
            console.error(err);
            showToast('Không thể kết nối đến máy chủ.', 'error');
        });
    }
</script>
@endpush
@endsection
