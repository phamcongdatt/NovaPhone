@extends('layouts.app')
@section('title', 'Ưu đãi chung - NovaPhone')

@section('content')
<div class="space-y-3">
    <div class="flex">
        <span class="rounded-full border border-[#e8e4de] bg-white px-3 py-1 text-[11px] font-semibold text-[#444] shadow-sm">Ưu đãi chung</span>
    </div>
    <section class="rounded-[28px] border border-[#ece8e2] bg-white p-5 shadow-[0_10px_35px_rgba(0,0,0,.04)]">
        <h1 class="text-2xl font-bold">Ưu đãi chung</h1>
        <p class="mt-1 text-sm text-[#7f7f7f]">Khám phá các mã giảm giá đang áp dụng cho mọi khách hàng và lưu mã phù hợp vào tài khoản.</p>
        <div class="mt-5 space-y-3">
            @forelse ($coupons as $coupon)
                <div class="flex flex-col gap-4 rounded-[22px] border border-[#ece8e2] p-4 sm:flex-row sm:items-center">
                    <div class="w-full rounded-[18px] bg-gradient-to-r from-[#171717] to-[#4b4b4b] p-4 text-white sm:w-40">
                        <div class="text-lg font-bold">
                            {{ $coupon->type === 'percent' ? 'Giảm ' . rtrim(rtrim((string) $coupon->value, '0'), '.') . '%' : 'Giảm ' . number_format($coupon->value, 0, ',', '.') . 'đ' }}
                        </div>
                        @if ($coupon->max_discount)
                            <div class="mt-1 text-xs opacity-90">Tối đa {{ number_format($coupon->max_discount, 0, ',', '.') }}đ</div>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="text-lg font-bold">{{ $coupon->code }}</div>
                        @if ($coupon->description)
                            <div class="mt-1 text-sm text-[#7f7f7f]">{{ $coupon->description }}</div>
                        @endif
                        <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-[#7f7f7f]">
                            @if ($coupon->min_order_amount > 0)
                                <span>Đơn tối thiểu {{ number_format($coupon->min_order_amount, 0, ',', '.') }}đ</span>
                            @endif
                            <span>
                                {{ $coupon->expires_at ? 'HSD: ' . $coupon->expires_at->format('d/m/Y') : 'Không giới hạn thời gian' }}
                            </span>
                        </div>
                    </div>
                    @php($isSaved = in_array($coupon->id, $savedCouponIds, true))
                    <button
                        type="button"
                        class="shrink-0 rounded-full px-4 py-2 text-sm font-semibold transition {{ $isSaved ? 'border border-[#e8e4de] text-[#777]' : 'bg-black text-white hover:bg-[#222]' }}"
                        data-coupon-save
                        data-save-url="{{ route('coupons.save', $coupon) }}"
                        data-login-url="{{ route('login') }}"
                        data-saved="{{ $isSaved ? 'true' : 'false' }}"
                        {{ $isSaved ? 'disabled' : '' }}
                    >
                        {{ $isSaved ? 'Đã lưu' : 'Lưu mã' }}
                    </button>
                </div>
            @empty
                <div class="rounded-2xl border-2 border-dashed border-[#e5e1db] p-10 text-center text-sm text-[#8b8b8b]">Hiện chưa có mã giảm giá khả dụng.</div>
            @endforelse
        </div>
        @if ($coupons->hasPages())
            <div class="mt-6">{{ $coupons->links() }}</div>
        @endif
    </section>
</div>
@endsection
