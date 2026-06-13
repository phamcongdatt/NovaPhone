@extends('layouts.app')

@section('title', 'NovaPhone — Điện thoại chính hãng, công nghệ mới nhất trong tầm tay')

@php
    // Dữ liệu tĩnh mô phỏng (sẽ thay bằng dữ liệu từ Controller/API khi làm Backend)
    $productImages = [
        'iPhone 15 Pro Max 256GB' => 'iphone-15-pro-max-256gb.jpg',
        'Samsung Galaxy S24 Ultra 256GB' => 'samsung-galaxy-s24-ultra-256gb.webp',
        'Xiaomi 14T Pro 512GB' => 'xiaomi-14t-pro-512gb.jpg',
        'OPPO Find X7 Ultra 256GB' => 'oppo-find-x7-ultra.webp',
        'Vivo X100 Pro 256GB' => 'vivo-x100-pro.webp',
        'Realme GT 6 512GB' => 'realme-gt-6-512gb.webp',
        'Samsung Galaxy S24 Ultra' => 'samsung-galaxy-s24-ultra.jpg',
        'iPhone 15 128GB' => 'iphone-15-128gb.webp',
        'Xiaomi Redmi Note 13 Pro' => 'xiaomi-redmi-note-13-pro.webp',
        'iPhone 16 128GB' => 'iphone-16-128gb.webp',
        'Samsung Galaxy Z Flip6' => 'samsung-galaxy-z-flip6.webp',
        'Xiaomi 14 Ultra' => 'xiaomi-14-ultra.webp',
        'OPPO Reno12 Pro' => 'oppo-reno12-pro.webp',
    ];
    $productImage = fn ($name) => asset('images/products/'.($productImages[$name] ?? 'iphone-15-pro-max-256gb.jpg'));

    $flashSale = [
        ['name' => 'iPhone 15 Pro Max 256GB', 'image' => $productImage('iPhone 15 Pro Max 256GB'), 'price' => 28990000, 'oldPrice' => 34990000, 'discount' => 17, 'sold' => '128', 'soldPercent' => 72],
        ['name' => 'Samsung Galaxy S24 Ultra 256GB', 'image' => $productImage('Samsung Galaxy S24 Ultra 256GB'), 'price' => 25990000, 'oldPrice' => 31990000, 'discount' => 19, 'sold' => '96', 'soldPercent' => 58],
        ['name' => 'Xiaomi 14T Pro 512GB', 'image' => $productImage('Xiaomi 14T Pro 512GB'), 'price' => 14990000, 'oldPrice' => 18990000, 'discount' => 21, 'sold' => '215', 'soldPercent' => 85],
        ['name' => 'OPPO Find X7 Ultra 256GB', 'image' => $productImage('OPPO Find X7 Ultra 256GB'), 'price' => 16990000, 'oldPrice' => 21990000, 'discount' => 23, 'sold' => '54', 'soldPercent' => 40],
        ['name' => 'Vivo X100 Pro 256GB', 'image' => $productImage('Vivo X100 Pro 256GB'), 'price' => 17990000, 'oldPrice' => 21990000, 'discount' => 18, 'sold' => '64', 'soldPercent' => 47],
        ['name' => 'Realme GT 6 512GB', 'image' => $productImage('Realme GT 6 512GB'), 'price' => 11990000, 'oldPrice' => 14990000, 'discount' => 20, 'sold' => '47', 'soldPercent' => 35],
    ];

    $bestSeller = [
        ['name' => 'iPhone 15 Pro Max 256GB', 'image' => $productImage('iPhone 15 Pro Max 256GB'), 'price' => 28990000, 'rating' => 4.9, 'sold' => '2,3k'],
        ['name' => 'Samsung Galaxy S24 Ultra', 'image' => $productImage('Samsung Galaxy S24 Ultra'), 'price' => 25990000, 'rating' => 4.8, 'sold' => '1,8k'],
        ['name' => 'iPhone 15 128GB', 'image' => $productImage('iPhone 15 128GB'), 'price' => 20990000, 'rating' => 4.8, 'sold' => '1,6k'],
        ['name' => 'Xiaomi Redmi Note 13 Pro', 'image' => $productImage('Xiaomi Redmi Note 13 Pro'), 'price' => 6990000, 'rating' => 4.6, 'sold' => '1,2k'],
    ];

    $newArrival = [
        ['name' => 'iPhone 16 128GB', 'image' => $productImage('iPhone 16 128GB'), 'price' => 22990000, 'rating' => 5.0, 'badge' => 'Mới'],
        ['name' => 'Samsung Galaxy Z Flip6', 'image' => $productImage('Samsung Galaxy Z Flip6'), 'price' => 26990000, 'rating' => 5.0, 'badge' => 'Mới'],
        ['name' => 'Xiaomi 14 Ultra', 'image' => $productImage('Xiaomi 14 Ultra'), 'price' => 24990000, 'rating' => 5.0, 'badge' => 'Mới'],
        ['name' => 'OPPO Reno12 Pro', 'image' => $productImage('OPPO Reno12 Pro'), 'price' => 12990000, 'rating' => 5.0, 'badge' => 'Mới'],
    ];

    $brands = [
        ['name' => 'iPhone', 'count' => '120+ sản phẩm'],
        ['name' => 'Samsung', 'count' => '80+ sản phẩm'],
        ['name' => 'Xiaomi', 'count' => '98+ sản phẩm'],
        ['name' => 'OPPO', 'count' => '60+ sản phẩm'],
        ['name' => 'Vivo', 'count' => '85+ sản phẩm'],
        ['name' => 'Realme', 'count' => '45+ sản phẩm'],
        ['name' => 'Honor', 'count' => '30+ sản phẩm'],
        ['name' => 'Google', 'count' => '15+ sản phẩm'],
    ];

    $services = [
        ['title' => 'Miễn phí giao hàng', 'desc' => 'Toàn quốc cho đơn từ 500k', 'icon' => 'M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.9 17.9 0 0 0-3.213-9.193 2.06 2.06 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12'],
        ['title' => 'Thu cũ đổi mới', 'desc' => 'Trợ giá đến 5 triệu đồng', 'icon' => 'M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99'],
        ['title' => 'Trả góp 0%', 'desc' => 'Duyệt nhanh trong 5 phút', 'icon' => 'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z'],
        ['title' => 'Bảo hành chính hãng', 'desc' => '12 – 24 tháng chính hãng', 'icon' => 'M9 12.75 11.25 15 15 9.75m-3-7.036A11.96 11.96 0 0 1 3.6 6c-.34 1.01-.6 2.08-.6 3.22 0 5.59 3.82 10.29 9 11.62 5.18-1.33 9-6.03 9-11.62 0-1.14-.26-2.21-.6-3.22a11.96 11.96 0 0 1-8.4-3.29Z'],
    ];

    $tradeinSteps = [
        ['step' => 1, 'title' => 'Chọn máy cũ', 'desc' => 'Chọn thương hiệu & tình trạng'],
        ['step' => 2, 'title' => 'Định giá online', 'desc' => 'Nhận giá thu ngay lập tức'],
        ['step' => 3, 'title' => 'Lên đời dễ dàng', 'desc' => 'Bù tiền nhận máy mới'],
    ];

    $news = [
        ['title' => 'iPhone 17 Pro Max: Thiết kế titanium mới, nâng cấp đột phá', 'image' => 'https://placehold.co/800x500/12151d/93c5fd?text=iPhone+17+Pro+Max', 'date' => '12/06/2026'],
        ['title' => 'Galaxy S24 Ultra sau 3 tháng: Có còn đáng mua?', 'image' => 'https://placehold.co/800x500/12151d/93c5fd?text=Galaxy+S24+Ultra', 'date' => '10/06/2026'],
        ['title' => 'Xiaomi 15 Ultra chính thức ra mắt tại Việt Nam', 'image' => 'https://placehold.co/800x500/12151d/93c5fd?text=Xiaomi+15+Ultra', 'date' => '08/06/2026'],
        ['title' => 'iPhone 15 Pro Max vs Galaxy S24 Ultra: Đâu là vua Android?', 'image' => 'https://placehold.co/800x500/12151d/93c5fd?text=Flagship+Battle', 'date' => '06/06/2026'],
    ];
    $detailHref = fn ($name) => route('products.show', \Illuminate\Support\Str::slug($name));
