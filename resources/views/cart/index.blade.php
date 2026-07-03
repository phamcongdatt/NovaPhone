@extends('layouts.app')

@section('title', 'Giỏ hàng của bạn | NovaPhone')

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
            <span class="font-medium text-gray-300">Giỏ hàng</span>
        </nav>

        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-black text-white tracking-tight">Giỏ hàng của bạn</h1>
            @if ($items->isNotEmpty())
                <button
                    type="button"
                    onclick="clearCart()"
                    class="flex items-center gap-2 rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-2 text-sm font-semibold text-red-400 transition hover:bg-red-500 hover:text-white"
                >
                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.34 6.6m-3.6 0L10.3 9m4.7-3.6-.3-1.8a2.25 2.25 0 0 0-2.25-2.25h-3a2.25 2.25 0 0 0-2.25 2.25L6.74 5.4M19.5 5.4h-15"/>
                    </svg>
                    Xóa tất cả
                </button>
            @endif
        </div>

        {{-- Nếu giỏ hàng trống --}}
        <div id="empty-cart-view" class="{{ $items->isEmpty() ? '' : 'hidden' }} rounded-3xl border border-white/5 bg-night-soft p-12 text-center shadow-xl shadow-black/30">
            <div class="mx-auto flex size-16 items-center justify-center rounded-2xl bg-white/5 text-gray-400 mb-4">
                <svg class="size-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.36-1.62 1.26 12a1.13 1.13 0 0 1-1.12 1.24H4.25a1.13 1.13 0 0 1-1.12-1.24l1.26-12A1.13 1.13 0 0 1 5.51 7.88h12.98c.58 0 1.06.43 1.12 1Z" />
                </svg>
            </div>
            <h2 class="text-xl font-bold text-white mb-2">Giỏ hàng đang trống</h2>
            <p class="text-gray-400 mb-6 max-w-md mx-auto">Bạn chưa thêm sản phẩm nào vào giỏ hàng. Hãy quay lại trang chủ để khám phá các mẫu điện thoại mới nhất.</p>
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center rounded-xl bg-brand-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-brand-600/20 transition hover:bg-brand-500 hover:-translate-y-0.5">
                Tiếp tục mua sắm
            </a>
        </div>

        {{-- Bố cục Giỏ hàng --}}
        <div id="cart-content-view" class="{{ $items->isEmpty() ? 'hidden' : 'grid' }} gap-8 lg:grid-cols-3">

            {{-- Danh sách sản phẩm --}}
            <div class="lg:col-span-2 space-y-4">
                @foreach ($items as $item)
                    @php
                        $product   = $item->product;
                        $variant   = $item->variant;
                        $thumbnail = $product->thumbnail ?: 'https://placehold.co/300x300/12151d/93c5fd?text='.urlencode($product->name);
                    @endphp
                    <div data-cart-row="{{ $item->id }}" class="flex flex-col sm:flex-row items-start sm:items-center gap-4 rounded-2xl border border-white/5 bg-night-soft p-4 shadow-lg transition duration-300 hover:border-white/10 hover:bg-white/[0.02]">

                        {{-- Ảnh sản phẩm --}}
                        <div class="size-20 shrink-0 overflow-hidden rounded-xl bg-night-card p-1.5 border border-white/5">
                            <img src="{{ $thumbnail }}" alt="{{ $product->name }}" class="size-full object-contain">
                        </div>

                        {{-- Tên & variant --}}
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('products.show', $product->slug) }}" class="font-bold text-white hover:text-brand-400 transition-colors block truncate text-base">
                                {{ $product->name }}
                            </a>
                            @if ($variant)
                                <p class="text-xs text-brand-300 font-semibold mt-1">Phiên bản: {{ $variant->name }}</p>
                            @endif
                            <p class="text-sm text-gray-400 font-medium mt-1 sm:hidden">Đơn giá: {{ $money($item->price) }}</p>
                        </div>

                        {{-- Bộ chọn số lượng --}}
                        <div class="flex items-center gap-2.5 bg-white/5 rounded-xl border border-white/10 p-1">
                            <button
                                type="button"
                                onclick="updateQuantity('{{ $item->id }}', -1)"
                                class="flex size-7 items-center justify-center rounded-lg text-gray-400 hover:bg-white/10 hover:text-white transition"
                                aria-label="Giảm"
                            >
                                <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15" />
                                </svg>
                            </button>
                            <input
                                type="number"
                                id="quantity-input-{{ $item->id }}"
                                value="{{ $item->quantity }}"
                                min="1"
                                readonly
                                class="w-10 text-center bg-transparent border-0 text-sm font-bold text-white focus:ring-0 select-none pointer-events-none"
                            >
                            <button
                                type="button"
                                onclick="updateQuantity('{{ $item->id }}', 1)"
                                class="flex size-7 items-center justify-center rounded-lg text-gray-400 hover:bg-white/10 hover:text-white transition"
                                aria-label="Tăng"
                            >
                                <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </button>
                        </div>

                        {{-- Giá tiền & Xóa --}}
                        <div class="flex sm:flex-col items-center sm:items-end justify-between w-full sm:w-auto gap-4">
                            <div class="text-left sm:text-right">
                                <p class="text-xs text-gray-500 font-medium hidden sm:block">Thành tiền</p>
                                <p id="item-subtotal-{{ $item->id }}" class="text-lg font-black text-brand-400 mt-0.5">
                                    {{ $money($item->price * $item->quantity) }}
                                </p>
                            </div>
                            <button
                                type="button"
                                onclick="removeItem('{{ $item->id }}')"
                                class="flex size-9 items-center justify-center rounded-xl bg-red-500/10 text-red-400 border border-red-500/20 transition hover:bg-red-500 hover:text-white"
                                aria-label="Xóa sản phẩm"
                            >
                                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.34 6.6m-3.6 0L10.3 9m4.7-3.6-.3-1.8a2.25 2.25 0 0 0-2.25-2.25h-3a2.25 2.25 0 0 0-2.25 2.25L6.74 5.4M19.5 5.4h-15" />
                                </svg>
                            </button>
                        </div>

                    </div>
                @endforeach
            </div>

            {{-- Hóa đơn thanh toán --}}
            <div>
                <div class="sticky top-24 rounded-3xl border border-white/5 bg-night-soft p-6 shadow-xl shadow-black/30">
                    <h2 class="text-lg font-extrabold text-white mb-4">Chi tiết thanh toán</h2>

                    <div class="space-y-3 border-b border-white/10 pb-4 mb-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Tổng phụ sản phẩm</span>
                            <span id="cart-subtotal" class="font-semibold text-white">{{ $money($total) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Phí vận chuyển</span>
                            <span class="font-semibold text-emerald-400">Miễn phí</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Khuyến mãi giảm giá</span>
                            <span class="font-semibold text-gray-500">0đ</span>
                        </div>
                    </div>

                    <div class="flex justify-between items-baseline mb-6">
                        <span class="text-base font-bold text-white">Tổng cộng</span>
                        <span id="cart-total" class="text-2xl font-black text-brand-400">{{ $money($total) }}</span>
                    </div>

                    <div class="grid gap-3">
                        <a
                            href="{{ route('checkout') }}"
                            class="flex items-center justify-center rounded-xl bg-gradient-to-r from-amber-400 to-amber-500 py-4 text-base font-black text-gray-950 shadow-lg shadow-amber-500/20 transition hover:-translate-y-0.5 hover:shadow-amber-500/30"
                        >
                            Tiến hành thanh toán
                        </a>
                        <a
                            href="{{ route('home') }}"
                            class="flex items-center justify-center rounded-xl border border-white/10 bg-white/5 py-3 text-sm font-bold text-gray-300 transition hover:bg-white/10 hover:text-white"
                        >
                            Tiếp tục mua điện thoại
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Toast --}}
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
        setTimeout(() => { toast.classList.add('translate-y-5', 'opacity-0'); setTimeout(() => toast.remove(), 300); }, 3000);
    }

    function updateCartHeader(count) {
        document.querySelectorAll('[data-cart-count]').forEach(el => el.textContent = count);
    }

    function updateQuantity(itemId, change) {
        const input = document.getElementById(`quantity-input-${itemId}`);
        const newQty = parseInt(input.value) + change;
        if (newQty < 1) return;

        fetch(`/cart/update/${itemId}`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({ quantity: newQty })
        })
        .then(async response => {
            const data = await response.json();
            if (response.ok) {
                input.value = data.item_quantity;
                document.getElementById(`item-subtotal-${itemId}`).textContent = data.item_subtotal;
                document.getElementById('cart-subtotal').textContent = data.cart_total;
                document.getElementById('cart-total').textContent = data.cart_total;
                updateCartHeader(data.cart_count);
                showToast('Đã cập nhật số lượng thành công!');
            } else {
                showToast(data.message || 'Có lỗi xảy ra', 'error');
            }
        })
        .catch(() => showToast('Không thể kết nối đến máy chủ.', 'error'));
    }

    function removeItem(itemId) {
        if (!confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng không?')) return;

        const row = document.querySelector(`[data-cart-row="${itemId}"]`);

        fetch(`/cart/remove/${itemId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        })
        .then(async response => {
            const data = await response.json();
            if (response.ok) {
                row.classList.add('transition-all', 'duration-300', 'scale-95', 'opacity-0');
                setTimeout(() => {
                    row.remove();
                    document.getElementById('cart-subtotal').textContent = data.cart_total;
                    document.getElementById('cart-total').textContent = data.cart_total;
                    updateCartHeader(data.cart_count);
                    if (data.cart_total_raw <= 0) {
                        document.getElementById('cart-content-view').classList.add('hidden');
                        document.getElementById('empty-cart-view').classList.remove('hidden');
                    }
                    showToast('Đã xóa sản phẩm khỏi giỏ hàng.');
                }, 300);
            } else {
                showToast(data.message || 'Không thể xóa sản phẩm.', 'error');
            }
        })
        .catch(() => showToast('Không thể kết nối đến máy chủ.', 'error'));
    }

    function clearCart() {
        if (!confirm('Bạn có chắc chắn muốn xóa toàn bộ giỏ hàng không?')) return;

        fetch('/cart/clear', {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        })
        .then(async response => {
            const data = await response.json();
            if (response.ok) {
                document.getElementById('cart-content-view').classList.add('hidden');
                document.getElementById('empty-cart-view').classList.remove('hidden');
                updateCartHeader(0);
                showToast('Đã xóa toàn bộ giỏ hàng.');
            } else {
                showToast(data.message || 'Có lỗi xảy ra.', 'error');
            }
        })
        .catch(() => showToast('Không thể kết nối đến máy chủ.', 'error'));
    }
</script>
@endpush
@endsection