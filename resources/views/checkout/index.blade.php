@extends('layouts.app')

@section('title', 'Thanh toán - NovaPhone')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#f8f6f2] to-white">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav class="mb-8 flex items-center gap-2 text-sm text-[#8b8b8b]">
            <a href="{{ route('home') }}" class="transition hover:text-black">Trang chủ</a>
            <span class="text-[#ddd]">/</span>
            <a href="{{ route('cart.index') }}" class="transition hover:text-black">Giỏ hàng</a>
            <span class="text-[#ddd]">/</span>
            <span class="font-semibold text-black">Thanh toán</span>
        </nav>

        {{-- Progress Steps --}}
        <div class="mb-10 flex items-center justify-between">
            @foreach ([
                ['num' => '1', 'label' => 'Địa chỉ', 'active' => true],
                ['num' => '2', 'label' => 'Thanh toán', 'active' => false],
            ] as $index => $step)
                <div class="flex flex-1 items-center">
                    <div class="flex size-12 flex-shrink-0 items-center justify-center rounded-full {{ $step['active'] ? 'bg-black text-white shadow-lg' : 'border-2 border-[#e0e0e0] bg-white text-[#8b8b8b]' }} text-sm font-bold transition-all duration-300">
                        {{ $step['num'] }}
                    </div>
                    <span class="ml-3 hidden text-sm font-semibold text-[#111] sm:inline">{{ $step['label'] }}</span>
                    @if ($index < 2)
                        <div class="ml-auto h-1 flex-1 rounded-full {{ $step['active'] ? 'bg-black' : 'bg-[#e0e0e0]' }}"></div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Coupon forms are kept outside the order form so the HTML never nests forms. --}}
        <form id="checkout-coupon-manual-form" method="POST" action="{{ route('checkout.apply-coupon') }}" class="hidden">
            @csrf
        </form>
        @foreach ($walletCoupons as $coupon)
            <form id="checkout-wallet-coupon-{{ $coupon->id }}" method="POST" action="{{ route('checkout.apply-coupon') }}" class="hidden">
                @csrf
                <input type="hidden" name="code" value="{{ $coupon->code }}">
            </form>
        @endforeach
        @foreach ($appliedCouponsData as $applied)
            <form id="checkout-remove-coupon-{{ $applied['coupon']->id }}" method="POST" action="{{ route('checkout.remove-coupon') }}" class="hidden">
                @csrf
                <input type="hidden" name="code" value="{{ $applied['coupon']->code }}">
            </form>
        @endforeach

        {{-- Main Content --}}
        <form id="checkoutForm" method="POST" action="{{ route('checkout.place-order') }}" class="grid gap-6 lg:grid-cols-[1fr_420px]">
            @csrf

            {{-- Left Column --}}
            <div class="space-y-6">
                {{-- Shipping Address --}}
                <section class="overflow-hidden rounded-2xl border border-[#e0e0e0] bg-white shadow-sm transition-shadow hover:shadow-md">
                    <div class="border-b border-[#e0e0e0] bg-gradient-to-r from-[#f8f6f2] to-white px-6 py-5">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-black text-sm font-bold text-white">1</div>
                                <h2 class="text-base font-bold text-[#111]">Địa chỉ giao hàng</h2>
                            </div>
                            @if (Auth::check())
                                <a href="{{ route('account.addresses') }}" class="text-xs font-semibold text-blue-600 transition hover:text-blue-800">+ Quản lý</a>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-3 p-6">
                        @if ($defaultAddress)
                            <label class="group flex cursor-pointer gap-4 rounded-xl border-2 border-black bg-[#f8f6f2] p-4 transition-all duration-200">
                                <input type="radio" name="shipping_address_id" value="{{ $defaultAddress->id }}" data-full-name="{{ $defaultAddress->full_name }}" data-phone="{{ $defaultAddress->phone }}" data-address="{{ $defaultAddress->address }}" data-province-code="{{ $defaultAddress->province_code }}" data-ward-code="{{ $defaultAddress->ward_code }}" checked class="mt-1 size-5 cursor-pointer accent-black">
                                <div class="flex-1">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <div class="font-semibold text-[#111]">{{ $defaultAddress->full_name }}</div>
                                            <div class="mt-2 text-sm text-[#666]">
                                                <p class="leading-relaxed">{{ $defaultAddress->full_address }}</p>
                                                @if (! $defaultAddress->province_code || ! $defaultAddress->ward_code)
                                                    <p class="mt-1 text-xs font-medium text-amber-700">Địa chỉ cũ: vui lòng chọn lại tỉnh/thành và phường/xã trước khi đặt hàng.</p>
                                                @endif
                                                <p class="mt-1">{{ $defaultAddress->phone }}</p>
                                            </div>
                                        </div>
                                        <span class="rounded-full bg-black px-3 py-1 text-[10px] font-bold text-white">Mặc định</span>
                                    </div>
                                </div>
                            </label>
                        @else
                            <div class="rounded-xl border-2 border-dashed border-[#e0e0e0] p-8 text-center">
                                <svg class="mx-auto h-12 w-12 text-[#ccc]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <p class="mt-3 text-sm text-[#8b8b8b]">Chưa có địa chỉ giao hàng</p>
                                @if (Auth::check())
                                    <a href="{{ route('account.addresses') }}" class="mt-4 inline-block rounded-lg bg-black px-4 py-2 text-xs font-semibold text-white transition hover:bg-[#222]">Thêm địa chỉ ngay</a>
                                @else
                                    <p class="mt-2 text-xs text-[#8b8b8b]">Bạn có thể nhập địa chỉ trực tiếp ở phần thông tin giao hàng bên dưới.</p>
                                @endif
                            </div>
                        @endif

                        <div class="rounded-2xl border border-[#e0e0e0] bg-white p-5 shadow-sm">
                            <div class="space-y-4">
                                <div>
                                    <label class="text-xs font-semibold text-[#8b8b8b]">Tên người nhận</label>
                                    <input type="text" name="shipping_full_name" id="shipping_full_name" value="{{ old('shipping_full_name', $defaultAddress?->full_name) }}" placeholder="Nhập tên người nhận" class="mt-2 w-full rounded-[12px] border {{ $errors->has('shipping_full_name') ? 'border-red-500' : 'border-[#ece8e2]' }} bg-[#fbfaf8] px-4 py-2.5 text-sm outline-none transition focus:border-black" required>
                                    @error('shipping_full_name')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="text-xs font-semibold text-[#8b8b8b]">Số điện thoại</label>
                                    <input type="tel" name="shipping_phone" id="shipping_phone" value="{{ old('shipping_phone', $defaultAddress?->phone) }}" placeholder="Nhập số điện thoại" class="mt-2 w-full rounded-[12px] border {{ $errors->has('shipping_phone') ? 'border-red-500' : 'border-[#ece8e2]' }} bg-[#fbfaf8] px-4 py-2.5 text-sm outline-none transition focus:border-black" required>
                                    @error('shipping_phone')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-[#8b8b8b]">Email nhận thông báo</label>
                                    <input type="email" name="customer_email" id="customer_email" value="{{ old('customer_email', Auth::user()?->email) }}" placeholder="you@example.com" class="mt-2 w-full rounded-[12px] border {{ $errors->has('customer_email') ? 'border-red-500' : 'border-[#ece8e2]' }} bg-[#fbfaf8] px-4 py-2.5 text-sm outline-none transition focus:border-black" required>
                                    <p class="mt-2 text-xs text-[#8b8b8b]">Dùng để nhận xác nhận và link quản lý đơn hàng.</p>
                                    @error('customer_email')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <label class="text-xs font-semibold text-[#8b8b8b]">Tỉnh/Thành phố</label>
                                        <select name="province_code" id="province_code" data-selected="{{ old('province_code', $defaultAddress?->province_code) }}" class="mt-2 w-full rounded-[12px] border {{ $errors->has('province_code') ? 'border-red-500' : 'border-[#ece8e2]' }} bg-[#fbfaf8] px-4 py-2.5 text-sm outline-none transition focus:border-black" required>
                                            <option value="">Đang tải tỉnh/thành phố...</option>
                                        </select>
                                        @error('province_code')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold text-[#8b8b8b]">Phường/Xã</label>
                                        <select name="ward_code" id="ward_code" data-selected="{{ old('ward_code', $defaultAddress?->ward_code) }}" class="mt-2 w-full rounded-[12px] border {{ $errors->has('ward_code') ? 'border-red-500' : 'border-[#ece8e2]' }} bg-[#fbfaf8] px-4 py-2.5 text-sm outline-none transition focus:border-black disabled:cursor-not-allowed disabled:opacity-60" required disabled>
                                            <option value="">Chọn tỉnh/thành phố trước</option>
                                        </select>
                                        @error('ward_code')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div>
                                    <label class="text-xs font-semibold text-[#8b8b8b]">Số nhà, tên đường / tòa nhà / thôn ấp</label>
                                    <div>
                                        <input type="text" name="shipping_address" id="shipping_address" value="{{ old('shipping_address', $defaultAddress?->address) }}" placeholder="Ví dụ: 12 Nguyễn Huệ, căn 08.12" class="mt-2 w-full rounded-[12px] border {{ $errors->has('shipping_address') ? 'border-red-500' : 'border-[#ece8e2]' }} bg-[#fbfaf8] px-4 py-2.5 text-sm outline-none transition focus:border-black" required>
                                        @error('shipping_address')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Payment Method --}}
                <section class="overflow-hidden rounded-2xl border border-[#e0e0e0] bg-white shadow-sm transition-shadow hover:shadow-md">
                    <div class="border-b border-[#e0e0e0] bg-gradient-to-r from-[#f8f6f2] to-white px-6 py-5">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-black text-sm font-bold text-white">2</div>
                            <h2 class="text-base font-bold text-[#111]">Phương thức thanh toán</h2>
                        </div>
                    </div>

                    <div class="space-y-3 p-6">
                        @foreach ([
                            ['id' => 'cod', 'name' => 'Thanh toán khi nhận hàng', 'desc' => 'Trả tiền khi nhân viên giao hàng', 'enabled' => !$disableCod, 'icon' => ''],
                            ['id' => 'vnpay', 'name' => 'Thẻ tín dụng / VNPay', 'desc' => 'Visa, Mastercard, JCB...', 'enabled' => true, 'icon' => ''],
                        ] as $method)
                            <label class="flex cursor-pointer gap-4 rounded-xl border-2 {{ $method['id'] === $defaultPaymentMethod ? 'border-black bg-[#f8f6f2]' : 'border-[#e0e0e0] bg-white' }} p-4 transition-all duration-200 {{ !$method['enabled'] ? 'opacity-50' : 'hover:border-[#ccc] hover:bg-[#fbfaf8]' }}">
                                <div class="mt-1">
                                    <input type="radio" name="payment_method" value="{{ $method['id'] }}" {{ $method['id'] === $defaultPaymentMethod ? 'checked' : '' }} class="size-5 cursor-pointer accent-black" {{ !$method['enabled'] ? 'disabled' : '' }}>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-lg">{{ $method['icon'] }}</span>
                                                <div class="font-semibold text-[#111]">{{ $method['name'] }}</div>
                                            </div>
                                            <p class="mt-1 text-xs text-[#666]">{{ $method['desc'] }}</p>
                                            @if (!$method['enabled'] && $method['id'] === 'cod')
                                                <p class="mt-2 text-xs text-red-600">⚠️ Không hỗ trợ cho đơn > {{ number_format($codMaxAmount, 0, ',', '.') }}đ</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </section>

                {{-- Notes --}}
                <section class="overflow-hidden rounded-2xl border border-[#e0e0e0] bg-white shadow-sm">
                    <div class="border-b border-[#e0e0e0] bg-gradient-to-r from-[#f8f6f2] to-white px-6 py-5">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-black text-sm font-bold text-white">3</div>
                            <h2 class="text-base font-bold text-[#111]">Ghi chú</h2>
                        </div>
                    </div>
                    <div class="p-6">
                        <textarea
                            name="note"
                            placeholder="Thêm ghi chú về đơn hàng (không bắt buộc)..."
                            rows="4"
                            class="w-full rounded-xl border border-[#e0e0e0] bg-[#fbfaf8] px-4 py-3 text-sm outline-none transition focus:border-black focus:ring-2 focus:ring-black/10"
                        >{{ old('note') }}</textarea>
                    </div>
                </section>
            </div>

            {{-- Right Column - Order Summary --}}
            <aside class="h-fit rounded-2xl border border-[#e0e0e0] bg-white shadow-sm">
                <div class="border-b border-[#e0e0e0] bg-gradient-to-r from-[#f8f6f2] to-white px-6 py-5">
                    <h2 class="text-base font-bold text-[#111]">Tóm tắt đơn hàng</h2>
                </div>

                <div class="p-6">
                    {{-- Order Items --}}
                    <div class="space-y-3 border-b border-[#e0e0e0] pb-4 max-h-80 overflow-y-auto">
                        @foreach ($items as $item)
                            @php
                                $product = $item->product;
                                $variant = $item->variant;
                                $thumbnail = $variant?->image ?: $product->thumbnail;
                                $image = $thumbnail
                                    ? (str_starts_with($thumbnail, 'http')
                                        ? $thumbnail
                                        : asset(str_starts_with($thumbnail, 'images/') || str_starts_with($thumbnail, 'storage/') ? $thumbnail : 'storage/' . ltrim($thumbnail, '/')))
                                    : asset('images/placeholder.svg');
                            @endphp
                            <div class="flex gap-3">
                                <div class="h-16 w-16 flex-shrink-0 rounded-lg bg-[#fbfaf8] p-1">
                                    <img src="{{ $image }}" class="h-full w-full object-contain" alt="{{ $product->name }}" loading="lazy" decoding="async">
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-[#111]">{{ $product->name }}</p>
                                    @if ($variant)
                                        <p class="text-xs text-[#666]">{{ $variant->name }}</p>
                                    @endif
                                    <div class="mt-1 flex items-center justify-between">
                                        <span class="text-xs text-[#8b8b8b]">×{{ $item->quantity }}</span>
                                        <span class="font-bold text-[#111]">{{ number_format($item->price * $item->quantity, 0, ',', '.') }}đ</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pricing Breakdown --}}
                    <div class="space-y-3 py-4 text-sm">
                        <div class="flex justify-between text-[#666]">
                            <span>Tạm tính</span>
                            <span class="font-semibold text-[#111]">{{ number_format($total, 0, ',', '.') }}đ</span>
                        </div>
                        @if ($discountAmount > 0)
                            <div class="flex justify-between text-[#666]">
                                <span>Chiết khấu</span>
                                <span class="font-semibold text-red-600">-{{ number_format($discountAmount, 0, ',', '.') }}đ</span>
                            </div>
                        @endif
                        @foreach ($taxBreakdown as $rate => $amount)
                            <div class="flex justify-between text-[#666]">
                                <span>Thuế VAT ({{ number_format((float) $rate, 0) }}%)</span>
                                <span class="font-semibold text-[#111]">{{ number_format($amount, 0, ',', '.') }}đ</span>
                            </div>
                        @endforeach
                        <div class="flex justify-between text-[#666]">
                            <span>Giao hàng</span>
                            <span class="font-semibold text-green-600">Miễn phí</span>
                        </div>
                    </div>

                    <div class="border-t border-[#e0e0e0] py-4">
                        <div class="rounded-[16px] border border-[#e7e3dd] bg-[#fbfaf8] p-3.5">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm font-bold text-[#171717]">Mã giảm giá</p>
                                    <p class="mt-0.5 text-[11px] text-[#777]">Nhập mã hoặc chọn từ kho phiếu.</p>
                                </div>
                                <a href="{{ route('account.show') }}#ma-giam-gia-cua-toi" class="shrink-0 text-[11px] font-semibold text-[#0a5ec2] transition hover:text-[#064b99]">Kho phiếu</a>
                            </div>

                            <div class="mt-3 flex gap-2">
                                <label for="checkout-coupon-code" class="sr-only">Mã giảm giá</label>
                                <input form="checkout-coupon-manual-form" id="checkout-coupon-code" name="code" value="{{ old('code') }}" required class="min-w-0 flex-1 rounded-xl border border-[#dfdbd5] bg-white px-3 py-2.5 text-xs font-semibold uppercase outline-none transition focus:border-black focus:ring-2 focus:ring-black/10" placeholder="Nhập mã">
                                <button form="checkout-coupon-manual-form" type="submit" class="rounded-xl bg-black px-3.5 py-2.5 text-xs font-semibold text-white transition hover:bg-[#222]">Áp dụng</button>
                            </div>

                            @if (!empty($appliedCouponsData))
                                <div class="mt-3 space-y-2">
                                    @foreach ($appliedCouponsData as $applied)
                                        <div class="flex items-center justify-between gap-3 rounded-xl border border-green-200 bg-green-50 px-3 py-2.5">
                                            <div class="min-w-0">
                                                <p class="truncate text-xs font-bold text-green-800">{{ $applied['coupon']->code }}</p>
                                                <p class="mt-0.5 text-[10px] text-green-700">Giảm {{ number_format($applied['discount_amount'], 0, ',', '.') }}đ</p>
                                            </div>
                                            <button form="checkout-remove-coupon-{{ $applied['coupon']->id }}" type="submit" class="shrink-0 text-[11px] font-semibold text-red-600 transition hover:text-red-800">Bỏ</button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <details class="group mt-3">
                                <summary class="flex cursor-pointer list-none items-center justify-between gap-3 rounded-xl border border-[#dfdbd5] bg-white px-3 py-2.5 text-xs font-semibold text-[#333] transition hover:border-black [&::-webkit-details-marker]:hidden">
                                    <span>Chọn từ kho phiếu của tôi</span>
                                    <span class="inline-flex items-center gap-1.5 text-[#777]">
                                        {{ $walletCoupons->count() }} mã
                                        <svg class="size-3.5 transition-transform duration-200 group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                                    </span>
                                </summary>

                                <div class="mt-2 space-y-2">
                                    @forelse ($walletCoupons as $coupon)
                                        <button form="checkout-wallet-coupon-{{ $coupon->id }}" type="submit" class="flex w-full items-center justify-between gap-3 rounded-xl border border-[#e7e3dd] bg-white px-3 py-2.5 text-left transition hover:border-black hover:bg-[#f7f5f2]">
                                            <span class="min-w-0">
                                                <span class="block truncate text-xs font-bold text-[#171717]">{{ $coupon->code }}</span>
                                                <span class="mt-0.5 block truncate text-[10px] text-[#777]">
                                                    {{ $coupon->type === 'percent' ? 'Giảm ' . rtrim(rtrim((string) $coupon->value, '0'), '.') . '%' : 'Giảm ' . number_format((float) $coupon->value, 0, ',', '.') . 'đ' }}
                                                    @if ($coupon->min_order_amount > 0)
                                                        · Đơn từ {{ number_format((float) $coupon->min_order_amount, 0, ',', '.') }}đ
                                                    @endif
                                                </span>
                                            </span>
                                            <span class="shrink-0 text-[11px] font-semibold text-[#0a5ec2]">Chọn</span>
                                        </button>
                                    @empty
                                        <div class="rounded-xl border border-dashed border-[#ddd8d0] px-3 py-4 text-center">
                                            <p class="text-[11px] leading-5 text-[#777]">Chưa có mã đang hiệu lực trong kho phiếu.</p>
                                            <a href="{{ route('coupons.index') }}" class="mt-1 inline-flex text-[11px] font-semibold text-[#0a5ec2] transition hover:text-[#064b99]">Xem ưu đãi chung</a>
                                        </div>
                                    @endforelse
                                </div>
                            </details>
                        </div>
                    </div>

                    {{-- Total Price --}}
                    <div class="border-t border-[#e0e0e0] pt-4">
                        <div class="flex items-end justify-between">
                            <span class="font-bold text-[#111]">Tổng cộng</span>
                            <div class="text-right">
                                <div class="text-3xl font-extrabold text-black">{{ number_format($finalTotal, 0, ',', '.') }}đ</div>
                                @if ($discountAmount > 0)
                                    <p class="mt-1 text-xs text-green-600">Tiết kiệm {{ number_format($discountAmount, 0, ',', '.') }}đ</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- CTA Button --}}
                    <button type="submit" class="mt-5 w-full rounded-full bg-gradient-to-r from-black to-[#222] px-5 py-4 text-base font-bold text-white shadow-lg transition-all duration-200 hover:shadow-xl hover:from-[#111] hover:to-[#333] active:scale-95">
                        Đặt hàng ngay
                    </button>

                    {{-- Trust Badges --}}
                    <div class="mt-5 space-y-2">
                        <div class="flex items-center gap-2 rounded-lg border border-[#e0e0e0] bg-[#fbfaf8] p-3 text-xs font-semibold text-[#666]">
                            <span class="text-sm">✓</span> Hàng chính hãng 100%
                        </div>
                        <div class="flex items-center gap-2 rounded-lg border border-[#e0e0e0] bg-[#fbfaf8] p-3 text-xs font-semibold text-[#666]">
                            <span class="text-sm">🔒</span> Bảo mật thanh toán
                        </div>
                        <div class="flex items-center gap-2 rounded-lg border border-[#e0e0e0] bg-[#fbfaf8] p-3 text-xs font-semibold text-[#666]">
                            <span class="text-sm">↩️</span> Đổi trả 30 ngày
                        </div>
                    </div>
                </div>
            </aside>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('checkoutForm');
    const addressRadios = document.querySelectorAll('input[name="shipping_address_id"]');
    const provinceSelect = document.getElementById('province_code');
    const wardSelect = document.getElementById('ward_code');
    const provincesUrl = @json(route('locations.provinces'));
    const wardsUrlTemplate = @json(route('locations.wards', ['provinceCode' => '__province_code__']));

    const replaceOptions = (select, placeholder, options = []) => {
        select.replaceChildren(new Option(placeholder, ''));
        options.forEach(option => select.add(new Option(option.name, option.code)));
    };

    async function loadWards(provinceCode, selectedWardCode = '') {
        wardSelect.disabled = true;
        replaceOptions(wardSelect, provinceCode ? 'Đang tải phường/xã...' : 'Chọn tỉnh/thành phố trước');

        if (!provinceCode) return;

        try {
            const response = await fetch(wardsUrlTemplate.replace('__province_code__', encodeURIComponent(provinceCode)));
            if (!response.ok) throw new Error('Không thể tải danh sách phường/xã.');

            const { data } = await response.json();
            replaceOptions(wardSelect, 'Chọn phường/xã', data);
            wardSelect.value = selectedWardCode;
            wardSelect.disabled = false;
        } catch (error) {
            replaceOptions(wardSelect, 'Không thể tải phường/xã');
        }
    }

    async function loadProvinces(selectedProvinceCode = '', selectedWardCode = '') {
        provinceSelect.disabled = true;
        replaceOptions(provinceSelect, 'Đang tải tỉnh/thành phố...');

        try {
            const response = await fetch(provincesUrl);
            if (!response.ok) throw new Error('Không thể tải danh sách tỉnh/thành phố.');

            const { data } = await response.json();
            replaceOptions(provinceSelect, 'Chọn tỉnh/thành phố', data);
            provinceSelect.value = selectedProvinceCode;
            provinceSelect.disabled = false;
            await loadWards(selectedProvinceCode, selectedWardCode);
        } catch (error) {
            replaceOptions(provinceSelect, 'Không thể tải tỉnh/thành phố');
        }
    }

    async function updateAddressFields(radio) {
        if (radio.checked) {
            document.getElementById('shipping_full_name').value = radio.dataset.fullName || '';
            document.getElementById('shipping_phone').value = radio.dataset.phone || '';
            document.getElementById('shipping_address').value = radio.dataset.address || '';
            await loadProvinces(radio.dataset.provinceCode || '', radio.dataset.wardCode || '');
        }
    }

    addressRadios.forEach(radio => {
        radio.addEventListener('change', async function() {
            await updateAddressFields(this);
        });
    });

    provinceSelect.addEventListener('change', async function() {
        await loadWards(this.value);
    });

    const selectedProvinceCode = provinceSelect.dataset.selected || '';
    const selectedWardCode = wardSelect.dataset.selected || '';
    loadProvinces(selectedProvinceCode, selectedWardCode);

    form.addEventListener('submit', function(e) {
        const fullName = document.getElementById('shipping_full_name').value;
        const phone = document.getElementById('shipping_phone').value;
        const address = document.getElementById('shipping_address').value;

        if (!fullName || !phone || !address || !provinceSelect.value || !wardSelect.value) {
            e.preventDefault();
            alert('Vui lòng chọn tỉnh/thành phố, phường/xã và nhập địa chỉ giao hàng hợp lệ.');
            return false;
        }
    });
});
</script>
@endsection
