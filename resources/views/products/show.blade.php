@extends('layouts.app')

@section('title', $detail['name'].' | NovaPhone')

@php
    $money = fn ($value) => number_format((float) $value, 0, ',', '.').'đ';
    $mainImage = $detail['images']->first()['url'];
    $storageOptions = $detail['variants']->pluck('storage')->filter()->unique()->values();
    $colorOptions = $detail['variants']->map(fn ($variant) => [
        'name' => $variant['color'],
        'code' => $variant['color_code'] ?: '#9ca3af',
    ])->filter(fn ($color) => filled($color['name']))->unique('name')->values();
@endphp

@section('content')
<style>
    .product-detail-grid {
        display: grid;
        gap: 1.5rem;
    }

    .product-detail-media-column {
        max-width: 680px;
    }

    .product-detail-image-frame {
        height: 300px;
    }

    .product-detail-main-image {
        max-height: 260px;
        width: 100%;
        object-fit: contain;
    }

    .product-detail-thumb {
        width: 56px;
        height: 56px;
    }

    .product-detail-thumb img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    @media (min-width: 1024px) {
        .product-detail-grid {
            grid-template-columns: minmax(0, 680px) minmax(360px, 1fr);
        }
    }

    @media (max-width: 640px) {
        .product-detail-image-frame {
            height: 240px;
        }

        .product-detail-main-image {
            max-height: 210px;
        }
    }
</style>

