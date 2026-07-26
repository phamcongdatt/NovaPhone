@extends('layouts.app')

@section('title', 'Trang chủ - NovaPhone')

@push('head')
    @if ($banners->isNotEmpty() && $banners->first()->image)
        <link rel="preload" as="image" href="{{ str_starts_with($banners->first()->image, 'http') ? $banners->first()->image : (str_starts_with($banners->first()->image, 'images/') ? asset($banners->first()->image) : asset('storage/' . $banners->first()->image)) }}" fetchpriority="high">
    @endif
@endpush

@section('content')
<div class="space-y-4">
    {{-- Breadcrumb --}}
    <div class="flex">
        <span class="rounded-full border border-[#e8e4de] bg-white px-3 py-1 text-[11px] font-semibold text-[#444] shadow-sm">Trang chủ</span>
    </div>

    {{-- Hero banners --}}
    @if ($banners->isNotEmpty())
        <section class="relative left-1/2 w-screen -translate-x-1/2 overflow-hidden bg-white" data-hero-slider aria-roledescription="carousel">
            <div class="flex transform-gpu transition-transform duration-[800ms] ease-[cubic-bezier(0.22,1,0.36,1)] motion-reduce:transition-none" data-hero-track>
                @foreach ($banners as $index => $banner)
                    @php
                        $bannerImage = $banner->image
                            ? (str_starts_with($banner->image, 'http') ? $banner->image : (str_starts_with($banner->image, 'images/') ? asset($banner->image) : asset('storage/' . $banner->image)))
                            : asset('images/placeholder.svg');
                    @endphp
                    <article class="w-full shrink-0" data-hero-slide aria-hidden="{{ $index === 0 ? 'false' : 'true' }}">
                        <div class="grid min-h-[360px] gap-0 lg:min-h-[460px] lg:grid-cols-[1.1fr_.9fr]">
                            <div class="p-7 sm:p-10 lg:py-16 lg:pl-[9vw] lg:pr-14">
                                @if ($banner->badge)
                                    <p class="text-sm font-semibold text-[#9a9a9a]">{{ $banner->badge }}</p>
                                @endif
                                <h1 class="mt-2 text-4xl font-extrabold tracking-tight text-[#171717] sm:text-5xl">{{ $banner->title }}</h1>
                                @if ($banner->description)
                                    <p class="mt-4 max-w-md text-sm leading-6 text-[#7b7b7b]">{{ $banner->description }}</p>
                                @endif
                                @if ($banner->highlights)
                                    <div class="mt-4 flex flex-wrap gap-2">
                                        @foreach ($banner->highlights as $highlight)
                                            <span class="rounded-full bg-[#f5f4f1] px-3 py-1 text-xs text-[#555]">{{ $highlight }}</span>
                                        @endforeach
                                    </div>
                                @endif
                                <div class="mt-7 flex flex-wrap items-center gap-3">
                                    <a href="{{ $banner->buy_url ?: route('products.index') }}" class="rounded-full bg-black px-5 py-3 text-sm font-semibold text-white transition-colors duration-300 hover:bg-[#222]">Mua ngay</a>
                                    @if ($banner->detail_url)
                                        <a href="{{ $banner->detail_url }}" class="rounded-full border border-[#e6e2dc] bg-white px-5 py-3 text-sm font-semibold text-[#161616] transition-colors duration-300 hover:bg-[#faf9f7]">Tìm hiểu thêm</a>
                                    @endif
                                </div>
                            </div>
                            <div class="flex min-h-64 items-center justify-center bg-gradient-to-br from-[#f8f7f4] to-white p-4 sm:p-8">
                                <img src="{{ $bannerImage }}" alt="{{ $banner->title }}" loading="{{ $index === 0 ? 'eager' : 'lazy' }}" fetchpriority="{{ $index === 0 ? 'high' : 'low' }}" decoding="async" class="max-h-[360px] w-full object-contain lg:max-h-[440px]" data-hero-image>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
            @if ($banners->count() > 1)
                <div class="absolute bottom-4 right-5 flex items-center gap-2">
                    <button type="button" data-hero-prev aria-label="Banner trước" class="grid size-8 place-items-center rounded-full border border-[#e8e4de] bg-white/90 text-sm transition-transform duration-300 hover:scale-105">‹</button>
                    @foreach ($banners as $index => $banner)
                        <button type="button" data-hero-dot="{{ $index }}" aria-label="Chuyển đến banner {{ $index + 1 }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}" class="size-2 rounded-full transition-all duration-300 {{ $index === 0 ? 'scale-125 bg-black' : 'bg-[#d8d4cd]' }}"></button>
                    @endforeach
                    <button type="button" data-hero-next aria-label="Banner tiếp theo" class="grid size-8 place-items-center rounded-full border border-[#e8e4de] bg-white/90 text-sm transition-transform duration-300 hover:scale-105">›</button>
                </div>
            @endif
        </section>
    @endif

    {{-- Flash Sale --}}
    @if ($activeFlashSale)
    <section class="rounded-[28px] border border-[#ece8e2] bg-white p-5 shadow-[0_10px_35px_rgba(0,0,0,.04)]">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-[#171717]">Flash Sale</h2>
                <p class="text-[12px] text-[#7f7f7f]">Ưu đãi giới hạn thời gian</p>
            </div>
            <div class="rounded-full bg-[#f5f4f1] px-3 py-1 text-[11px] font-semibold text-[#4a4a4a]" id="flash-sale-timer">00 : 00 : 00 : 00</div>
        </div>
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-5">
            @forelse ($activeFlashSale->items->take(5) as $item)
                @php
                    $product = $item->product;
                    $discount = $item->discount_percent;
                @endphp
                <x-product-card
                    :id="$product->id"
                    :name="$product->name"
                    :image="$product->thumbnail ? (str_starts_with($product->thumbnail, 'http') ? $product->thumbnail : (str_starts_with($product->thumbnail, 'images/') || str_starts_with($product->thumbnail, 'storage/') ? asset($product->thumbnail) : asset('storage/' . $product->thumbnail))) : asset('images/placeholder.svg')"
                    :price="$product->effective_price"
                    :oldPrice="$product->price"
                    :discount="$discount"
                    :href="route('products.show', $product)"
                />
            @empty
                <p class="col-span-full text-center text-gray-500">Không có sản phẩm trong flash sale</p>
            @endforelse
        </div>
    </section>
    @endif

    {{-- Best Sellers --}}
    @if ($bestSellerProducts->isNotEmpty())
    <section class="rounded-[28px] border border-[#ece8e2] bg-white p-5 shadow-[0_10px_35px_rgba(0,0,0,.04)]">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-[#171717]">Sản phẩm bán chạy</h2>
                <p class="text-[12px] text-[#7f7f7f]">Các sản phẩm được yêu thích nhất</p>
            </div>
            <a href="{{ route('products.index') }}" class="text-xs font-semibold text-[#111] hover:text-[#666]">Xem tất cả</a>
        </div>
        <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
            @foreach ($bestSellerProducts as $product)
                <x-product-card
                    :id="$product->id"
                    :name="$product->name"
                    :image="$product->thumbnail ? (str_starts_with($product->thumbnail, 'http') ? $product->thumbnail : (str_starts_with($product->thumbnail, 'images/') || str_starts_with($product->thumbnail, 'storage/') ? asset($product->thumbnail) : asset('storage/' . $product->thumbnail))) : asset('images/placeholder.svg')"
                    :price="$product->effective_price"
                    :oldPrice="$product->sale_price && $product->sale_price < $product->price ? $product->price : null"
                    :href="route('products.show', $product)"
                />
            @endforeach
        </div>
    </section>
    @endif

    {{-- Featured Products --}}
    <section class="rounded-[28px] border border-[#ece8e2] bg-white p-5 shadow-[0_10px_35px_rgba(0,0,0,.04)]">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-[#171717]">Thương hiệu nổi bật</h2>
                <p class="text-[12px] text-[#7f7f7f]">Khám phá sản phẩm theo thương hiệu</p>
            </div>
            <a href="{{ route('products.index') }}" class="text-xs font-semibold text-[#111] hover:text-[#666]">Xem tất cả</a>
        </div>
        <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
            @forelse ($filterBrands->take(4) as $brand)
                <a href="{{ route('products.index', ['brand' => $brand->id]) }}" class="group rounded-[22px] border border-[#ece8e2] bg-[#fbfaf8] p-4 text-center transition-[border-color,box-shadow,transform] duration-300 ease-out hover:-translate-y-0.5 hover:border-[#ddd] hover:shadow-md">
                    <div class="mx-auto flex h-14 w-28 items-center justify-center rounded-2xl bg-white px-3 shadow-sm transition-transform duration-300 ease-out group-hover:scale-[1.03]">
                        @if ($brand->logo)
                            <img src="{{ asset($brand->logo) }}" alt="" aria-hidden="true" loading="lazy" decoding="async" class="max-h-7 max-w-full object-contain">
                        @else
                            <span class="text-sm font-bold text-[#171717]">{{ mb_substr($brand->name, 0, 1) }}</span>
                        @endif
                    </div>
                    <div class="mt-3 text-sm font-semibold text-[#171717] group-hover:text-black">{{ $brand->name }}</div>
                    <div class="mt-1 text-[11px] text-[#8b8b8b]">{{ $brand->products_count }} sản phẩm</div>
                </a>
            @empty
                <p class="col-span-full text-center text-gray-500">Không có thương hiệu nào</p>
            @endforelse
        </div>
    </section>

    {{-- Latest Posts --}}
    @if ($latestPosts->isNotEmpty())
    <section class="rounded-[28px] border border-[#ece8e2] bg-white p-5 shadow-[0_10px_35px_rgba(0,0,0,.04)]">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-[#171717]">Bài viết mới nhất</h2>
                <p class="text-[12px] text-[#7f7f7f]">Tin tức và mẹo sử dụng</p>
            </div>
            <a href="{{ route('posts.index') }}" class="text-xs font-semibold text-[#111] hover:text-[#666]">Xem tất cả</a>
        </div>
        <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
            @foreach ($latestPosts as $post)
                <article class="group overflow-hidden rounded-[16px] border border-[#ece8e2] transition-all hover:shadow-lg">
                    @if ($post->thumbnail)
                        <div class="h-40 overflow-hidden bg-gray-200">
                            <img src="{{ str_starts_with($post->thumbnail, 'http') ? $post->thumbnail : (str_starts_with($post->thumbnail, 'images/') ? asset($post->thumbnail) : asset('storage/' . $post->thumbnail)) }}" alt="{{ $post->title }}" loading="lazy" decoding="async" class="size-full object-cover group-hover:scale-105 transition-transform">
                        </div>
                    @endif
                    <div class="p-4">
                        <p class="text-[11px] text-[#999]">{{ $post->published_at?->format('d/m/Y') }}</p>
                        <h3 class="mt-2 line-clamp-2 font-semibold text-[#171717] group-hover:text-black">{{ $post->title }}</h3>
                        <p class="mt-2 line-clamp-2 text-[12px] text-[#7f7f7f]">{{ $post->summary ?: \Illuminate\Support\Str::limit($post->content, 100) }}</p>
                         <a href="{{ route('posts.show', $post->slug) }}" class="mt-3 inline-block text-xs font-semibold text-[#111] hover:text-[#666]">Đọc thêm</a>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
    @endif

    <section class="rounded-[28px] border border-[#ece8e2] bg-white p-5 shadow-[0_10px_35px_rgba(0,0,0,.04)]">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-[#171717]">Tất cả sản phẩm</h2>
                <p class="text-[12px] text-[#7f7f7f]">Lấy trực tiếp từ cơ sở dữ liệu và có thể tìm kiếm</p>
            </div>
            <a href="{{ route('products.index') }}" class="text-xs font-semibold text-[#111] hover:text-[#666]">Xem tất cả</a>
        </div>
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-4">
            @forelse ($catalogProducts as $product)
                @php
                    $discount = $product->sale_price && $product->sale_price < $product->price ? (int) round((($product->price - $product->sale_price) / $product->price) * 100) : null;
                @endphp
                <x-product-card
                    :id="$product->id"
                    :name="$product->name"
                    :image="$product->thumbnail ? (str_starts_with($product->thumbnail, 'http') ? $product->thumbnail : (str_starts_with($product->thumbnail, 'images/') || str_starts_with($product->thumbnail, 'storage/') ? asset($product->thumbnail) : asset('storage/' . $product->thumbnail))) : asset('images/placeholder.svg')"
                    :price="$product->effective_price"
                    :old-price="$product->sale_price ? $product->price : null"
                    :discount="$discount"
                    :rating="$product->rating_average ? round($product->rating_average, 1) : null"
                    :sold="$product->sold_count ? number_format($product->sold_count, 0, ',', '.') : null"
                    :is-flash-sale="$product->activeFlashSaleItem !== null"
                    :href="route('products.show', $product)"
                />
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-[#e6e2dc] p-10 text-center text-sm text-[#8b8b8b]">Chưa có sản phẩm.</div>
            @endforelse
        </div>
        @if ($catalogProducts->hasPages())
            <div class="mt-6">
                {{ $catalogProducts->links() }}
            </div>
        @endif
    </section>
</div>

<script>
    // Flash Sale Timer
    function updateFlashSaleTimer() {
        const timerElement = document.getElementById('flash-sale-timer');
        if (!timerElement) return false;

        const endTime = new Date('{{ $activeFlashSale?->end_time }}').getTime();
        const now = new Date().getTime();
        const distance = endTime - now;

        if (distance <= 0) {
            timerElement.textContent = '00 : 00 : 00 : 00';
            return false;
        }

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        timerElement.textContent =
            `${String(hours).padStart(2, '0')} : ${String(minutes).padStart(2, '0')} : ${String(seconds).padStart(2, '0')} : ${String(days).padStart(2, '0')}`;
        return true;
    }

    @if ($activeFlashSale)
        let flashSaleTimerId;

        const stopFlashSaleTimer = () => window.clearInterval(flashSaleTimerId);
        const startFlashSaleTimer = () => {
            stopFlashSaleTimer();
            if (!updateFlashSaleTimer()) return;
            flashSaleTimerId = window.setInterval(() => {
                if (!updateFlashSaleTimer()) stopFlashSaleTimer();
            }, 1000);
        };

        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                stopFlashSaleTimer();
            } else {
                startFlashSaleTimer();
            }
        });

        startFlashSaleTimer();
    @endif
</script>
@endsection
