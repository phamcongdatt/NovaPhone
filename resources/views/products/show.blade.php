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
            <h1 class="text-2xl font-extrabold tracking-tight text-white flex items-center gap-2">
                {{ $detail['name'] }}
                @if ($product->activeFlashSaleItem !== null)
                    <span class="inline-flex rounded-lg bg-gradient-to-r from-orange-500 to-red-600 px-2 py-1 text-xs font-bold text-white shadow-sm items-center gap-1 w-max">
                        <svg class="size-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M13 2.05v9.45h5.5l-9.5 10.5v-9.5H3.5l9.5-10.45z"/></svg>
                        Flash Sale
                    </span>
                @endif
            </h1>
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
                                <img src="{{ $image['url'] }}" alt="{{ $detail['name'] }} ảnh {{ $loop->iteration }}" loading="lazy">
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
                <form id="cart-form" method="POST" action="{{ route('cart.buy-now') }}" class="mt-5">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $detail['id'] }}">
                    <input type="hidden" name="variant_id" id="hidden-variant-id" value="">
                    <input type="hidden" name="quantity" value="1">
                    
                    <div class="grid gap-3">
                        <button type="submit" id="buy-now-btn" class="rounded-xl bg-gradient-to-r from-amber-400 to-amber-500 px-5 py-3.5 text-base font-black text-gray-950 shadow-lg shadow-amber-500/20 transition hover:-translate-y-0.5 cursor-pointer">
                            Mua ngay
                        </button>
                        <button type="button" id="add-to-cart-btn" class="rounded-xl border border-brand-500/40 bg-brand-600/15 px-5 py-3.5 text-base font-black text-brand-200 transition hover:bg-brand-600 hover:text-white cursor-pointer">
                            Thêm vào giỏ hàng
                        </button>
                        @php($isCompared = in_array($detail['id'], $compareProductIds ?? [], true))
                        <button type="button"
                                data-compare-toggle="{{ $detail['id'] }}"
                                data-compared="{{ $isCompared ? 'true' : 'false' }}"
                                aria-label="{{ $isCompared ? 'Xóa khỏi so sánh' : 'Thêm vào so sánh' }}"
                                class="rounded-xl border border-white/10 bg-white/[0.03] px-5 py-3 text-sm font-bold text-gray-200 transition hover:border-brand-500/50 hover:bg-brand-600/10 hover:text-brand-200">
                            <span data-compare-label>{{ $isCompared ? 'Xóa khỏi so sánh' : 'So sánh sản phẩm' }}</span>
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

                <div class="mt-4 space-y-2" aria-label="Phân bố điểm đánh giá">
                    @foreach (range(5, 1) as $star)
                        @php
                            $ratingCount = $detail['rating']['breakdown'][$star] ?? 0;
                            $ratingPercent = $detail['rating']['count'] > 0
                                ? round(($ratingCount / $detail['rating']['count']) * 100)
                                : 0;
                        @endphp
                        <div class="grid grid-cols-[2.5rem_1fr_2.5rem] items-center gap-2 text-xs">
                            <span class="font-semibold text-gray-400">{{ $star }} ★</span>
                            <div class="h-2 overflow-hidden rounded-full bg-white/10">
                                <div class="h-full rounded-full bg-amber-400 transition-all"
                                     style="width: {{ $ratingPercent }}%"
                                     role="progressbar"
                                     aria-valuenow="{{ $ratingPercent }}"
                                     aria-valuemin="0"
                                     aria-valuemax="100"></div>
                            </div>
                            <span class="text-right text-gray-500">{{ $ratingCount }}</span>
                        </div>
                    @endforeach
                </div>

                @auth
                    @if ($reviewStatus === 'eligible')
                        <form id="review-form" method="POST" action="{{ route('products.review.store', $detail['id']) }}"
                              class="relative mt-6 overflow-hidden rounded-2xl border border-brand-500/20 bg-gradient-to-br from-brand-950/40 via-night-card to-night-card p-5 shadow-lg shadow-black/20">
                            @csrf
                            <input type="hidden" name="order_id" value="{{ $reviewOrderId }}">
                            <div class="pointer-events-none absolute -right-16 -top-20 size-48 rounded-full bg-brand-500/10 blur-3xl"></div>
                            <div class="relative flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <span class="flex size-10 shrink-0 items-center justify-center rounded-xl border border-brand-500/20 bg-brand-600/15 text-brand-300">
                                        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.5a.56.56 0 0 1 1.04 0l2.125 5.111a.56.56 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.56.56 0 0 0-.182.557l1.285 5.385a.56.56 0 0 1-.84.61l-4.725-2.885a.56.56 0 0 0-.586 0L6.982 20.54a.56.56 0 0 1-.84-.61l1.285-5.386a.56.56 0 0 0-.182-.557l-4.204-3.602a.56.56 0 0 1 .321-.988l5.518-.442a.56.56 0 0 0 .475-.345L11.48 3.5Z"/>
                                        </svg>
                                    </span>
                                    <div>
                                        <h3 class="font-extrabold text-white">Đánh giá đơn hàng</h3>
                                        <p class="mt-0.5 text-xs text-gray-500">Chia sẻ trải nghiệm thực tế của bạn</p>
                                    </div>
                                </div>
                                <span class="inline-flex shrink-0 items-center gap-1 rounded-full border border-emerald-500/20 bg-emerald-500/10 px-2.5 py-1 text-[11px] font-bold text-emerald-300">
                                    <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                    Đã mua hàng
                                </span>
                            </div>

                            <fieldset>
                                <legend class="mt-5 text-xs font-bold uppercase tracking-wider text-gray-400">Mức độ hài lòng</legend>
                                <div class="mt-3 flex flex-wrap items-center gap-3">
                                    <div data-rating-picker class="flex gap-1.5 rounded-xl border border-white/5 bg-black/15 px-3 py-2" aria-label="Chọn số sao">
                                        @foreach (range(1, 5) as $rating)
                                            <input class="sr-only" type="radio" name="rating" id="review-rating-{{ $rating }}" value="{{ $rating }}">
                                            <label for="review-rating-{{ $rating }}" class="cursor-pointer text-3xl leading-none text-gray-600 transition duration-150 hover:-translate-y-0.5" title="{{ $rating }} sao">★</label>
                                        @endforeach
                                    </div>
                                    <span id="review-rating-label" class="rounded-full bg-white/5 px-3 py-1.5 text-xs font-bold text-gray-400" aria-live="polite">Chưa chọn sao</span>
                                </div>
                            </fieldset>

                            <div class="mt-5">
                                <label for="review-comment" class="block text-xs font-bold uppercase tracking-wider text-gray-400">Nhận xét của bạn</label>
                                <div class="mt-2 overflow-hidden rounded-xl border border-white/10 bg-black/15 transition focus-within:border-brand-500/60 focus-within:ring-2 focus-within:ring-brand-500/10">
                                    <textarea id="review-comment" name="comment" rows="4" maxlength="5000"
                                              class="w-full resize-none bg-transparent px-4 py-3 text-sm leading-6 text-white outline-none placeholder:text-gray-600"
                                              placeholder="Sản phẩm sử dụng thế nào? Thiết kế, hiệu năng, pin..."></textarea>
                                    <div class="flex items-center justify-between border-t border-white/5 px-4 py-2">
                                        <span class="text-[11px] text-gray-600">Nhận xét chân thực sẽ giúp ích cho người mua khác</span>
                                        <span id="review-comment-count" class="shrink-0 text-[11px] font-semibold text-gray-500">0/5000</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-5">
                                <label for="review-images" class="group flex cursor-pointer items-center gap-3 rounded-xl border border-dashed border-white/15 bg-white/[0.02] px-4 py-3 transition hover:border-brand-500/50 hover:bg-brand-600/5">
                                    <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-white/5 text-gray-400 transition group-hover:bg-brand-600/15 group-hover:text-brand-300">
                                        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm12.75-11.25h.008v.008H15V8.25Z"/></svg>
                                    </span>
                                    <span class="min-w-0">
                                        <span id="review-image-label" class="block text-sm font-bold text-gray-300 group-hover:text-brand-200">Thêm ảnh thực tế</span>
                                        <span class="mt-0.5 block text-xs text-gray-600">JPG, PNG, WEBP · Tối đa 5 ảnh · 2 MB/ảnh</span>
                                    </span>
                                </label>
                                <input id="review-images" name="images[]" type="file" accept="image/jpeg,image/png,image/webp" multiple class="sr-only">
                                <div id="review-image-preview" class="mt-3 hidden grid-cols-3 gap-2 sm:grid-cols-5" aria-label="Ảnh đánh giá đã chọn"></div>
                            </div>

                            <p id="review-form-message" class="mt-4 hidden rounded-xl border border-white/10 bg-black/15 px-4 py-3 text-sm" role="status"></p>
                            <button id="review-submit-btn" type="submit"
                                    class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-brand-600 to-brand-500 px-5 py-3.5 text-sm font-extrabold text-white shadow-lg shadow-brand-900/30 transition hover:-translate-y-0.5 hover:shadow-brand-600/20 disabled:cursor-not-allowed disabled:opacity-60 disabled:hover:translate-y-0">
                                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/></svg>
                                <span data-review-submit-label>Gửi đánh giá</span>
                            </button>
                        </form>
                    @elseif ($reviewStatus === 'reviewed')
                        <div class="mt-5 flex items-center gap-3 rounded-xl border border-emerald-500/15 bg-emerald-500/[0.06] px-4 py-3 text-sm text-emerald-300">
                            <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-emerald-500/10">✓</span>
                            <span><strong class="block text-emerald-200">Cảm ơn bạn!</strong>Bạn đã đánh giá sản phẩm này trong đơn hàng này.</span>
                        </div>
                    @else
                        <div class="mt-5 flex items-center gap-3 rounded-xl border border-white/10 bg-white/[0.025] px-4 py-3 text-sm text-gray-400">
                            <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-white/5 text-gray-500">i</span>
                            <span>Chỉ khách hàng đã nhận và thanh toán đơn hàng mới có thể đánh giá sản phẩm.</span>
                        </div>
                    @endif
                @else
                    <div class="mt-5 rounded-xl border border-brand-500/15 bg-brand-600/[0.06] p-4 text-center">
                        <p class="text-sm text-gray-400">Bạn đã mua sản phẩm này?</p>
                        <a href="{{ route('login') }}" class="mt-2 inline-flex items-center rounded-lg bg-brand-600/20 px-4 py-2 text-sm font-bold text-brand-200 transition hover:bg-brand-600 hover:text-white">Đăng nhập để đánh giá</a>
                    </div>
                @endauth

                @if ($detail['reviews']->isNotEmpty())
                    <div class="mt-5 flex flex-wrap gap-2" aria-label="Lọc đánh giá theo số sao">
                        <button type="button" data-review-filter="all" aria-pressed="true"
                                class="rounded-lg border border-brand-500 bg-brand-600/20 px-3 py-1.5 text-xs font-bold text-brand-200 transition">
                            Tất cả
                        </button>
                        @foreach (range(5, 1) as $star)
                            <button type="button" data-review-filter="{{ $star }}" aria-pressed="false"
                                    class="rounded-lg border border-white/10 px-3 py-1.5 text-xs font-bold text-gray-400 transition hover:border-amber-400/40 hover:text-amber-300">
                                {{ $star }} sao ({{ $detail['rating']['breakdown'][$star] ?? 0 }})
                            </button>
                        @endforeach
                    </div>
                @endif

                <div id="review-list" class="mt-4 space-y-3">
                    @forelse ($detail['reviews'] as $review)
                        <article class="border-t border-white/10 pt-3 {{ $loop->index >= 3 ? 'hidden' : '' }}"
                                 data-review-card data-review-rating="{{ $review['rating'] }}"
                                 @if ($loop->index >= 3) data-extra-review @endif>
                            <div class="flex items-center justify-between">
                                <p class="font-semibold text-white">{{ $review['user']['name'] ?? 'Khách hàng' }}</p>
                                <span class="text-xs text-gray-500">{{ $review['created_at'] }}</span>
                            </div>
                            <div class="mt-1 flex items-center gap-1" aria-label="{{ $review['rating'] }} trên 5 sao">
                                @foreach (range(1, 5) as $star)
                                    <span class="text-sm {{ $star <= $review['rating'] ? 'text-amber-400' : 'text-gray-700' }}" aria-hidden="true">★</span>
                                @endforeach
                                <span class="ml-1 text-xs font-semibold text-gray-500">{{ $review['rating'] }}/5</span>
                            </div>
                            <p class="mt-1 text-sm text-gray-400">{{ $review['comment'] ?: 'Khách hàng chưa để lại nội dung nhận xét.' }}</p>
                            @if ($review['images']->isNotEmpty())
                                <div class="mt-3 flex flex-wrap gap-2" aria-label="Ảnh từ đánh giá của {{ $review['user']['name'] ?? 'khách hàng' }}">
                                    @foreach ($review['images'] as $image)
                                        <a href="{{ $image }}" target="_blank" rel="noopener noreferrer"
                                           class="block overflow-hidden rounded-lg border border-white/10 transition hover:border-brand-500/50">
                                            <img src="{{ $image }}" alt="Ảnh đánh giá sản phẩm"
                                                 class="size-20 object-cover transition duration-300 hover:scale-105" loading="lazy">
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </article>
                    @empty
                        <p class="mt-3 text-sm text-gray-500">Chưa có đánh giá nào cho sản phẩm này.</p>
                    @endforelse
                </div>

                @if ($detail['reviews']->count() > 3)
                    <button id="toggle-reviews-btn" type="button" aria-expanded="false" aria-controls="review-list"
                            class="mt-4 w-full rounded-xl border border-white/10 px-4 py-2.5 text-sm font-bold text-brand-300 transition hover:border-brand-500/40 hover:bg-brand-600/10">
                        Xem thêm {{ $detail['reviews']->count() - 3 }} đánh giá
                    </button>
                @endif
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

        const reviewForm = document.getElementById('review-form');

        if (reviewForm) {
            const ratingInputs = Array.from(reviewForm.querySelectorAll('input[name="rating"]'));
            const ratingLabels = Array.from(reviewForm.querySelectorAll('[data-rating-picker] label'));
            const ratingText = document.getElementById('review-rating-label');
            const commentInput = document.getElementById('review-comment');
            const commentCount = document.getElementById('review-comment-count');
            const imageInput = document.getElementById('review-images');
            const imageLabel = document.getElementById('review-image-label');
            const imagePreview = document.getElementById('review-image-preview');
            const message = document.getElementById('review-form-message');
            const submitButton = document.getElementById('review-submit-btn');
            const submitLabel = submitButton.querySelector('[data-review-submit-label]');
            let previewUrls = [];

            function paintRating(selectedRating = 0) {
                ratingLabels.forEach((label) => {
                    const rating = Number(label.htmlFor.replace('review-rating-', ''));
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
                input.addEventListener('change', () => {
                    paintRating(Number(input.value));
                    ratingText.textContent = `${input.value}/5 sao`;
                });
            });

            ratingLabels.forEach((label) => {
                const hoveredRating = Number(label.htmlFor.replace('review-rating-', ''));

                label.addEventListener('mouseenter', () => {
                    paintRating(hoveredRating);
                    ratingText.textContent = `${hoveredRating}/5 sao`;
                });

                label.addEventListener('mouseleave', () => {
                    const selectedInput = reviewForm.querySelector('input[name="rating"]:checked');
                    const selectedRating = selectedInput ? Number(selectedInput.value) : 0;
                    paintRating(selectedRating);
                    ratingText.textContent = selectedRating ? `${selectedRating}/5 sao` : 'Chưa chọn sao';
                });
            });

            commentInput.addEventListener('input', () => {
                commentCount.textContent = `${commentInput.value.length}/5000`;
            });

            imageInput.addEventListener('change', () => {
                previewUrls.forEach((url) => URL.revokeObjectURL(url));
                previewUrls = [];
                imagePreview.replaceChildren();

                const images = Array.from(imageInput.files);

                if (images.length > 5) {
                    imageInput.value = '';
                    imageLabel.textContent = 'Thêm ảnh thực tế';
                    imagePreview.classList.add('hidden');
                    imagePreview.classList.remove('grid');
                    showReviewMessage('Bạn chỉ có thể chọn tối đa 5 ảnh.');
                    return;
                }

                images.forEach((file, index) => {
                    const previewUrl = URL.createObjectURL(file);
                    const previewItem = document.createElement('div');
                    const image = document.createElement('img');
                    const number = document.createElement('span');
                    previewUrls.push(previewUrl);
                    previewItem.className = 'relative overflow-hidden rounded-xl border border-white/10 bg-black/20';
                    image.src = previewUrl;
                    image.alt = `Ảnh xem trước ${file.name}`;
                    image.className = 'aspect-square w-full object-cover';
                    number.textContent = String(index + 1);
                    number.className = 'absolute right-1.5 top-1.5 flex size-5 items-center justify-center rounded-full bg-black/70 text-[10px] font-bold text-white';
                    previewItem.append(image, number);
                    imagePreview.appendChild(previewItem);
                });

                imageLabel.textContent = images.length > 0 ? `Đã chọn ${images.length} ảnh` : 'Thêm ảnh thực tế';
                imagePreview.classList.toggle('hidden', images.length === 0);
                imagePreview.classList.toggle('grid', images.length > 0);
                message.classList.add('hidden');
            });

            reviewForm.addEventListener('submit', async (event) => {
                event.preventDefault();
                const formData = new FormData(reviewForm);

                if (!formData.get('rating')) {
                    showReviewMessage('Vui lòng chọn số sao trước khi gửi.');
                    return;
                }

                submitButton.disabled = true;
                submitLabel.textContent = 'Đang gửi đánh giá...';
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
                        const validationMessage = data.errors ? Object.values(data.errors).flat()[0] : null;
                        throw new Error(validationMessage || data.message || 'Không thể gửi đánh giá.');
                    }

                    showReviewMessage(data.message || 'Đánh giá sản phẩm thành công.', 'success');
                    submitLabel.textContent = 'Đã gửi đánh giá';
                    window.setTimeout(() => window.location.reload(), 800);
                } catch (error) {
                    showReviewMessage(error.message || 'Không thể kết nối đến máy chủ.');
                    submitButton.disabled = false;
                    submitLabel.textContent = 'Gửi đánh giá';
                }
            });
        }

        const reviewCards = Array.from(document.querySelectorAll('[data-review-card]'));
        const reviewFilterButtons = Array.from(document.querySelectorAll('[data-review-filter]'));
        const toggleReviewsButton = document.getElementById('toggle-reviews-btn');
        const extraReviews = reviewCards.filter((review) => review.hasAttribute('data-extra-review'));
        const hiddenReviewCount = extraReviews.length;

        if (toggleReviewsButton) {
            toggleReviewsButton.addEventListener('click', () => {
                const isExpanded = toggleReviewsButton.getAttribute('aria-expanded') === 'true';

                extraReviews.forEach((review) => review.classList.toggle('hidden', isExpanded));
                toggleReviewsButton.setAttribute('aria-expanded', String(!isExpanded));
                toggleReviewsButton.textContent = isExpanded
                    ? `Xem thêm ${hiddenReviewCount} đánh giá`
                    : 'Thu gọn đánh giá';
            });
        }

        reviewFilterButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const selectedRating = button.dataset.reviewFilter;

                reviewFilterButtons.forEach((filterButton) => {
                    const isActive = filterButton === button;
                    filterButton.setAttribute('aria-pressed', String(isActive));
                    filterButton.classList.toggle('border-brand-500', isActive);
                    filterButton.classList.toggle('bg-brand-600/20', isActive);
                    filterButton.classList.toggle('text-brand-200', isActive);
                    filterButton.classList.toggle('border-white/10', !isActive);
                    filterButton.classList.toggle('text-gray-400', !isActive);
                });

                reviewCards.forEach((review) => {
                    if (selectedRating === 'all') {
                        const isExtraReview = review.hasAttribute('data-extra-review');
                        const isExpanded = toggleReviewsButton?.getAttribute('aria-expanded') === 'true';
                        review.classList.toggle('hidden', isExtraReview && !isExpanded);
                        return;
                    }

                    review.classList.toggle('hidden', review.dataset.reviewRating !== selectedRating);
                });

                if (toggleReviewsButton) {
                    toggleReviewsButton.classList.toggle('hidden', selectedRating !== 'all');
                }
            });
        });
    });
</script>
@endsection