<div class="bg-night text-gray-100">
    <section class="mx-auto max-w-7xl px-4 py-5 sm:px-6">
        <nav class="flex flex-wrap items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('home') }}" class="transition-colors hover:text-brand-400">Trang chủ</a>
            <span>/</span>
            <span>{{ $detail['brand']['name'] ?? 'Điện thoại' }}</span>
            <span>/</span>
            <span class="font-medium text-gray-300">{{ $detail['name'] }}</span>
        </nav>

        <div class="mt-5">
            <h1 class="text-2xl font-extrabold tracking-tight text-white">{{ $detail['name'] }}</h1>
            <div class="mt-3 flex flex-wrap items-center gap-3 text-sm">
                <span class="text-gray-500">Đã bán {{ number_format($detail['sold_count']) }}</span>
                <span class="text-gray-700">•</span>
                <span class="inline-flex items-center gap-1 font-semibold text-amber-400">
                <svg class="size-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.05 2.93c.3-.92 1.6-.92 1.9 0l1.07 3.29a1 1 0 0 0 .95.69h3.46c.97 0 1.37 1.24.59 1.81l-2.8 2.03a1 1 0 0 0-.36 1.12l1.07 3.29c.3.92-.76 1.69-1.54 1.12l-2.8-2.03a1 1 0 0 0-1.18 0l-2.8 2.03c-.78.57-1.84-.2-1.54-1.12l1.07-3.29a1 1 0 0 0-.36-1.12L3 8.72c-.78-.57-.38-1.8.59-1.8h3.46a1 1 0 0 0 .95-.7l1.07-3.29Z"/>
                </svg>
                {{ $detail['rating']['average'] ?: 'Chưa có' }}
                </span>
                <span class="text-gray-700">•</span>
                <a href="#specifications" class="font-semibold text-brand-400 transition-colors hover:text-brand-300">Thông số</a>
                <span class="text-gray-700">•</span>
                <a href="#reviews" class="font-semibold text-brand-400 transition-colors hover:text-brand-300">Đánh giá</a>
            </div>
        </div>
    </section>

    <section class="product-detail-grid mx-auto max-w-7xl px-4 pb-10 sm:px-6">
        <div class="product-detail-media-column space-y-5">
            <section class="rounded-2xl border border-white/5 bg-night-soft p-4 shadow-xl shadow-black/30">
                <div class="product-detail-image-frame relative flex items-center justify-center overflow-hidden rounded-xl bg-night-card">
                    <img src="{{ $mainImage }}"
                         alt="{{ $detail['name'] }}"
                         data-gallery-main
                         class="product-detail-main-image px-6 py-4">
                </div>

                <div class="mt-4 flex items-center gap-3">
                    <button type="button" data-gallery-prev class="flex size-10 shrink-0 items-center justify-center rounded-full border border-white/10 bg-white/5 text-gray-400 transition hover:border-brand-400 hover:bg-brand-600/15 hover:text-brand-300" aria-label="Ảnh trước">
                        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/>
                        </svg>
                    </button>

                    <div class="no-scrollbar flex min-w-0 flex-1 gap-3 overflow-x-auto pb-1">
                        @foreach ($detail['images'] as $image)
                            <button type="button"
                                    data-gallery-thumb
                                    data-gallery-index="{{ $loop->index }}"
                                    data-gallery-src="{{ $image['url'] }}"
                                    class="product-detail-thumb shrink-0 overflow-hidden rounded-lg border {{ $loop->first ? 'border-brand-500' : 'border-white/10' }} bg-night-card p-1">
                                <img src="{{ $image['url'] }}" alt="{{ $detail['name'] }} ảnh {{ $loop->iteration }}">
                            </button>
                        @endforeach
                    </div>

                    <button type="button" data-gallery-next class="flex size-10 shrink-0 items-center justify-center rounded-full border border-white/10 bg-white/5 text-gray-400 transition hover:border-brand-400 hover:bg-brand-600/15 hover:text-brand-300" aria-label="Ảnh sau">
                        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
                        </svg>
                    </button>
                </div>
            </section>

            <section class="rounded-2xl border border-white/5 bg-night-soft p-5 shadow-xl shadow-black/30">
                <h2 class="text-lg font-extrabold text-white">NovaPhone cam kết</h2>
                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    @foreach ([
                        ['title' => 'Hàng chính hãng', 'desc' => 'Bảo hành theo tiêu chuẩn nhà sản xuất'],
                        ['title' => 'Giao hàng nhanh', 'desc' => 'Nhận hàng tại nhà hoặc cửa hàng'],
                        ['title' => 'Trả góp linh hoạt', 'desc' => 'Hỗ trợ trả góp 0% theo kỳ hạn'],
                        ['title' => 'Đổi trả dễ dàng', 'desc' => 'Hỗ trợ đổi trả theo chính sách'],
                    ] as $commitment)
                        <div class="rounded-xl border border-white/10 bg-white/[0.03] p-4">
                            <p class="font-semibold text-white">{{ $commitment['title'] }}</p>
                            <p class="mt-1 text-sm text-gray-400">{{ $commitment['desc'] }}</p>
                        </div>
                    @endforeach
                </div>
            </section>

            <section id="specifications" class="rounded-2xl border border-white/5 bg-night-soft p-5 shadow-xl shadow-black/30">
                <h2 class="text-lg font-extrabold text-white">Thông số kỹ thuật</h2>
                <dl class="mt-4 overflow-hidden rounded-xl border border-white/10">
                    @foreach ($detail['specifications'] as $spec)
                        <div class="grid grid-cols-[140px_1fr] gap-4 border-b border-white/10 px-4 py-3 last:border-b-0">
                            <dt class="text-sm text-gray-500">{{ $spec['label'] }}</dt>
                            <dd class="text-sm font-semibold text-gray-100">{{ $spec['value'] }}</dd>
                        </div>
                    @endforeach
                </dl>
            </section>
        </div>

        <aside class="space-y-4">
            <div class="overflow-hidden rounded-xl border border-brand-500/30 bg-gradient-to-r from-brand-950 via-brand-900 to-cyan-950 px-5 py-4 shadow-xl shadow-black/30">
                <p class="text-sm font-black uppercase text-brand-200">Ưu đãi NovaPhone</p>
                <p class="mt-1 text-3xl font-black text-white">Giảm thêm 300.000đ</p>
            </div>

            <div class="overflow-hidden rounded-xl border border-amber-400/30 bg-gradient-to-r from-amber-500 to-orange-500 px-5 py-4 text-gray-950 shadow-xl shadow-black/30">
                <p class="text-lg font-black">Online Giá Rẻ Quá</p>
                <p class="mt-1 text-sm font-semibold text-gray-900">Ưu đãi áp dụng số lượng có hạn</p>
            </div>

            <section class="rounded-2xl border border-white/5 bg-night-soft p-5 shadow-xl shadow-black/30">
                @if ($storageOptions->isNotEmpty())
                    <div>
                        <p class="mb-2 text-sm font-semibold text-gray-300">Dung lượng</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($storageOptions as $storage)
                                <button type="button"
                                        data-storage="{{ $storage }}"
                                        class="storage-btn rounded-lg border px-5 py-3 text-sm font-semibold transition cursor-pointer {{ $loop->first ? 'border-brand-500 bg-brand-600/15 text-brand-200' : 'border-white/10 bg-white/[0.03] text-gray-200 hover:border-brand-400' }}">
                                    {{ $storage }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($colorOptions->isNotEmpty())
                    <div class="mt-5">
                        <p class="mb-2 text-sm font-semibold text-gray-300">Màu sắc</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($colorOptions as $color)
                                <button type="button"
                                        data-color="{{ $color['name'] }}"
                                        class="color-btn inline-flex items-center gap-2 rounded-full border px-3 py-2 text-sm transition cursor-pointer {{ $loop->first ? 'border-brand-500 bg-brand-600/15 text-brand-200' : 'border-white/10 bg-white/[0.03] text-gray-200 hover:border-brand-400' }}">
                                    <span class="size-4 rounded-full border border-white/20" style="background: {{ $color['code'] }}"></span>
                                    {{ $color['name'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="mt-5 border-t border-white/10 pt-5">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-500">Giá tại Thành phố Hồ Chí Minh</p>
                        <span id="stock-status-text" class="text-xs font-bold text-emerald-400">Còn hàng</span>
                    </div>
                    <div class="mt-2 flex flex-wrap items-baseline gap-3">
                        <span id="price-display" class="text-3xl font-black text-brand-400">{{ $money($detail['effective_price']) }}</span>
                        @if ($detail['sale_price'])
                            <span id="old-price-display" class="text-base text-gray-500 line-through">{{ $money($detail['price']) }}</span>
                        @endif
                        @if ($detail['discount_percent'])
                            <span id="discount-display" class="rounded-full bg-red-600 px-3 py-1 text-xs font-bold text-white">
                                -{{ $detail['discount_percent'] }}%
                            </span>
                        @endif
                    </div>
                    <p class="mt-2 text-sm leading-6 text-gray-400">{{ $detail['description'] ?? 'Sản phẩm chính hãng tại NovaPhone.' }}</p>
                </div>

                <div class="mt-5 rounded-xl border border-brand-500/30 bg-brand-600/10 p-4">
                    <p class="font-bold text-brand-200">Đăng nhập để nhận Voucher đến 6%</p>
                    <p class="mt-1 text-sm text-gray-400">Áp dụng cho khách hàng NovaPhone.</p>
                </div>

                {{-- Form AJAX --}}
                <form id="cart-form" class="mt-5">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $detail['id'] }}">
                    <input type="hidden" name="variant_id" id="hidden-variant-id" value="">
                    <input type="hidden" name="quantity" value="1">
                    
                    <div class="grid gap-3">
                        <button type="button" id="buy-now-btn" class="rounded-xl bg-gradient-to-r from-amber-400 to-amber-500 px-5 py-3.5 text-base font-black text-gray-950 shadow-lg shadow-amber-500/20 transition hover:-translate-y-0.5 cursor-pointer">
                            Mua ngay
                        </button>
                        <button type="button" id="add-to-cart-btn" class="rounded-xl border border-brand-500/40 bg-brand-600/15 px-5 py-3.5 text-base font-black text-brand-200 transition hover:bg-brand-600 hover:text-white cursor-pointer">
                            Thêm vào giỏ hàng
                        </button>
                    </div>
                </form>
            </section>

            <section id="reviews" class="rounded-2xl border border-white/5 bg-night-soft p-5 shadow-xl shadow-black/30">
                <h2 class="text-lg font-extrabold text-white">Đánh giá khách hàng</h2>
                <div class="mt-3 flex items-end gap-3">
                    <span class="text-4xl font-black text-white">{{ $detail['rating']['average'] ?: '0.0' }}</span>
                    <span class="pb-1 text-sm text-gray-500">/ 5 từ {{ $detail['rating']['count'] }} đánh giá</span>
                </div>

                @auth
                    <form id="review-form"
                          action="{{ route('products.review.store', $product) }}"
                          method="POST"
                          class="mt-5 rounded-xl border border-white/10 bg-white/[0.03] p-4">
                        @csrf
                        <fieldset>
                            <legend class="text-sm font-semibold text-gray-200">Bạn đánh giá sản phẩm này thế nào?</legend>
                            <div class="mt-3 flex flex-row-reverse justify-end gap-1" data-rating-picker>
                                @for ($rating = 5; $rating >= 1; $rating--)
                                    <input type="radio"
                                           id="review-rating-{{ $rating }}"
                                           name="rating"
                                           value="{{ $rating }}"
                                           class="peer sr-only"
                                           required>
                                    <label for="review-rating-{{ $rating }}"
                                           class="cursor-pointer text-3xl text-gray-600 transition peer-checked:text-amber-400 peer-hover:text-amber-300 hover:text-amber-300"
                                           title="{{ $rating }} sao"
                                           aria-label="{{ $rating }} sao">
                                        ★
                                    </label>
                                @endfor
                            </div>
                        </fieldset>

                        <label for="review-comment" class="mt-4 block text-sm font-semibold text-gray-200">Nhận xét</label>
                        <textarea id="review-comment"
                                  name="comment"
                                  rows="4"
                                  maxlength="5000"
                                  placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm..."
                                  class="mt-2 w-full rounded-xl border border-white/10 bg-night-card px-3 py-2.5 text-sm text-white outline-none transition placeholder:text-gray-600 focus:border-brand-500"></textarea>

                        <p id="review-form-message" class="mt-3 hidden text-sm" role="alert"></p>

                        <button type="submit"
                                id="review-submit-btn"
                                class="mt-4 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-brand-500 disabled:cursor-not-allowed disabled:opacity-60">
                            Gửi đánh giá
                        </button>
                        <p class="mt-2 text-xs text-gray-500">Chỉ tài khoản đã nhận hàng và thanh toán thành công mới có thể đánh giá.</p>
                    </form>
                @else
                    <div class="mt-5 rounded-xl border border-white/10 bg-white/[0.03] p-4 text-sm text-gray-400">
                        <a href="{{ route('login') }}" class="font-bold text-brand-400 transition hover:text-brand-300">Đăng nhập</a>
                        để đánh giá sản phẩm đã mua.
                    </div>
                @endauth

                <div id="review-list" class="mt-4 space-y-3">
                    @forelse ($detail['reviews']->take(3) as $review)
                        <article class="border-t border-white/10 pt-3">
                            <div class="flex items-center justify-between">
                                <p class="font-semibold text-white">{{ $review['user']['name'] ?? 'Khách hàng' }}</p>
                                <span class="text-xs text-gray-500">{{ $review['created_at'] }}</span>
                            </div>
                            <p class="mt-1 text-sm text-amber-400" aria-label="{{ $review['rating'] }} trên 5 sao">
                                {{ str_repeat('★', $review['rating']) }}<span class="text-gray-700">{{ str_repeat('★', 5 - $review['rating']) }}</span>
                            </p>
                            <p class="mt-1 text-sm text-gray-400">{{ $review['comment'] ?: 'Khách hàng chưa để lại nội dung nhận xét.' }}</p>
                        </article>
                    @empty
                        <p class="mt-3 text-sm text-gray-500">Chưa có đánh giá nào cho sản phẩm này.</p>
                    @endforelse
                </div>
            </section>
        </aside>
    </section>
</div>

{{-- Container cho Toast alert --}}
<div id="toast-container" class="fixed bottom-5 right-5 z-50 flex flex-col gap-3"></div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // ---------- 1. Bộ sưu tập ảnh (Gallery) ----------
        const mainImage = document.querySelector('[data-gallery-main]');
        const thumbs = Array.from(document.querySelectorAll('[data-gallery-thumb]'));
        const previous = document.querySelector('[data-gallery-prev]');
        const next = document.querySelector('[data-gallery-next]');

        if (mainImage && thumbs.length > 0) {
            let currentIndex = 0;

            function setActiveImage(index) {
                currentIndex = (index + thumbs.length) % thumbs.length;
                const activeThumb = thumbs[currentIndex];
                mainImage.src = activeThumb.dataset.gallerySrc;

                thumbs.forEach((thumb) => {
                    thumb.classList.remove('border-brand-500');
                    thumb.classList.add('border-white/10');
                });

                activeThumb.classList.remove('border-white/10');
                activeThumb.classList.add('border-brand-500');
                activeThumb.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
            }

            thumbs.forEach((thumb) => {
                thumb.addEventListener('click', () => setActiveImage(Number(thumb.dataset.galleryIndex)));
            });

            previous?.addEventListener('click', () => setActiveImage(currentIndex - 1));
            next?.addEventListener('click', () => setActiveImage(currentIndex + 1));
        }

        // ---------- 2. Xử lý Variant, Giá, Tồn kho ----------
        const variants = @json($detail['variants']);
        const basePrice = {{ (float) $detail['effective_price'] }};
        const hasVariants = variants.length > 0;

        let selectedStorage = null;
        let selectedColor = null;

        const storageButtons = document.querySelectorAll('.storage-btn');
        const colorButtons = document.querySelectorAll('.color-btn');
        const priceDisplay = document.getElementById('price-display');
        const oldPriceDisplay = document.getElementById('old-price-display');
        const discountDisplay = document.getElementById('discount-display');
        const stockStatusText = document.getElementById('stock-status-text');
        const hiddenVariantInput = document.getElementById('hidden-variant-id');
        const buyNowBtn = document.getElementById('buy-now-btn');
        const addToCartBtn = document.getElementById('add-to-cart-btn');

        function formatMoney(amount) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' })
                .format(amount)
                .replace(/\s?₫/, 'đ');
        }

        function updateSelectedVariant() {
            if (!hasVariants) {
                // Sản phẩm không có biến thể
                const baseStock = {{ $detail['inventory']['available_quantity'] ?? 0 }};
                hiddenVariantInput.value = "";
                updateStockUI(baseStock);
                return;
            }

            // Tìm biến thể khớp với dung lượng & màu sắc đang chọn
            const matched = variants.find(v => 
                (!selectedStorage || v.storage === selectedStorage) &&
                (!selectedColor || v.color === selectedColor)
            );

            if (matched) {
                hiddenVariantInput.value = matched.id;
                
                // Cập nhật giá bán = giá gốc + giá thêm của biến thể
                const currentPrice = basePrice + matched.additional_price;
                priceDisplay.textContent = formatMoney(currentPrice);
                
                if (oldPriceDisplay) {
                    const originalPrice = {{ (float) $detail['price'] }} + matched.additional_price;
                    oldPriceDisplay.textContent = formatMoney(originalPrice);
                }

                updateStockUI(matched.available_quantity);
            } else {
                hiddenVariantInput.value = "";
                priceDisplay.textContent = formatMoney(basePrice);
                updateStockUI(0); // Không khớp -> Coi như hết hàng
            }
        }

        function updateStockUI(availableQty) {
            if (availableQty > 0) {
                stockStatusText.textContent = `Còn hàng (${availableQty} máy)`;
                stockStatusText.className = "text-xs font-bold text-emerald-400";
                buyNowBtn.disabled = false;
                addToCartBtn.disabled = false;
                buyNowBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                addToCartBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                stockStatusText.textContent = "Hết hàng tạm thời";
                stockStatusText.className = "text-xs font-bold text-red-400";
                buyNowBtn.disabled = true;
                addToCartBtn.disabled = true;
                buyNowBtn.classList.add('opacity-50', 'cursor-not-allowed');
                addToCartBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }

        // Chọn ban đầu
        if (hasVariants) {
            const firstVariant = variants[0];
            selectedStorage = firstVariant.storage;
            selectedColor = firstVariant.color;
            hiddenVariantInput.value = firstVariant.id;
        }
        updateSelectedVariant();

        // Lắng nghe sự kiện click dung lượng
        storageButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                selectedStorage = btn.dataset.storage;
                
                storageButtons.forEach(b => {
                    b.className = "storage-btn rounded-lg border px-5 py-3 text-sm font-semibold transition cursor-pointer border-white/10 bg-white/[0.03] text-gray-200 hover:border-brand-400";
                });
                btn.className = "storage-btn rounded-lg border px-5 py-3 text-sm font-semibold transition cursor-pointer border-brand-500 bg-brand-600/15 text-brand-200";
                
                updateSelectedVariant();
            });
        });

        // Lắng nghe sự kiện click màu sắc
        colorButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                selectedColor = btn.dataset.color;

                colorButtons.forEach(b => {
                    b.className = "color-btn inline-flex items-center gap-2 rounded-full border px-3 py-2 text-sm transition cursor-pointer border-white/10 bg-white/[0.03] text-gray-200 hover:border-brand-400";
                });
                btn.className = "color-btn inline-flex items-center gap-2 rounded-full border px-3 py-2 text-sm transition cursor-pointer border-brand-500 bg-brand-600/15 text-brand-200";

                updateSelectedVariant();
            });
        });

        // ---------- 3. Xử lý Thêm vào giỏ & Mua ngay bằng AJAX ----------
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

        function addToCart(callback = null) {
            const form = document.getElementById('cart-form');
            const formData = new FormData(form);

            fetch("{{ route('cart.store') }}", {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': formData.get('_token')
                },
                body: formData
            })
            .then(async response => {
                const data = await response.json();
                if (response.ok) {
                    // Cập nhật giỏ hàng trên header
                    const badge = document.getElementById('cart-count-badge');
                    if (badge) {
                        badge.textContent = data.cart_count;
                        badge.classList.toggle('hidden', !(data.cart_count > 0));
                        badge.classList.toggle('flex', data.cart_count > 0);
                    }
                    
                    if (callback) {
                        callback();
                    } else {
                        showToast('Đã thêm sản phẩm vào giỏ hàng thành công!');
                    }
                } else {
                    showToast(data.message || 'Thêm vào giỏ hàng thất bại.', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Không thể kết nối đến máy chủ.', 'error');
            });
        }

        addToCartBtn.addEventListener('click', () => addToCart());

        buyNowBtn.addEventListener('click', () => {
            addToCart(() => {
                window.location.href = "{{ route('checkout') }}";
            });
        });

        // ---------- 4. Gửi đánh giá sản phẩm ----------
        const reviewForm = document.getElementById('review-form');

        if (reviewForm) {
            const ratingInputs = Array.from(reviewForm.querySelectorAll('input[name="rating"]'));
            const ratingLabels = Array.from(reviewForm.querySelectorAll('[data-rating-picker] label'));
            const message = document.getElementById('review-form-message');
            const submitButton = document.getElementById('review-submit-btn');

            function paintRating(selectedRating = 0) {
                ratingLabels.forEach((label) => {
                    const rating = Number(label.getAttribute('for').replace('review-rating-', ''));
                    label.classList.toggle('text-amber-400', rating <= selectedRating);
                    label.classList.toggle('text-gray-600', rating > selectedRating);
                });
            }

            function showReviewMessage(text, type = 'error') {
                message.textContent = text;
                message.classList.remove('hidden', 'text-red-400', 'text-emerald-400');
                message.classList.add(type === 'success' ? 'text-emerald-400' : 'text-red-400');
            }

            ratingInputs.forEach((input) => {
                input.addEventListener('change', () => paintRating(Number(input.value)));
            });

            reviewForm.addEventListener('submit', async (event) => {
                event.preventDefault();

                const formData = new FormData(reviewForm);
                const rating = formData.get('rating');

                if (!rating) {
                    showReviewMessage('Vui lòng chọn số sao trước khi gửi.');
                    return;
                }

                submitButton.disabled = true;
                submitButton.textContent = 'Đang gửi...';
                message.classList.add('hidden');

                try {
                    const response = await fetch(reviewForm.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': formData.get('_token'),
                        },
                        body: formData,
                    });
                    const data = await response.json();

                    if (!response.ok) {
                        const validationMessage = data.errors
                            ? Object.values(data.errors).flat()[0]
                            : null;
                        throw new Error(validationMessage || data.message || 'Không thể gửi đánh giá.');
                    }

                    showReviewMessage(data.message || 'Đánh giá sản phẩm thành công.', 'success');
                    submitButton.textContent = 'Đã gửi đánh giá';
                    window.setTimeout(() => window.location.reload(), 800);
                } catch (error) {
                    showReviewMessage(error.message || 'Không thể kết nối đến máy chủ.');
                    submitButton.disabled = false;
                    submitButton.textContent = 'Gửi đánh giá';
                }
            });
        }
    });
</script>
@endsection
