@extends('layouts.app')

@section('title', 'Thanh toán đơn hàng | NovaPhone')

@section('content')
@php
    $money = fn ($value) => number_format((float) $value, 0, ',', '.').'đ';
@endphp

<div class="bg-night text-gray-100 min-h-[calc(100vh-68px-340px)] py-8">
    <div class="mx-auto max-w-7xl px-4 sm:px-6">
        
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
            <a href="{{ route('home') }}" class="transition-colors hover:text-brand-400">Trang chủ</a>
            <span>/</span>
            <a href="{{ route('cart.index') }}" class="transition-colors hover:text-brand-400">Giỏ hàng</a>
            <span>/</span>
            <span class="font-medium text-gray-300">Thanh toán</span>
        </nav>

        <h1 class="text-3xl font-black text-white tracking-tight mb-8">Thanh toán</h1>

        @if (session('error'))
            <div class="mb-6 rounded-2xl border border-red-500/20 bg-red-500/10 p-4 text-sm text-red-400">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('checkout.place-order') }}">
            @csrf

            <div class="grid gap-8 lg:grid-cols-3">
                
                {{-- Form thông tin giao hàng --}}
                <div class="lg:col-span-2 space-y-6">
                    
                    {{-- Địa chỉ nhận hàng --}}
                    <div class="rounded-3xl border border-white/5 bg-night-soft p-6 shadow-xl shadow-black/30">
                        <h2 class="text-lg font-extrabold text-white mb-5 flex items-center gap-2">
                            <span class="flex size-6 items-center justify-center rounded-lg bg-brand-600 text-xs text-white">1</span>
                            Thông tin nhận hàng
                        </h2>

                        <div class="grid gap-5 sm:grid-cols-2">
                            {{-- Họ tên --}}
                            <div class="space-y-1.5">
                                <label for="shipping_full_name" class="text-xs font-bold uppercase tracking-wider text-gray-400">Họ và tên người nhận</label>
                                <input 
                                    type="text" 
                                    name="shipping_full_name" 
                                    id="shipping_full_name" 
                                    value="{{ old('shipping_full_name', $defaultAddress->full_name ?? Auth::user()->name) }}" 
                                    required 
                                    placeholder="Nguyễn Văn A"
                                    class="w-full rounded-xl border border-white/10 bg-white/5 py-3 px-4 text-sm text-white outline-none transition duration-200 focus:border-brand-500 focus:bg-white/[0.08] focus:ring-2 focus:ring-brand-500/25"
                                >
                                @error('shipping_full_name')
                                    <span class="text-xs text-red-500">{{ $message }}</span>
                                @enderror
                                <span id="shipping_full_name_error" class="text-xs text-red-500 hidden">Chỉ được phép nhập ký tự chữ</span>
                            </div>

                            {{-- Số điện thoại --}}
                            <div class="space-y-1.5">
                                <label for="shipping_phone" class="text-xs font-bold uppercase tracking-wider text-gray-400">Số điện thoại liên hệ</label>
                                <input 
                                    type="tel" 
                                    name="shipping_phone" 
                                    id="shipping_phone" 
                                    value="{{ old('shipping_phone', $defaultAddress->phone ?? Auth::user()->phone) }}" 
                                    required 
                                    placeholder="09XXXXXXXX"
                                    class="w-full rounded-xl border border-white/10 bg-white/5 py-3 px-4 text-sm text-white outline-none transition duration-200 focus:border-brand-500 focus:bg-white/[0.08] focus:ring-2 focus:ring-brand-500/25"
                                >
                                @error('shipping_phone')
                                    <span class="text-xs text-red-500">{{ $message }}</span>
                                @enderror
                                <span id="shipping_phone_error" class="text-xs text-red-500 hidden">Chỉ được phép nhập ký tự số</span>
                            </div>

                            {{-- Tỉnh / Thành phố --}}
                            <div class="space-y-1.5">
                                <label for="shipping_province" class="text-xs font-bold uppercase tracking-wider text-gray-400">Tỉnh / Thành phố</label>
                                <input 
                                    type="text" 
                                    name="shipping_province" 
                                    id="shipping_province" 
                                    value="{{ old('shipping_province', $defaultAddress->province ?? '') }}" 
                                    required 
                                    placeholder="Hồ Chí Minh"
                                    class="w-full rounded-xl border border-white/10 bg-white/5 py-3 px-4 text-sm text-white outline-none transition duration-200 focus:border-brand-500 focus:bg-white/[0.08] focus:ring-2 focus:ring-brand-500/25"
                                >
                                @error('shipping_province')
                                    <span class="text-xs text-red-500">{{ $message }}</span>
                                @enderror
                                <span id="shipping_province_error" class="text-xs text-red-500 hidden">Chỉ được phép nhập ký tự chữ</span>
                            </div>

                            {{-- Quận / Huyện --}}
                            <div class="space-y-1.5">
                                <label for="shipping_district" class="text-xs font-bold uppercase tracking-wider text-gray-400">Quận / Huyện</label>
                                <input 
                                    type="text" 
                                    name="shipping_district" 
                                    id="shipping_district" 
                                    value="{{ old('shipping_district', $defaultAddress->district ?? '') }}" 
                                    required 
                                    placeholder="Quận 1"
                                    class="w-full rounded-xl border border-white/10 bg-white/5 py-3 px-4 text-sm text-white outline-none transition duration-200 focus:border-brand-500 focus:bg-white/[0.08] focus:ring-2 focus:ring-brand-500/25"
                                >
                                @error('shipping_district')
                                    <span class="text-xs text-red-500">{{ $message }}</span>
                                @enderror
                                <span id="shipping_district_error" class="text-xs text-red-500 hidden">Chỉ được phép nhập ký tự chữ</span>
                            </div>

                            {{-- Phường / Xã --}}
                            <div class="space-y-1.5">
                                <label for="shipping_ward" class="text-xs font-bold uppercase tracking-wider text-gray-400">Phường / Xã</label>
                                <input 
                                    type="text" 
                                    name="shipping_ward" 
                                    id="shipping_ward" 
                                    value="{{ old('shipping_ward', $defaultAddress->ward ?? '') }}" 
                                    required 
                                    placeholder="Phường Bến Nghé"
                                    class="w-full rounded-xl border border-white/10 bg-white/5 py-3 px-4 text-sm text-white outline-none transition duration-200 focus:border-brand-500 focus:bg-white/[0.08] focus:ring-2 focus:ring-brand-500/25"
                                >
                                @error('shipping_ward')
                                    <span class="text-xs text-red-500">{{ $message }}</span>
                                @enderror
                                <span id="shipping_ward_error" class="text-xs text-red-500 hidden">Chỉ được phép nhập ký tự chữ</span>
                            </div>

                            {{-- Địa chỉ chi tiết --}}
                            <div class="space-y-1.5">
                                <label for="shipping_address" class="text-xs font-bold uppercase tracking-wider text-gray-400">Số nhà, tên đường</label>
                                <input 
                                    type="text" 
                                    name="shipping_address" 
                                    id="shipping_address" 
                                    value="{{ old('shipping_address', $defaultAddress->address ?? '') }}" 
                                    required 
                                    placeholder="123 Đường Nguyễn Huệ"
                                    class="w-full rounded-xl border border-white/10 bg-white/5 py-3 px-4 text-sm text-white outline-none transition duration-200 focus:border-brand-500 focus:bg-white/[0.08] focus:ring-2 focus:ring-brand-500/25"
                                >
                                @error('shipping_address')
                                    <span class="text-xs text-red-500">{{ $message }}</span>
                                @enderror
                                <span id="shipping_address_error" class="text-xs text-red-500 hidden">Không được để trống</span>
                            </div>
                        </div>

                        {{-- Ghi chú --}}
                        <div class="space-y-1.5 mt-5">
                            <label for="note" class="text-xs font-bold uppercase tracking-wider text-gray-400">Ghi chú giao hàng (Tùy chọn)</label>
                            <textarea 
                                name="note" 
                                id="note" 
                                rows="3" 
                                placeholder="Giao ngoài giờ hành chính, gọi trước khi đến..."
                                class="w-full rounded-xl border border-white/10 bg-white/5 py-3 px-4 text-sm text-white outline-none transition duration-200 focus:border-brand-500 focus:bg-white/[0.08] focus:ring-2 focus:ring-brand-500/25 resize-none"
                            >{{ old('note') }}</textarea>
                        </div>
                    </div>

                    {{-- Phương thức thanh toán --}}
                    <div class="rounded-3xl border border-white/5 bg-night-soft p-6 shadow-xl shadow-black/30">
                        <h2 class="text-lg font-extrabold text-white mb-5 flex items-center gap-2">
                            <span class="flex size-6 items-center justify-center rounded-lg bg-brand-600 text-xs text-white">2</span>
                            Phương thức thanh toán
                        </h2>

                        <div class="grid gap-4 sm:grid-cols-3">
                            
                            {{-- COD --}}
                            <label class="relative flex flex-col items-center justify-center gap-3 rounded-2xl border border-white/10 bg-white/5 p-5 cursor-pointer select-none transition hover:border-brand-500/50 hover:bg-white/[0.08] has-[:checked]:border-brand-500 has-[:checked]:bg-brand-600/10 text-center">
                                <input 
                                    type="radio" 
                                    name="payment_method" 
                                    value="cod" 
                                    checked 
                                    class="sr-only"
                                >
                                <span class="flex size-10 items-center justify-center rounded-xl bg-amber-500/10 text-amber-400 font-bold text-xs">COD</span>
                                <div>
                                    <span class="block text-sm font-bold text-white">Thanh toán COD</span>
                                    <span class="block text-[11px] text-gray-400 mt-1">Nhận hàng thanh toán</span>
                                </div>
                            </label>

                            {{-- VNPay --}}
                            <label class="relative flex flex-col items-center justify-center gap-3 rounded-2xl border border-white/10 bg-white/5 p-5 cursor-pointer select-none transition hover:border-brand-500/50 hover:bg-white/[0.08] has-[:checked]:border-brand-500 has-[:checked]:bg-brand-600/10 text-center">
                                <input 
                                    type="radio" 
                                    name="payment_method" 
                                    value="vnpay" 
                                    {{ old('payment_method') === 'vnpay' ? 'checked' : '' }}
                                    class="sr-only"
                                >
                                <span class="flex size-10 items-center justify-center rounded-xl bg-sky-500/15 text-sky-400 font-bold text-xs">VNPAY</span>
                                <div>
                                    <span class="block text-sm font-bold text-white">Ví VNPAY / Bank</span>
                                    <span class="block text-[11px] text-gray-400 mt-1">Quét mã QR ngân hàng</span>
                                </div>
                            </label>

                        </div>
                    </div>

                </div>

                {{-- Tóm tắt đơn hàng (Giỏ hàng thu nhỏ) --}}
                <div>
                    <div class="sticky top-24 rounded-3xl border border-white/5 bg-night-soft p-6 shadow-xl shadow-black/30">
                        <h2 class="text-lg font-extrabold text-white mb-5">Đơn hàng của bạn</h2>
                        
                        {{-- Danh sách item --}}
                        <div class="max-h-60 overflow-y-auto pr-1 space-y-3 mb-6 no-scrollbar border-b border-white/10 pb-4">
                            @foreach ($items as $item)
                                @php
                                    $product = $item->product;
                                    $variant = $item->variant;
                                    $thumbnail = $product->thumbnail ?: asset('images/placeholder.svg');
                                @endphp
                                <div class="flex items-center gap-3">
                                    <div class="size-11 shrink-0 overflow-hidden rounded-lg bg-night-card p-1 border border-white/10">
                                        <img src="{{ $thumbnail }}" alt="{{ $product->name }}" class="size-full object-contain">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-xs font-bold text-white truncate">{{ $product->name }}</h3>
                                        <p class="text-[10px] text-gray-400 mt-0.5">
                                            {{ $item->quantity }} x {{ $money($item->price) }}
                                            @if ($variant)
                                                | <span class="text-brand-300 font-semibold">{{ $variant->name }}</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <p class="text-xs font-bold text-white">{{ $money($item->price * $item->quantity) }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Mã giảm giá --}}
                        <div class="space-y-3 border-b border-white/10 pb-4 mb-4">
                            <h3 class="text-sm font-bold text-white mb-2">Mã giảm giá</h3>
                            
                            <div class="flex items-center gap-2" id="coupon-input-container">
                                <input type="text" id="coupon-code-input" placeholder="Nhập mã giảm giá..." 
                                    class="w-full uppercase rounded-xl border border-white/10 bg-white/5 py-2.5 px-4 text-sm text-white outline-none transition focus:border-brand-500">
                                
                                <button type="button" onclick="applyCoupon()" class="shrink-0 rounded-xl bg-white/10 px-4 py-2.5 text-sm font-bold text-white hover:bg-white/20 transition">Áp dụng</button>
                            </div>

                            <div id="applied-coupons-container" class="space-y-2" style="{{ !empty($appliedCouponsData) ? '' : 'display: none;' }}">
                                @if(!empty($appliedCouponsData))
                                    @foreach($appliedCouponsData as $ac)
                                    <div class="flex items-center justify-between gap-2 p-3 rounded-xl bg-brand-500/10 border border-brand-500/20">
                                        <div>
                                            <p class="text-xs font-bold text-brand-400">{{ $ac['coupon']->code }}</p>
                                            <p class="text-[10px] text-gray-400">Giảm {{ number_format($ac['discount_amount'], 0, ',', '.') }}đ</p>
                                        </div>
                                        <button type="button" onclick="removeCoupon('{{ $ac['coupon']->code }}')" class="shrink-0 text-xs font-bold text-red-400 hover:text-red-300 transition">Bỏ mã</button>
                                    </div>
                                    @endforeach
                                @endif
                            </div>

                            <p id="coupon-error-message" class="text-xs text-red-400 hidden"></p>

                            {{-- Danh sách mã có thể dùng --}}
                            @if (isset($availableCoupons) && $availableCoupons->isNotEmpty())
                                <div class="pt-3 border-t border-white/5" id="available-coupons-list">
                                    <p class="text-[11px] font-semibold text-gray-400 mb-2 uppercase tracking-wider">Mã giảm giá khả dụng</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($availableCoupons as $avCoupon)
                                            <button type="button" 
                                                onclick="quickApplyCoupon('{{ $avCoupon->code }}')"
                                                class="group relative overflow-hidden rounded-xl border border-brand-500/30 bg-brand-500/10 px-3 py-1.5 text-left transition hover:border-brand-500 hover:bg-brand-500/20 text-xs">
                                                <div class="font-bold text-brand-400 group-hover:text-brand-300">{{ $avCoupon->code }}</div>
                                                @if($avCoupon->description)
                                                    <div class="text-[10px] text-gray-400 truncate max-w-[140px]">{{ $avCoupon->description }}</div>
                                                @endif
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Hoá đơn --}}
                        <div class="space-y-3 border-b border-white/10 pb-4 mb-4">
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-400">Tổng tiền sản phẩm</span>
                                <span class="font-semibold text-white">{{ $money($total) }}</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-400">Phí vận chuyển</span>
                                <span class="font-semibold text-emerald-400">Miễn phí</span>
                            </div>
                            <div class="flex justify-between text-xs" id="discount-row" style="{{ $discountAmount > 0 ? '' : 'display: none;' }}">
                                <span class="text-gray-400">Khấu trừ giảm giá</span>
                                <span class="font-semibold text-brand-400">-<span id="discount-amount">{{ number_format($discountAmount, 0, ',', '.') }}đ</span></span>
                            </div>
                        </div>

                        <div class="flex justify-between items-baseline mb-6">
                            <span class="text-sm font-bold text-white">Tổng thanh toán</span>
                            @php
                                $finalTotal = max(0, $total - $discountAmount);
                            @endphp
                            <span class="text-xl font-black text-brand-400" id="final-total">{{ $money($finalTotal) }}</span>
                        </div>

                        <button 
                            type="submit" 
                            id="submit-button"
                            class="w-full flex items-center justify-center rounded-xl bg-gradient-to-r from-amber-400 to-amber-500 py-4 text-base font-black text-gray-950 shadow-lg shadow-amber-500/20 transition hover:-translate-y-0.5 hover:shadow-amber-500/30 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0 disabled:hover:shadow-amber-500/20"
                        >
                            Đặt hàng ngay
                        </button>
                    </div>
                </div>

            </div>
        </form>

    </div>
