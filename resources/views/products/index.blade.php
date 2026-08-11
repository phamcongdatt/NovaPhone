@extends('layouts.app')

@section('title', 'Sản phẩm - NovaPhone')

@php
    $selectedBrand = $filters['brand'] ?? '';
    $selectedSearch = $filters['search'] ?? '';
    $selectedPrice = $filters['price'] ?? '';
    $selectedSort = $filters['sort'] ?? 'latest';
@endphp

@section('content')
<div class="space-y-4">
    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-[#8b8b8b]">
        <a href="{{ route('home') }}" class="hover:text-black">Trang chủ</a>
        <span>/</span>
        <span class="text-black">Sản phẩm</span>
    </div>

    {{-- Header --}}
    <section class="rounded-[28px] border border-[#ece8e2] bg-white p-5 sm:p-6 shadow-[0_10px_35px_rgba(0,0,0,.04)]">
        <div class="mb-6 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-2xl font-bold text-[#171717] sm:text-3xl">Danh sách sản phẩm</h1>
                <p class="mt-1 text-sm text-[#8b8b8b]">Khám phá bộ sưu tập đầy đủ của chúng tôi</p>
            </div>
            <span class="text-sm font-semibold text-[#8b8b8b]">{{ $products->total() }} sản phẩm</span>
        </div>

        {{-- Filters --}}
        <form method="GET" class="space-y-4">
            {{-- Search & Quick Filters --}}
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                <input
                    type="text"
                    name="search"
                    value="{{ $selectedSearch }}"
                    placeholder="Tìm kiếm sản phẩm..."
                    class="col-span-1 rounded-[16px] border border-[#e8e4de] bg-white px-4 py-2.5 text-sm outline-none transition focus:border-black sm:col-span-2 lg:col-span-2"
                >

                <select name="brand" class="rounded-[16px] border border-[#e8e4de] bg-white px-4 py-2.5 text-sm outline-none transition focus:border-black">
                    <option value="">Tất cả thương hiệu</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}" @selected((string) $selectedBrand === (string) $brand->id)>
                            {{ $brand->name }}
                        </option>
                    @endforeach
                </select>

                <select name="price" class="rounded-[16px] border border-[#e8e4de] bg-white px-4 py-2.5 text-sm outline-none transition focus:border-black">
                    <option value="">Mọi mức giá</option>
                    <option value="under-5m" @selected($selectedPrice === 'under-5m')>Dưới 5 triệu</option>
                    <option value="5m-10m" @selected($selectedPrice === '5m-10m')>5 - 10 triệu</option>
                    <option value="10m-20m" @selected($selectedPrice === '10m-20m')>10 - 20 triệu</option>
                    <option value="above-20m" @selected($selectedPrice === 'above-20m')>Trên 20 triệu</option>
                </select>

                <select name="sort" class="rounded-[16px] border border-[#e8e4de] bg-white px-4 py-2.5 text-sm outline-none transition focus:border-black">
                    <option value="latest" @selected($selectedSort === 'latest')>Mới nhất</option>
                    <option value="price_asc" @selected($selectedSort === 'price_asc')>Giá tăng dần</option>
                    <option value="price_desc" @selected($selectedSort === 'price_desc')>Giá giảm dần</option>
                </select>

                <button type="submit" class="rounded-[16px] bg-black px-4 py-2.5 text-sm font-semibold text-white transition-all hover:bg-[#222]">
                    Tìm kiếm
                </button>
            </div>

            {{-- Active Filters Tags --}}
            @if ($selectedSearch || $selectedBrand || $selectedPrice || $selectedSort !== 'latest')
                <div class="flex flex-wrap gap-2">
                    @if ($selectedSearch)
                        <span class="inline-flex items-center gap-2 rounded-full bg-[#f0eeea] px-3 py-1.5 text-xs font-semibold text-[#111]">
                            {{ $selectedSearch }}
                            <a href="{{ route('products.index', array_merge($filters, ['search' => ''])) }}" class="hover:text-red-600">X</a>
                        </span>
                    @endif
                    @if ($selectedBrand)
                        <span class="inline-flex items-center gap-2 rounded-full bg-[#f0eeea] px-3 py-1.5 text-xs font-semibold text-[#111]">
                            {{ $brands->find($selectedBrand)->name ?? 'Brand' }}
                            <a href="{{ route('products.index', array_merge($filters, ['brand' => ''])) }}" class="hover:text-red-600">X</a>
                        </span>
                    @endif
                    @if ($selectedPrice)
                        <span class="inline-flex items-center gap-2 rounded-full bg-[#f0eeea] px-3 py-1.5 text-xs font-semibold text-[#111]">
                            {{ ['under-5m' => 'Dưới 5 triệu', '5m-10m' => '5-10 triệu', '10m-20m' => '10-20 triệu', 'above-20m' => 'Trên 20 triệu'][$selectedPrice] ?? 'Mức giá' }}
                            <a href="{{ route('products.index', array_merge($filters, ['price' => ''])) }}" class="hover:text-red-600">X</a>
                        </span>
                    @endif
                    <a href="{{ route('products.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800">Xóa tất cả</a>
                </div>
            @endif
        </form>
    </section>

    {{-- Main Content --}}
    <div class="grid gap-4 lg:grid-cols-[280px_1fr]">
        {{-- Sidebar Brands --}}
        <aside class="rounded-[28px] border border-[#ece8e2] bg-white p-5 shadow-[0_10px_35px_rgba(0,0,0,.04)]">
            <h3 class="text-base font-bold text-[#171717]">Thương hiệu</h3>
            <nav class="mt-4 space-y-2">
                <a href="{{ route('products.index') }}" class="block rounded-[14px] px-3 py-2 text-sm font-medium text-[#111] transition hover:bg-[#f7f5f2]">
                    Tất cả sản phẩm
                </a>
                @foreach ($brands as $brand)
                    <a
                        href="{{ route('products.index', ['brand' => $brand->id]) }}"
                        class="flex items-center justify-between rounded-[14px] px-3 py-2 text-sm transition hover:bg-[#f7f5f2]"
                    >
                        <span class="font-medium text-[#111]">{{ $brand->name }}</span>
                    </a>
                @endforeach
            </nav>
        </aside>

        {{-- Products Grid --}}
        <section class="space-y-4">
            <div class="rounded-[28px] border border-[#ece8e2] bg-white p-5 shadow-[0_10px_35px_rgba(0,0,0,.04)]">
                @if ($products->isNotEmpty())
                    <div class="grid grid-cols-2 gap-3 md:grid-cols-3 xl:grid-cols-4">
                        @foreach ($products as $product)
                            @php
                                $discount = null;
                                if ($product->sale_price && $product->sale_price < $product->price) {
                                    $discount = round((1 - ($product->sale_price / $product->price)) * 100);
                                }
                            @endphp
                            <x-product-card
                                :id="$product->id"
                                :name="$product->name"
                                :variants="$product->variants"
                                :image="$product->thumbnail ? (str_starts_with($product->thumbnail, 'http') ? $product->thumbnail : (str_starts_with($product->thumbnail, 'images/') || str_starts_with($product->thumbnail, 'storage/') ? asset($product->thumbnail) : asset('storage/' . $product->thumbnail))) : asset('images/placeholder.svg')"
                                :price="$product->sale_price ?? $product->price"
                                :oldPrice="$product->sale_price && $product->sale_price < $product->price ? $product->price : null"
                                :discount="$discount"
                                :rating="isset($product->rating_average) && $product->rating_average ? round($product->rating_average, 1) : null"
                                :sold="$product->sold_count ? number_format($product->sold_count, 0, ',', '.') : null"
                                :href="route('products.show', $product)"
                            />
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if (method_exists($products, 'links'))
                        <div class="mt-6 flex justify-center">
                            {{ $products->links('pagination::tailwind') }}
                        </div>
                    @endif
                @else
                    <div class="rounded-2xl border-2 border-dashed border-[#e5e1db] py-12 text-center">
                        <p class="mt-3 text-sm text-[#8b8b8b]">Không tìm thấy sản phẩm phù hợp</p>
                        <p class="mt-1 text-xs text-[#b0b0b0]">Hãy thử thay đổi bộ lọc hoặc từ khóa tìm kiếm</p>
                        <a href="{{ route('products.index') }}" class="mt-4 inline-block rounded-full bg-black px-5 py-2 text-xs font-semibold text-white hover:bg-[#222]">
                            Xem tất cả sản phẩm
                        </a>
                    </div>
                @endif
            </div>
        </section>
    </div>
</div>

<style>
    .pagination a, .pagination span {
        @apply px-3 py-2 text-xs font-medium rounded-lg border border-[#ece8e2] transition;
    }
    .pagination a:hover {
        @apply border-black bg-[#f7f5f2];
    }
    .pagination .active span {
        @apply border-black bg-black text-white;
    }
</style>
@endsection