@endphp

@section('content')

{{-- ===================== 1. Hero Banner ===================== --}}
<section class="relative overflow-hidden">
    <div class="hero-glow absolute inset-0"></div>
    <div class="absolute inset-0 opacity-[0.05]" style="background-image: radial-gradient(circle, #fff 1px, transparent 1px); background-size: 28px 28px;"></div>

    <div class="relative mx-auto grid max-w-7xl items-center gap-10 px-4 py-14 sm:px-6 lg:grid-cols-2 lg:py-20">
        <div class="reveal revealed text-center lg:text-left">
            <span class="mb-5 inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-1.5 text-xs font-semibold tracking-wide text-brand-300 backdrop-blur">
                <span class="size-1.5 animate-pulse rounded-full bg-brand-400"></span>
                Sản phẩm mới ra mắt
            </span>
            <h1 class="text-4xl font-extrabold leading-tight tracking-tight text-white sm:text-5xl lg:text-6xl">
                iPhone <span class="bg-gradient-to-r from-brand-400 to-cyan-300 bg-clip-text text-transparent">17 Pro Max</span>
            </h1>
            <p class="mt-4 text-lg font-medium text-gray-300">Titanium. Mạnh mẽ. Đẳng cấp.</p>

            {{-- Chips tính năng nổi bật --}}
            <div class="mt-6 flex flex-wrap items-center justify-center gap-2.5 lg:justify-start">
                @foreach (['A19 Pro Chip', 'Camera 48MP', 'Pin cả ngày'] as $chip)
                    <span class="rounded-full border border-white/10 bg-white/5 px-4 py-1.5 text-xs font-semibold text-gray-300 backdrop-blur">{{ $chip }}</span>
                @endforeach
            </div>

            <div class="mt-8 flex flex-wrap items-center justify-center gap-3 lg:justify-start">
                <a href="#flash-sale"
                   class="rounded-2xl bg-gradient-to-r from-amber-400 to-amber-500 px-8 py-3.5 text-sm font-bold text-gray-900 shadow-lg shadow-amber-500/25 transition-all duration-200 ease-in-out hover:-translate-y-0.5 hover:shadow-xl hover:shadow-amber-500/35 active:translate-y-0">
                    Mua ngay
                </a>
                <a href="{{ route('products.show', 'iphone-15-pro-max-256gb') }}"
                   class="rounded-2xl border border-white/15 bg-white/5 px-8 py-3.5 text-sm font-bold text-white backdrop-blur transition-all duration-200 ease-in-out hover:-translate-y-0.5 hover:bg-white/10">
                    Xem chi tiết
                </a>
            </div>
        </div>

        {{-- Ảnh sản phẩm hero --}}
        <div class="relative hidden justify-center lg:flex">
            <div class="absolute inset-0 m-auto size-80 rounded-full bg-brand-600/25 blur-3xl"></div>
            <img src="https://placehold.co/460x540/0d1017/3b82f6?text=iPhone+17+Pro+Max"
                 alt="iPhone 17 Pro Max — flagship mới nhất tại NovaPhone"
                 class="float-slow relative w-[26rem] rounded-[2.5rem] border border-white/10 shadow-2xl shadow-black/60">
        </div>
    </div>

    {{-- Điều hướng slider --}}
    <button aria-label="Banner trước" class="absolute left-3 top-1/2 hidden size-10 -translate-y-1/2 items-center justify-center rounded-full border border-white/10 bg-white/5 text-white backdrop-blur transition-all duration-200 ease-in-out hover:bg-white/15 lg:flex">
        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
    </button>
    <button aria-label="Banner sau" class="absolute right-3 top-1/2 hidden size-10 -translate-y-1/2 items-center justify-center rounded-full border border-white/10 bg-white/5 text-white backdrop-blur transition-all duration-200 ease-in-out hover:bg-white/15 lg:flex">
        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
    </button>
    {{-- Chấm chuyển slide --}}
    <div class="absolute bottom-5 left-1/2 flex -translate-x-1/2 gap-2">
        <span class="h-1.5 w-6 rounded-full bg-brand-500"></span>
        <span class="size-1.5 rounded-full bg-white/25"></span>
        <span class="size-1.5 rounded-full bg-white/25"></span>
        <span class="size-1.5 rounded-full bg-white/25"></span>
    </div>
