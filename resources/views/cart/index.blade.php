@extends('layouts.app')

@section('title', 'Giỏ hàng - NovaPhone')

@section('content')
<div data-cart-page class="space-y-4">
    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-[#8b8b8b]">
        <a href="{{ route('home') }}" class="hover:text-black">Trang chủ</a>
        <span>/</span>
        <span class="text-black">Giỏ hàng</span>
    </div>

    @if (empty($items) || count($items) === 0)
        {{-- Empty Cart View --}}
        <div class="rounded-[28px] border border-[#ece8e2] bg-white p-10 text-center shadow-[0_10px_35px_rgba(0,0,0,.04)]">
            <h1 class="mt-4 text-2xl font-bold text-[#171717]">Giỏ hàng của bạn trống</h1>
            <p class="mt-2 text-[#8b8b8b]">Hãy thêm một số sản phẩm để bắt đầu mua sắm</p>
            <a href="{{ route('products.index') }}" class="mt-6 inline-block rounded-full bg-black px-8 py-3 text-sm font-semibold text-white hover:bg-[#222]">
                Tiếp tục mua sắm
            </a>
        </div>
    @else
        <div class="grid gap-4 lg:grid-cols-[1fr_380px]">
            {{-- Cart Items --}}
            <section class="rounded-[28px] border border-[#ece8e2] bg-white p-5 sm:p-6 shadow-[0_10px_35px_rgba(0,0,0,.04)]">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-[#171717]">Giỏ hàng</h1>
                        <p class="mt-1 text-sm text-[#8b8b8b]">{{ count($items) }} sản phẩm</p>
                    </div>
                    <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Xóa tất cả sản phẩm trong giỏ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs font-semibold text-red-600 hover:text-red-800">Xóa tất cả</button>
                    </form>
                </div>

                <div class="space-y-3">
                    @foreach ($items as $item)
                        @php
                            $product = $item->product ?? null;
                            $variant = $item->variant ?? null;
                            $thumbnail = $variant?->image ?: $product?->thumbnail;
                            $image = $product ? (
                                $thumbnail
                                    ? (str_starts_with($thumbnail, 'http')
                                        ? $thumbnail
                                        : ((str_starts_with($thumbnail, 'images/') || str_starts_with($thumbnail, 'storage/'))
                                            ? asset($thumbnail)
                                            : asset('storage/' . ltrim($thumbnail, '/'))))
                                    : asset('images/placeholder.svg')
                            ) : asset('images/placeholder.svg');
                            $itemTotal = $item->price * $item->quantity;
                            $productUnavailable = ! $product
                                || $product->trashed()
                                || ! $product->is_active
                                || ($item->variant_id && ! $variant)
                                || ($variant && ! $variant->is_active);
                            $unavailableMessage = ! $product
                                ? 'Sản phẩm không còn tồn tại.'
                                : (($product->trashed() || ! $product->is_active)
                                    ? 'Sản phẩm đã ngừng bán.'
                                    : (($item->variant_id && ! $variant)
                                        ? 'Biến thể sản phẩm không còn tồn tại.'
                                        : 'Biến thể sản phẩm đã ngừng bán.'));
                        @endphp
                        <div
                            data-cart-item
                            data-cart-item-unavailable="{{ $productUnavailable ? 'true' : 'false' }}"
                            data-item-id="{{ $item->display_id }}"
                            data-update-url="{{ route('cart.update', $item->display_id) }}"
                            data-unit-price="{{ (float) $item->price }}"
                            data-confirmed-quantity="{{ $item->quantity }}"
                            class="flex gap-4 rounded-[20px] border p-4 transition hover:shadow-md {{ $productUnavailable ? 'border-amber-200 bg-amber-50/40' : 'border-[#ece8e2]' }}"
                        >
                            <input type="checkbox" @checked(! $productUnavailable) @disabled($productUnavailable) class="mt-1 size-4 rounded border-[#d8d4cd] accent-black disabled:cursor-not-allowed disabled:opacity-50" data-cart-selection data-item-id="{{ $item->display_id }}">

                            <div class="flex h-24 w-24 flex-shrink-0 items-center justify-center rounded-[16px] bg-[#fbfaf8]">
                                <img src="{{ $image }}" class="max-h-full max-w-full object-contain" alt="{{ $product->name ?? 'Product' }}" loading="lazy" decoding="async">
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        @if ($product && ! $productUnavailable)
                                            <a href="{{ route('products.show', $product) }}" class="text-sm font-semibold text-[#171717] hover:text-black">
                                                {{ $product->name }}
                                            </a>
                                        @elseif ($product)
                                            <span class="text-sm font-semibold text-[#171717]">{{ $product->name }}</span>
                                        @else
                                            <span class="text-sm font-semibold text-[#171717]">Sản phẩm không còn khả dụng</span>
                                        @endif
                                        @if ($productUnavailable)
                                            <p class="mt-2 text-xs font-semibold text-amber-700" role="alert">
                                                ⚠ {{ $unavailableMessage }} Vui lòng xóa sản phẩm này khỏi giỏ trước khi thanh toán.
                                            </p>
                                        @endif
                                        @if ($variant)
                                            <p class="mt-1 text-xs text-[#8b8b8b]">
                                                @if ($variant->color) Màu: {{ $variant->color }}@endif
                                                @if ($variant->storage) | Dung lượng: {{ $variant->storage }}@endif
                                            </p>
                                        @endif
                                        @if ($product && $product->rating_average)
                                            <p class="mt-2 text-sm font-semibold text-[#f59e0b]">
                                                {{ number_format($product->rating_average, 1) }}
                                            </p>
                                        @endif
                                    </div>
                                    <form action="{{ route('cart.destroy', $item->display_id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-[#8b8b8b] hover:text-red-600">X</button>
                                    </form>
                                </div>
                            </div>

                            <div class="flex flex-col items-end gap-3">
                                <div class="text-right">
                                    <div class="text-lg font-bold text-[#111]">{{ number_format($item->price, 0, ',', '.') }}đ</div>
                                    @if ($product && $product->sale_price && $product->sale_price < $product->price)
                                        <div class="text-xs text-[#999] line-through">{{ number_format($product->price, 0, ',', '.') }}đ</div>
                                    @endif
                                    <p class="mt-1 text-xs text-[#8b8b8b]">
                                        Tạm tính
                                        <span data-cart-item-subtotal data-cart-item-subtotal-raw="{{ $itemTotal }}" class="font-semibold text-[#333]">{{ number_format($itemTotal, 0, ',', '.') }}đ</span>
                                    </p>
                                </div>

                                @if ($productUnavailable)
                                    <p class="text-right text-xs font-medium text-amber-700">Không thể thay đổi số lượng</p>
                                @else
                                    <div data-cart-quantity-controls class="flex items-center gap-2 rounded-[12px] border border-[#ece8e2]">
                                        <button type="button" data-cart-quantity-change data-quantity-delta="-1" @disabled($item->quantity <= 1) class="px-3 py-1.5 text-[#8b8b8b] transition hover:text-black disabled:cursor-not-allowed disabled:opacity-35" aria-label="Giảm số lượng">-</button>
                                        <input type="number" value="{{ $item->quantity }}" min="1" inputmode="numeric" class="w-10 border-0 bg-transparent text-center text-sm font-semibold outline-none" data-cart-quantity-input data-item-id="{{ $item->display_id }}" aria-label="Số lượng">
                                        <button type="button" data-cart-quantity-change data-quantity-delta="1" class="px-3 py-1.5 text-[#8b8b8b] transition hover:text-black disabled:cursor-not-allowed disabled:opacity-35" aria-label="Tăng số lượng">+</button>
                                    </div>
                                @endif
                                <p data-cart-item-status class="min-h-4 text-right text-[11px] text-[#8b8b8b]" aria-live="polite"></p>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Continue Shopping --}}
                <div class="mt-6 border-t border-[#ece8e2] pt-4">
                    <a href="{{ route('products.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800">Tiếp tục mua sắm</a>
                </div>
            </section>

            {{-- Order Summary --}}
            <aside class="h-fit rounded-[28px] border border-[#ece8e2] bg-white p-5 shadow-[0_10px_35px_rgba(0,0,0,.04)]">
                <h2 class="text-lg font-bold text-[#171717]">Đơn hàng của bạn</h2>

                <div class="mt-4 space-y-3 border-b border-[#ece8e2] pb-4 text-sm">
                    <div class="flex justify-between">
                        <span class="text-[#8b8b8b]">Tạm tính</span>
                        <span data-cart-total data-cart-total-raw="{{ $total }}" class="font-semibold text-[#111]">{{ number_format($total, 0, ',', '.') }}đ</span>
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-between border-b border-[#ece8e2] pb-4">
                    <span class="text-base font-bold text-[#111]">Tổng cộng</span>
                    <div data-cart-total data-cart-total-raw="{{ $total }}" class="text-2xl font-extrabold text-[#111]">{{ number_format($total, 0, ',', '.') }}đ</div>
                </div>

                <div class="mt-4 space-y-3">
                    <form action="{{ route('cart.set-selection') }}" method="POST" id="cart-selection-form">
                        @csrf
                        <div id="cart-selection-fields"></div>
                        <button type="submit" class="block w-full rounded-full bg-black px-5 py-3 text-center text-sm font-semibold text-white transition-all hover:bg-[#222]">
                            Tiếp tục thanh toán
                        </button>
                    </form>
                </div>

                {{-- Promo Code --}}
                <div class="mt-4 space-y-2">
                    <label class="text-xs font-semibold text-[#8b8b8b]">Mã khuyến mãi</label>
                    <form action="{{ route('checkout.apply-coupon') }}" method="POST" class="flex gap-2">
                        @csrf
                        <input
                            type="text"
                            name="code"
                            placeholder="Nhập mã..."
                            class="flex-1 rounded-[12px] border border-[#ece8e2] bg-[#fbfaf8] px-3 py-2 text-xs outline-none transition focus:border-black"
                        >
                        <button type="submit" class="rounded-[12px] bg-[#f0eeea] px-3 py-2 text-xs font-semibold text-[#111] transition hover:bg-[#e5e0d7]">
                            Áp dụng
                        </button>
                    </form>
                </div>

                {{-- Benefits --}}
                <div class="mt-4 grid grid-cols-1 gap-2 text-center text-[10px] font-semibold text-[#8b8b8b]">
                    <div class="rounded-[12px] border border-[#ece8e2] bg-[#fbfaf8] p-2">Hàng chính hãng 100%</div>
                    <div class="rounded-[12px] border border-[#ece8e2] bg-[#fbfaf8] p-2">Đổi trả 30 ngày</div>
                    <div class="rounded-[12px] border border-[#ece8e2] bg-[#fbfaf8] p-2">Bảo mật thanh toán</div>
                </div>
            </aside>
        </div>
    @endif
</div>

<script>
    const selectionForm = document.getElementById('cart-selection-form');
    const selectionFields = document.getElementById('cart-selection-fields');

    selectionForm?.addEventListener('submit', function () {
        selectionFields.innerHTML = '';
        document.querySelectorAll('[data-cart-selection]:checked').forEach(checkbox => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_item_ids[]';
            input.value = checkbox.dataset.itemId;
            selectionFields.appendChild(input);
        });
    });

    document.querySelectorAll('.qty-minus').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.closest('.qty-wrap')?.querySelector('input[type="number"]');
            if (input && input.value > 1) {
                input.value = parseInt(input.value) - 1;
                updateQuantity(input);
            }
        });
    });

    document.querySelectorAll('.qty-plus').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.closest('.qty-wrap')?.querySelector('input[type="number"]');
            if (input) {
                input.value = parseInt(input.value) + 1;
                updateQuantity(input);
            }
        });
    });

    function updateQuantity(input) {
        const itemId = input.dataset.itemId;
        const quantity = parseInt(input.value);

        fetch(`{{ route('cart.update', ':id') }}`.replace(':id', itemId), {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ quantity })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                window.dispatchEvent(new CustomEvent('nova-toast', {
                    detail: { type: 'error', message: data.message || 'Lỗi cập nhật giỏ hàng' }
                }));
            }
        })
        .catch(() => {
            window.dispatchEvent(new CustomEvent('nova-toast', {
                detail: { type: 'error', message: 'Lỗi cập nhật giỏ hàng' }
            }));
        });
    }
</script>
@endsection