</div>
@endsection

@push('scripts')
<script>
    const formatMoney = (amount) => {
        return new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
    };

    const totalAmount = {{ $total }};

    // Kiểm tra trạng thái validation toàn bộ form
    function checkFormValidation() {
        const fullNameError = document.getElementById('shipping_full_name_error');
        const phoneError = document.getElementById('shipping_phone_error');
        const provinceError = document.getElementById('shipping_province_error');
        const districtError = document.getElementById('shipping_district_error');
        const wardError = document.getElementById('shipping_ward_error');
        const addressError = document.getElementById('shipping_address_error');
        const submitButton = document.getElementById('submit-button');
        
        // Nếu có lỗi hiển thị, disable button
        const hasErrors = !fullNameError.classList.contains('hidden') || 
                         !phoneError.classList.contains('hidden') ||
                         !provinceError.classList.contains('hidden') ||
                         !districtError.classList.contains('hidden') ||
                         !wardError.classList.contains('hidden') ||
                         !addressError.classList.contains('hidden');
        
        if (hasErrors) {
            submitButton.disabled = true;
        } else {
            submitButton.disabled = false;
        }
    }

    // Validate shipping_full_name - chỉ được ký tự chữ
    document.getElementById('shipping_full_name').addEventListener('input', function() {
        const value = this.value.trim();
        const errorSpan = document.getElementById('shipping_full_name_error');
        
        // Regex để kiểm tra chỉ có ký tự chữ (hỗ trợ tiếng Việt) và khoảng trắng
        const nameRegex = /^[a-zA-ZÀ-ỹ\s]*$/;
        
        if (value && !nameRegex.test(value)) {
            errorSpan.classList.remove('hidden');
        } else {
            errorSpan.classList.add('hidden');
        }
        
        checkFormValidation();
    });

    // Validate shipping_phone - chỉ được ký tự số, tối đa 11 chữ số
    document.getElementById('shipping_phone').addEventListener('input', function() {
        const value = this.value.trim();
        const errorSpan = document.getElementById('shipping_phone_error');
        
        // Chỉ cho phép nhập số
        this.value = this.value.replace(/[^\d]/g, '');
        
        // Giới hạn tối đa 11 chữ số
        if (this.value.length > 11) {
            this.value = this.value.slice(0, 11);
        }
        
        // Kiểm tra nếu có nhập dữ liệu nhưng không phải số hoặc vượt quá 11 chữ số
        if (value && (!/^\d+$/.test(value) || value.length > 11)) {
            errorSpan.classList.remove('hidden');
        } else {
            errorSpan.classList.add('hidden');
        }
        
        checkFormValidation();
    });

    // Validate shipping_province - chỉ được ký tự chữ
    document.getElementById('shipping_province').addEventListener('input', function() {
        const value = this.value.trim();
        const errorSpan = document.getElementById('shipping_province_error');
        
        // Regex để kiểm tra chỉ có ký tự chữ (hỗ trợ tiếng Việt) và khoảng trắng
        const nameRegex = /^[a-zA-ZÀ-ỹ\s]*$/;
        
        if (value && !nameRegex.test(value)) {
            errorSpan.classList.remove('hidden');
        } else {
            errorSpan.classList.add('hidden');
        }
        
        checkFormValidation();
    });

    // Validate shipping_district - chỉ được ký tự chữ
    document.getElementById('shipping_district').addEventListener('input', function() {
        const value = this.value.trim();
        const errorSpan = document.getElementById('shipping_district_error');
        
        // Regex để kiểm tra chỉ có ký tự chữ (hỗ trợ tiếng Việt) và khoảng trắng
        const nameRegex = /^[a-zA-ZÀ-ỹ\s]*$/;
        
        if (value && !nameRegex.test(value)) {
            errorSpan.classList.remove('hidden');
        } else {
            errorSpan.classList.add('hidden');
        }
        
        checkFormValidation();
    });

    // Validate shipping_ward - chỉ được ký tự chữ
    document.getElementById('shipping_ward').addEventListener('input', function() {
        const value = this.value.trim();
        const errorSpan = document.getElementById('shipping_ward_error');
        
        // Regex để kiểm tra chỉ có ký tự chữ (hỗ trợ tiếng Việt) và khoảng trắng
        const nameRegex = /^[a-zA-ZÀ-ỹ\s]*$/;
        
        if (value && !nameRegex.test(value)) {
            errorSpan.classList.remove('hidden');
        } else {
            errorSpan.classList.add('hidden');
        }
        
        checkFormValidation();
    });

    // Validate shipping_address - không được để trống
    document.getElementById('shipping_address').addEventListener('input', function() {
        const value = this.value.trim();
        const errorSpan = document.getElementById('shipping_address_error');
        
        if (!value) {
            errorSpan.classList.remove('hidden');
        } else {
            errorSpan.classList.add('hidden');
        }
        
        checkFormValidation();
    });

    // Kiểm tra validation khi page load
    window.addEventListener('load', function() {
        checkFormValidation();
    });

    function quickApplyCoupon(code) {
        document.getElementById('coupon-code-input').value = code;
        applyCoupon();
    }

    function applyCoupon() {
        const code = document.getElementById('coupon-code-input').value.trim();
        const errorMsg = document.getElementById('coupon-error-message');
        if (!code) {
            errorMsg.innerText = 'Vui lòng nhập mã giảm giá.';
            errorMsg.classList.remove('hidden');
            return;
        }

        fetch('{{ route('checkout.apply-coupon') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ code: code })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                errorMsg.classList.add('hidden');
                document.getElementById('coupon-code-input').value = '';
                
                renderAppliedCoupons(data.data.coupons);

                // Update totals
                if (data.data.discount_amount > 0) {
                    document.getElementById('discount-row').style.display = 'flex';
                    document.getElementById('discount-amount').innerText = formatMoney(data.data.discount_amount);
                } else {
                    document.getElementById('discount-row').style.display = 'none';
                }

                let finalTotal = totalAmount - data.data.discount_amount;
                if (finalTotal < 0) finalTotal = 0;
                document.getElementById('final-total').innerText = formatMoney(finalTotal);
            } else {
                errorMsg.innerText = data.message;
                errorMsg.classList.remove('hidden');
            }
        })
        .catch(err => {
            errorMsg.innerText = 'Đã có lỗi xảy ra. Vui lòng thử lại sau.';
            errorMsg.classList.remove('hidden');
        });
    }

    function removeCoupon(code) {
        fetch('{{ route('checkout.remove-coupon') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ code: code })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // To properly refresh everything, the easiest is to reload the page to get the updated session state,
                // OR we can just reload the page for both apply and remove to be safe, but since we already have DOM update:
                window.location.reload();
            }
        });
    }

    function renderAppliedCoupons(coupons) {
        const container = document.getElementById('applied-coupons-container');
        if (!coupons || coupons.length === 0) {
            container.style.display = 'none';
            container.innerHTML = '';
            return;
        }

        container.style.display = 'block';
        let html = '';
        coupons.forEach(ac => {
            html += `
            <div class="flex items-center justify-between gap-2 p-3 rounded-xl bg-brand-500/10 border border-brand-500/20">
                <div>
                    <p class="text-xs font-bold text-brand-400">${ac.coupon.code}</p>
                    <p class="text-[10px] text-gray-400">Giảm ${formatMoney(ac.discount_amount)}</p>
                </div>
                <button type="button" onclick="removeCoupon('${ac.coupon.code}')" class="shrink-0 text-xs font-bold text-red-400 hover:text-red-300 transition">Bỏ mã</button>
            </div>
            `;
        });
        container.innerHTML = html;
    }
</script>
@endpush