</section>

{{-- ===================== 2. Thương hiệu ===================== --}}
<section class="mx-auto max-w-7xl px-4 py-10 sm:px-6">
    <div class="reveal-stagger grid grid-cols-3 gap-3 rounded-3xl border border-white/5 bg-night-soft p-4 sm:grid-cols-5 lg:grid-cols-9">
        @foreach ($brands as $brand)
            <a href="#" class="group flex flex-col items-center gap-2 rounded-2xl p-4 text-center transition-all duration-200 ease-in-out hover:-translate-y-1 hover:bg-white/5">
                <span class="flex size-11 items-center justify-center rounded-full bg-white/5 text-sm font-extrabold text-gray-300 transition-all duration-200 ease-in-out group-hover:bg-brand-600 group-hover:text-white">
                    {{ mb_substr($brand['name'], 0, 1) }}
                </span>
                <span>
                    <span class="block text-xs font-bold text-white">{{ $brand['name'] }}</span>
                    <span class="mt-0.5 block text-[10px] text-gray-500">{{ $brand['count'] }}</span>
                </span>
            </a>
        @endforeach
        <a href="#" class="group flex flex-col items-center justify-center gap-2 rounded-2xl p-4 text-center transition-all duration-200 ease-in-out hover:-translate-y-1 hover:bg-white/5">
            <span class="flex size-11 items-center justify-center rounded-full border border-dashed border-white/20 text-gray-400 transition-all duration-200 ease-in-out group-hover:border-brand-500 group-hover:text-brand-400">
                <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
            </span>
            <span class="block text-xs font-bold text-gray-400 group-hover:text-white">Xem tất cả</span>
        </a>
    </div>
