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
                                <button class="rounded-lg border px-5 py-3 text-sm font-semibold transition {{ $loop->first ? 'border-brand-500 bg-brand-600/15 text-brand-200' : 'border-white/10 bg-white/[0.03] text-gray-200 hover:border-brand-400' }}">
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
                                <button class="inline-flex items-center gap-2 rounded-full border px-3 py-2 text-sm transition {{ $loop->first ? 'border-brand-500 bg-brand-600/15 text-brand-200' : 'border-white/10 bg-white/[0.03] text-gray-200 hover:border-brand-400' }}">
                                    <span class="size-4 rounded-full border border-white/20" style="background: {{ $color['code'] }}"></span>
                                    {{ $color['name'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="mt-5 border-t border-white/10 pt-5">
                    <p class="text-sm text-gray-500">Giá tại Thành phố Hồ Chí Minh</p>
                    <div class="mt-2 flex flex-wrap items-baseline gap-3">
                        <span class="text-3xl font-black text-brand-400">{{ $money($detail['effective_price']) }}</span>
                        @if ($detail['sale_price'])
                            <span class="text-base text-gray-500 line-through">{{ $money($detail['price']) }}</span>
                        @endif
                        @if ($detail['discount_percent'])
                            <span class="rounded-full bg-red-600 px-3 py-1 text-xs font-bold text-white">
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

                <div class="mt-5 grid gap-3">
                    <button class="rounded-xl bg-gradient-to-r from-amber-400 to-amber-500 px-5 py-3.5 text-base font-black text-gray-950 shadow-lg shadow-amber-500/20 transition hover:-translate-y-0.5">
                        Mua ngay
                    </button>
                    <button class="rounded-xl border border-brand-500/40 bg-brand-600/15 px-5 py-3.5 text-base font-black text-brand-200 transition hover:bg-brand-600 hover:text-white">
                        Thêm vào giỏ hàng
                    </button>
                </div>
            </section>

            <section id="reviews" class="rounded-2xl border border-white/5 bg-night-soft p-5 shadow-xl shadow-black/30">
                <h2 class="text-lg font-extrabold text-white">Đánh giá khách hàng</h2>
                <div class="mt-3 flex items-end gap-3">
                    <span class="text-4xl font-black text-white">{{ $detail['rating']['average'] ?: '0.0' }}</span>
                    <span class="pb-1 text-sm text-gray-500">/ 5 từ {{ $detail['rating']['count'] }} đánh giá</span>
                </div>

                <div class="mt-4 space-y-3">
                    @forelse ($detail['reviews']->take(3) as $review)
                        <article class="border-t border-white/10 pt-3">
                            <div class="flex items-center justify-between">
                                <p class="font-semibold text-white">{{ $review['user']['name'] ?? 'Khách hàng' }}</p>
                                <span class="text-xs text-gray-500">{{ $review['created_at'] }}</span>
                            </div>
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

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const mainImage = document.querySelector('[data-gallery-main]');
        const thumbs = Array.from(document.querySelectorAll('[data-gallery-thumb]'));
        const previous = document.querySelector('[data-gallery-prev]');
        const next = document.querySelector('[data-gallery-next]');

        if (!mainImage || thumbs.length === 0) {
            return;
        }

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
    });
</script>
@endsection
