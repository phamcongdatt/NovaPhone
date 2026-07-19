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
                            <label id="payment-method-cod" class="relative flex flex-col items-center justify-center gap-3 rounded-2xl border border-white/10 bg-white/5 p-5 cursor-pointer select-none transition hover:border-brand-500/50 hover:bg-white/[0.08] has-[:checked]:border-brand-500 has-[:checked]:bg-brand-600/10 text-center {{ $disableCod ? 'hidden' : '' }}">
                                <input 
                                    type="radio" 
                                    id="input-payment-cod"
                                    name="payment_method" 
                                    value="cod" 
                                    {{ $defaultPaymentMethod === 'cod' ? 'checked' : '' }}
                                    {{ $disableCod ? 'disabled' : '' }}
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
                                    id="input-payment-vnpay"
                                    name="payment_method" 
                                    value="vnpay" 
                                    {{ $defaultPaymentMethod === 'vnpay' ? 'checked' : '' }}
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
                                    $thumbnail = $product->thumbnail ?: 'https://placehold.co/100x100/12151d/93c5fd?text='.urlencode($product->name);
                                @endphp
                                <div class="flex items-center gap-3" data-cart-row="{{ $item->id }}">
                                    <div class="size-11 shrink-0 overflow-hidden rounded-lg bg-night-card p-1 border border-white/10">
                                        <img src="{{ $thumbnail }}" alt="{{ $product->name }}" class="size-full object-contain">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-xs font-bold text-white truncate">{{ $product->name }}</h3>
                                        @if ($variant)
                                            <p class="text-[10px] text-brand-300 font-semibold mt-0.5">{{ $variant->name }}</p>
                                        @endif
                                        
                                        {{-- Selector số lượng --}}
                                        <div class="flex items-center gap-1 bg-white/5 rounded-lg border border-white/10 p-0.5 mt-1.5 w-max">
                                            <button
                                                type="button"
                                                onclick="updateCheckoutQuantity('{{ $item->id }}', -1)"
                                                class="flex size-5 items-center justify-center rounded text-gray-400 hover:bg-white/10 hover:text-white transition"
                                                aria-label="Giảm"
                                            >
                                                <svg class="size-2.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15" />
                                                </svg>
                                            </button>
                                            <input
                                                type="number"
                                                id="quantity-input-{{ $item->id }}"
                                                value="{{ $item->quantity }}"
                                                min="1"
                                                onchange="updateCheckoutQuantity('{{ $item->id }}', this.value, true)"
                                                onkeydown="if(event.key === 'Enter') this.blur();"
                                                class="w-7 text-center bg-transparent border-0 text-xs font-bold text-white focus:ring-0 p-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                            >
                                            <button
                                                type="button"
                                                onclick="updateCheckoutQuantity('{{ $item->id }}', 1)"
                                                class="flex size-5 items-center justify-center rounded text-gray-400 hover:bg-white/10 hover:text-white transition"
                                                aria-label="Tăng"
                                            >
                                                <svg class="size-2.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <p id="item-subtotal-{{ $item->id }}" class="text-xs font-bold text-white">{{ $money($item->price * $item->quantity) }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Hoá đơn --}}
                        <div class="space-y-3 border-b border-white/10 pb-4 mb-4">
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-400">Tổng tiền sản phẩm</span>
                                <span id="checkout-subtotal" class="font-semibold text-white">{{ $money($total) }}</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-400">Phí vận chuyển</span>
                                <span id="checkout-shipping" class="font-semibold text-emerald-400">Miễn phí</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-400">Khấu trừ giảm giá</span>
                                <span id="checkout-discount" class="font-semibold text-gray-500">0đ</span>
                            </div>
                        </div>

                        <div class="flex justify-between items-baseline mb-6">
                            <span class="text-sm font-bold text-white">Tổng thanh toán</span>
                            <span id="checkout-total" class="text-xl font-black text-brand-400">{{ $money($total) }}</span>
                        </div>

                        <button 
                            type="submit" 
                            class="w-full flex items-center justify-center rounded-xl bg-gradient-to-r from-amber-400 to-amber-500 py-4 text-base font-black text-gray-950 shadow-lg shadow-amber-500/20 transition hover:-translate-y-0.5 hover:shadow-amber-500/30"
                        >
                            Đặt hàng ngay
                        </button>
                    </div>
                </div>

            </div>
        </form>

    </div>
</div>

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
        
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'fixed bottom-5 right-5 z-50 flex flex-col gap-3';
            document.body.appendChild(container);
        }
        container.appendChild(toast);

        // Animation in
        setTimeout(() => {
            toast.classList.remove('translate-y-5', 'opacity-0');
        }, 10);

        // Animation out & remove
        setTimeout(() => {
            toast.classList.add('translate-y-5', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    function updateCheckoutQuantity(itemId, changeOrQty, isDirect = false) {
        const input = document.getElementById(`quantity-input-${itemId}`);
        const currentQty = parseInt(input.value) || 1;
        let newQty = isDirect ? parseInt(changeOrQty) : (currentQty + changeOrQty);

        if (isNaN(newQty) || newQty < 1) {
            newQty = 1;
            input.value = 1;
        }

        // Gọi AJAX cập nhật giỏ hàng (PATCH /cart/update/{itemId})
        fetch(`/cart/update/${itemId}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ quantity: newQty })
        })
        .then(async response => {
            const data = await response.json();
            if (response.ok) {
                input.value = data.item_quantity;
                document.getElementById(`item-subtotal-${itemId}`).textContent = data.item_subtotal;
                document.getElementById('checkout-subtotal').textContent = data.cart_total;
                document.getElementById('checkout-total').textContent = data.cart_total;

                // Cập nhật số trên header nếu có
                const headerCounts = document.querySelectorAll('.absolute.-right-2.-top-1\\.5');
                headerCounts.forEach(el => el.textContent = data.cart_count);

                showToast('Đã cập nhật số lượng thành công!');

                // Kiểm tra phương thức thanh toán dựa trên tổng tiền
                checkPaymentMethods(data.cart_total_raw);
            } else {
                showToast(data.message || 'Có lỗi xảy ra', 'error');
                if (data.item_quantity) {
                    input.value = data.item_quantity;
                } else {
                    input.value = currentQty;
                }
            }
        })
        .catch(err => {
            console.error(err);
            showToast('Không thể kết nối đến máy chủ.', 'error');
            input.value = currentQty;
        });
    }

    function checkPaymentMethods(totalRaw) {
        const codLabel = document.getElementById('payment-method-cod');
        const codInput = document.getElementById('input-payment-cod');
        const vnpayInput = document.getElementById('input-payment-vnpay');

        if (totalRaw > 20000000) {
            if (codLabel) {
                codLabel.classList.add('hidden');
            }
            if (codInput) {
                codInput.disabled = true;
                if (codInput.checked) {
                    vnpayInput.checked = true;
                }
            }
        } else {
            if (codLabel) {
                codLabel.classList.remove('hidden');
            }
            if (codInput) {
                codInput.disabled = false;
            }
        }
    }
</script>
@endpush
@endsection