</section>

{{-- ===================== 3. Cam kết dịch vụ ===================== --}}
<section class="mx-auto max-w-7xl px-4 pb-4 sm:px-6">
    <div class="reveal-stagger grid grid-cols-2 gap-3 lg:grid-cols-4">
        @foreach ($services as $sv)
            <div class="flex items-center gap-3.5 rounded-2xl border border-white/5 bg-night-soft p-4 transition-all duration-200 ease-in-out hover:border-brand-500/30">
                <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-brand-600/15 text-brand-400">
                    <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $sv['icon'] }}"/></svg>
                </span>
                <span>
                    <span class="block text-sm font-bold text-white">{{ $sv['title'] }}</span>
                    <span class="mt-0.5 block text-xs text-gray-500">{{ $sv['desc'] }}</span>
                </span>
            </div>
        @endforeach
    </div>
</section>

{{-- ===================== 4. Flash Sale ===================== --}}
<section id="flash-sale" class="mx-auto max-w-7xl scroll-mt-24 px-4 py-10 sm:px-6">
    <div class="reveal rounded-3xl border border-white/5 bg-night-soft p-5 sm:p-7">
        {{-- Tiêu đề + đếm ngược --}}
        <div class="mb-6 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <div class="flex items-center gap-3">
                <span class="flex size-10 items-center justify-center rounded-xl bg-amber-400/15 text-amber-400">
                    <svg class="size-5" fill="currentColor" viewBox="0 0 24 24"><path d="M13 2 4.09 12.69a.6.6 0 0 0 .46.99H11l-1.27 7.4a.6.6 0 0 0 1.07.47l8.91-10.68a.6.6 0 0 0-.46-.99H13l1.27-7.4A.6.6 0 0 0 13.2 2H13Z"/></svg>
                </span>
                <h2 class="text-xl font-extrabold tracking-tight text-white sm:text-2xl">Flash Sale</h2>
            </div>

            <div class="flex items-center gap-4">
                {{-- Countdown --}}
                <div class="flex items-center gap-1.5" data-countdown role="timer" aria-label="Thời gian còn lại của Flash Sale">
                    <span class="mr-1 text-xs text-gray-500">Kết thúc sau:</span>
                    @foreach ([['gio', 'Giờ'], ['phut', 'Phút'], ['giay', 'Giây']] as $i => [$key, $label])
                        @if ($i > 0)
                            <span class="pb-4 text-sm font-bold text-white/40">:</span>
                        @endif
                        <div class="flex flex-col items-center">
                            <span data-cd="{{ $key }}" class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-600 text-sm font-extrabold tabular-nums text-white">00</span>
                            <span class="mt-1 text-[9px] font-medium uppercase tracking-wider text-gray-500">{{ $label }}</span>
                        </div>
                    @endforeach
                </div>
                <a href="#" class="hidden items-center gap-1 text-xs font-semibold text-brand-400 transition-colors duration-200 hover:text-brand-300 sm:inline-flex">
                    Xem tất cả
                    <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                </a>
            </div>
        </div>

        {{-- Lưới sản phẩm sale --}}
        <div class="reveal-stagger grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
            @foreach ($flashSale as $p)
                <x-product-card
                    :name="$p['name']" :image="$p['image']" :price="$p['price']" :old-price="$p['oldPrice']"
                    :discount="$p['discount']" :sold="$p['sold']" :sold-percent="$p['soldPercent']"
                    :href="$detailHref($p['name'])"
                />
            @endforeach
        </div>
    </div>
</section>

{{-- ===================== 5. Điện thoại / Lọc theo giá, thương hiệu, tính năng ===================== --}}
<section id="san-pham" class="mx-auto max-w-7xl scroll-mt-24 px-4 py-6 sm:px-6">
    <div class="reveal mb-6">
        <div class="mb-5">
            <h2 class="text-xl font-extrabold tracking-tight text-white sm:text-2xl">Điện thoại</h2>
            <p class="mt-1 text-sm text-gray-500">Lọc nhanh sản phẩm theo giá, thương hiệu và tính năng bạn cần.</p>
        </div>

        <form method="GET" action="{{ route('home') }}#san-pham"
              class="rounded-2xl border border-white/5 bg-night-soft p-4 shadow-xl shadow-black/20">
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-12">
                <div class="flex flex-col gap-1.5 xl:col-span-3">
                    <label for="search-filter" class="text-xs font-semibold uppercase tracking-wider text-gray-500">Từ khóa</label>
                    <input id="search-filter" name="q" type="search" value="{{ $selectedSearchQuery }}"
                           placeholder="Tên, SKU..."
                           class="h-12 rounded-xl border border-white/10 bg-night-card px-4 text-sm font-semibold text-white outline-none transition-all duration-200 ease-in-out placeholder:text-gray-500 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/25">
                </div>

                <div class="flex flex-col gap-1.5 xl:col-span-2">
                    <label for="price-filter" class="text-xs font-semibold uppercase tracking-wider text-gray-500">Khoảng giá</label>
                    <select id="price-filter" name="price"
                            class="h-12 rounded-xl border border-white/10 bg-night-card px-4 text-sm font-semibold text-white outline-none transition-all duration-200 ease-in-out focus:border-brand-500 focus:ring-2 focus:ring-brand-500/25">
                        <option value="">Tất cả mức giá</option>
                        @foreach ($priceRanges as $value => $label)
                            <option value="{{ $value }}" @selected($selectedPriceRange === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-1.5 xl:col-span-2">
                    <label for="brand-filter" class="text-xs font-semibold uppercase tracking-wider text-gray-500">Thương hiệu</label>
                    <select id="brand-filter" name="brand"
                            class="h-12 rounded-xl border border-white/10 bg-night-card px-4 text-sm font-semibold text-white outline-none transition-all duration-200 ease-in-out focus:border-brand-500 focus:ring-2 focus:ring-brand-500/25">
                        <option value="">Tất cả thương hiệu</option>
                        @foreach ($filterBrands as $brand)
                            <option value="{{ $brand->slug }}" @selected($selectedBrandSlug === $brand->slug)>
                                {{ $brand->name }} ({{ $brand->products_count }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-1.5 xl:col-span-2">
                    <label for="sort-filter" class="text-xs font-semibold uppercase tracking-wider text-gray-500">Sắp xếp</label>
                    <select id="sort-filter" name="sort"
                            class="h-12 rounded-xl border border-white/10 bg-night-card px-4 text-sm font-semibold text-white outline-none transition-all duration-200 ease-in-out focus:border-brand-500 focus:ring-2 focus:ring-brand-500/25">
                        @foreach ($sortOptions as $value => $label)
                            <option value="{{ $value }}" @selected($selectedSort === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-2 xl:col-span-3">
                    <button type="submit"
                            class="h-12 flex-1 rounded-xl bg-brand-600 px-5 text-sm font-bold text-white shadow-lg shadow-brand-600/20 transition-all duration-200 ease-in-out hover:bg-brand-500">
                        Lọc
                    </button>
                    @if ($selectedSearchQuery || $selectedPriceRange || $selectedBrandSlug || $selectedFeatures)
                        <a href="{{ route('home') }}#san-pham"
                           class="flex h-12 items-center justify-center rounded-xl border border-white/10 px-5 text-center text-sm font-bold text-gray-300 transition-all duration-200 ease-in-out hover:bg-white/5 hover:text-white">
                            Xóa lọc
                        </a>
                    @endif
                </div>
            </div>

            <fieldset class="mt-4">
                <legend class="mb-2 text-xs font-semibold uppercase tracking-wider text-gray-500">Tính năng</legend>
                <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
                    @foreach ($featureFilters as $value => $label)
                        <label class="flex min-h-11 items-center gap-2 rounded-xl border border-white/10 bg-night-card px-3 text-sm font-semibold text-gray-300 transition-colors duration-200 hover:border-brand-500/50 hover:text-white">
                            <input type="checkbox" name="features[]" value="{{ $value }}" @checked(in_array($value, $selectedFeatures, true))
                                   class="size-4 rounded border-white/20 bg-white/5 text-brand-600 focus:ring-brand-500">
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </fieldset>
        </form>
    </div>

    <div class="reveal-stagger grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6">
        @forelse ($catalogProducts as $product)
            @php
                $discount = null;
                if ($product->sale_price && $product->sale_price < $product->price) {
                    $discount = (int) round((($product->price - $product->sale_price) / $product->price) * 100);
                }
            @endphp
            <x-product-card
                :name="$product->name"
                :image="$product->thumbnail ?: 'https://placehold.co/900x900/12151d/93c5fd?text='.urlencode($product->name)"
                :price="$product->effective_price"
                :old-price="$product->sale_price ? $product->price : null"
                :discount="$discount"
                :rating="$product->rating_average ? round($product->rating_average, 1) : null"
                :sold="$product->sold_count ? number_format($product->sold_count, 0, ',', '.') : null"
                :href="route('products.show', $product)"
            />
        @empty
            <div class="col-span-full rounded-2xl border border-white/5 bg-night-soft p-8 text-center">
                <p class="text-sm font-semibold text-white">Chưa có sản phẩm phù hợp.</p>
                <p class="mt-1 text-xs text-gray-500">Thử tìm từ khóa khác hoặc chọn lại bộ lọc để xem thêm điện thoại.</p>
            </div>
        @endforelse
    </div>

    @if ($catalogProducts->hasPages())
        <div class="mt-8">
            {{ $catalogProducts->links() }}
        </div>
    @endif
</section>

{{-- ===================== 5. Best Seller + New Arrival ===================== --}}
<section class="mx-auto max-w-7xl scroll-mt-24 px-4 py-6 sm:px-6">
    <div class="grid gap-10 lg:grid-cols-2 lg:gap-8">
        @foreach ([
            ['title' => 'Best Seller', 'iconColor' => 'text-amber-400 bg-amber-400/15', 'items' => $bestSeller, 'icon' => 'M13 2 4.09 12.69a.6.6 0 0 0 .46.99H11l-1.27 7.4a.6.6 0 0 0 1.07.47l8.91-10.68a.6.6 0 0 0-.46-.99H13l1.27-7.4A.6.6 0 0 0 13.2 2H13Z'],
            ['title' => 'New Arrival', 'iconColor' => 'text-violet-400 bg-violet-400/15', 'items' => $newArrival, 'icon' => 'M11.48 3.5c.2-.6 1.04-.6 1.24 0l1.65 5.06a.65.65 0 0 0 .62.45h5.32c.63 0 .9.81.38 1.18l-4.3 3.13a.65.65 0 0 0-.24.73l1.64 5.06c.2.6-.49 1.1-1 .73l-4.3-3.13a.65.65 0 0 0-.77 0l-4.3 3.13c-.51.37-1.2-.13-1-.73l1.64-5.06a.65.65 0 0 0-.24-.73l-4.3-3.13c-.51-.37-.25-1.18.38-1.18h5.32a.65.65 0 0 0 .62-.45l1.65-5.06Z'],
        ] as $block)
            <div class="reveal">
                <div class="mb-5 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="flex size-9 items-center justify-center rounded-xl {{ $block['iconColor'] }}">
                            <svg class="size-4.5" fill="currentColor" viewBox="0 0 24 24"><path d="{{ $block['icon'] }}"/></svg>
                        </span>
                        <h2 class="text-lg font-extrabold tracking-tight text-white sm:text-xl">{{ $block['title'] }}</h2>
                    </div>
                    <a href="#" class="inline-flex items-center gap-1 text-xs font-semibold text-brand-400 transition-colors duration-200 hover:text-brand-300">
                        Xem tất cả
                        <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                    </a>
                </div>
                <div class="reveal-stagger grid grid-cols-2 gap-3 sm:grid-cols-4 lg:grid-cols-2 xl:grid-cols-4">
                    @foreach ($block['items'] as $p)
                        <x-product-card
                            :name="$p['name']" :image="$p['image']" :price="$p['price']"
                            :rating="$p['rating']" :sold="$p['sold'] ?? null" :badge="$p['badge'] ?? null"
                            :href="$detailHref($p['name'])"
                        />
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</section>

{{-- ===================== 6. Thu cũ đổi mới ===================== --}}
<section class="mx-auto max-w-7xl px-4 py-10 sm:px-6">
    <div class="reveal relative overflow-hidden rounded-3xl border border-white/5 bg-gradient-to-r from-brand-950 via-night-soft to-night-soft">
        <div class="hero-glow absolute inset-0 opacity-60"></div>
        <div class="relative grid items-center gap-8 p-6 sm:p-10 lg:grid-cols-[1fr_1.4fr_auto]">
            {{-- Cột trái: thông điệp --}}
            <div>
                <span class="mb-3 inline-block rounded-full border border-brand-500/30 bg-brand-600/15 px-3.5 py-1 text-[11px] font-bold uppercase tracking-wider text-brand-300">
                    Thu cũ đổi mới
                </span>
                <h2 class="text-2xl font-extrabold tracking-tight text-white sm:text-3xl">Lên đời flagship</h2>
                <p class="mt-1.5 text-sm text-gray-400">
                    Trợ giá đến <span class="font-extrabold text-amber-400">5.000.000₫</span>
                </p>
                <a href="#" class="mt-5 inline-block rounded-xl bg-gradient-to-r from-amber-400 to-amber-500 px-6 py-3 text-sm font-bold text-gray-900 shadow-lg shadow-amber-500/25 transition-all duration-200 ease-in-out hover:-translate-y-0.5 hover:shadow-xl hover:shadow-amber-500/35">
                    Định giá máy ngay
                </a>
            </div>

            {{-- Cột giữa: 3 bước --}}
            <div class="grid gap-4 sm:grid-cols-3">
                @foreach ($tradeinSteps as $step)
                    <div class="flex items-start gap-3 sm:flex-col sm:gap-2.5">
                        <span class="flex size-9 shrink-0 items-center justify-center rounded-full border border-brand-500/40 bg-brand-600/15 text-sm font-extrabold text-brand-300">{{ $step['step'] }}</span>
                        <span>
                            <span class="block text-sm font-bold text-white">{{ $step['title'] }}</span>
                            <span class="mt-0.5 block text-xs text-gray-500">{{ $step['desc'] }}</span>
                        </span>
                    </div>
                @endforeach
            </div>

            {{-- Cột phải: hình minh hoạ --}}
            <div class="hidden xl:block">
                <img src="https://placehold.co/280x180/0d1017/3b82f6?text=Trade-in"
                     alt="Thu cũ đổi mới tại NovaPhone"
                     class="w-64 rounded-2xl border border-white/10 shadow-xl shadow-black/40">
            </div>
        </div>
    </div>
</section>

{{-- ===================== 7. Tech Journal ===================== --}}
<section id="tech-journal" class="mx-auto max-w-7xl scroll-mt-24 px-4 py-10 sm:px-6">
    <div class="reveal mb-6 flex items-end justify-between">
        <h2 class="text-xl font-extrabold tracking-tight text-white sm:text-2xl">Tech Journal</h2>
        <a href="#" class="inline-flex items-center gap-1 text-xs font-semibold text-brand-400 transition-colors duration-200 hover:text-brand-300">
            Xem tất cả
            <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
        </a>
    </div>

    <div class="reveal-stagger grid grid-cols-2 gap-4 lg:grid-cols-4">
        @foreach ($news as $article)
            <a href="#"
               class="group overflow-hidden rounded-2xl border border-white/5 bg-night-card transition-all duration-200 ease-in-out hover:-translate-y-1.5 hover:border-brand-500/40 hover:shadow-xl hover:shadow-black/50">
                <div class="skeleton overflow-hidden">
                    <img src="{{ $article['image'] }}" alt="{{ $article['title'] }}" loading="lazy" data-skeleton
                         class="aspect-[8/5] w-full object-cover transition-transform duration-300 ease-in-out group-hover:scale-105">
                </div>
                <div class="p-4">
                    <h3 class="line-clamp-2 text-sm font-bold leading-snug text-gray-100 transition-colors duration-200 group-hover:text-brand-400">
                        {{ $article['title'] }}
                    </h3>
                    <p class="mt-2 text-[11px] text-gray-500">{{ $article['date'] }}</p>
                </div>
            </a>
        @endforeach
    </div>
</section>

{{-- ===================== 8. Đăng ký nhận tin ===================== --}}
<section class="mx-auto max-w-7xl px-4 pb-14 pt-4 sm:px-6">
    <div class="reveal relative overflow-hidden rounded-3xl border border-brand-500/20 bg-gradient-to-r from-brand-950 to-night-soft px-6 py-8 sm:px-10">
        <div class="hero-glow absolute inset-0 opacity-50"></div>
        <div class="relative flex flex-col items-center justify-between gap-6 lg:flex-row">
            <div class="flex items-center gap-4">
                <span class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-brand-600/20 text-brand-400">
                    <svg class="size-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.24a2.25 2.25 0 0 1-1.07 1.92l-7.5 4.61a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.92v-.24"/></svg>
                </span>
                <div class="text-center lg:text-left">
                    <h2 class="text-lg font-extrabold tracking-tight text-white sm:text-xl">Đăng ký nhận tin khuyến mãi</h2>
                    <p class="mt-1 text-xs text-gray-400">Nhận ngay ưu đãi độc quyền từ NovaPhone</p>
                </div>
            </div>
            <form class="flex w-full max-w-md gap-2" onsubmit="return false">
                <input type="email" required placeholder="Nhập email của bạn"
                       class="min-w-0 flex-1 rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none transition-all duration-200 ease-in-out placeholder:text-gray-500 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/25">
                <button type="submit"
                        class="shrink-0 rounded-xl bg-brand-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-brand-600/30 transition-all duration-200 ease-in-out hover:-translate-y-0.5 hover:bg-brand-500 active:translate-y-0">
                    Đăng ký
                </button>
            </form>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    // ===== Đồng hồ đếm ngược Flash Sale (đếm về 0h ngày hôm sau) =====
    (function () {
        const box = document.querySelector('[data-countdown]');
        if (!box) return;

        const el = {
            gio: box.querySelector('[data-cd="gio"]'),
            phut: box.querySelector('[data-cd="phut"]'),
            giay: box.querySelector('[data-cd="giay"]'),
        };
        const pad = (n) => String(n).padStart(2, '0');

        function tick() {
            const now = new Date();
            const end = new Date(now);
            end.setHours(24, 0, 0, 0); // hết ngày hôm nay

            let diff = Math.max(0, Math.floor((end - now) / 1000));
            el.gio.textContent = pad(Math.floor(diff / 3600));
            el.phut.textContent = pad(Math.floor((diff % 3600) / 60));
            el.giay.textContent = pad(diff % 60);
        }

        tick();
        setInterval(tick, 1000);
    })();
</script>
@endpush
