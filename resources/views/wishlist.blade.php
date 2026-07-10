@extends('layouts.app')

@section('title', 'Sản phẩm yêu thích — NovaPhone')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:py-12">
    {{-- Breadcrumb --}}
    <nav class="mb-6 flex items-center text-sm font-medium text-gray-500">
        <a href="{{ route('home') }}" class="hover:text-brand-400 transition-colors">Trang chủ</a>
        <svg class="mx-2 h-4 w-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-300">Sản phẩm yêu thích</span>
    </nav>

    <div class="mb-8 flex items-end justify-between">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-white sm:text-3xl">Sản phẩm yêu thích</h1>
            <p class="mt-2 text-sm text-gray-400">Danh sách các sản phẩm bạn đang quan tâm.</p>
        </div>
        <div class="hidden sm:block">
            <span class="rounded-full bg-white/5 px-4 py-1.5 text-sm font-semibold text-gray-300 border border-white/10">
                Tổng cộng: <span class="text-brand-400" id="wishlist-total-count">{{ $wishlists->count() }}</span> sản phẩm
            </span>
        </div>
    </div>

    @if ($wishlists->isEmpty())
        <div class="flex flex-col items-center justify-center rounded-3xl border border-white/5 bg-night-soft py-24 px-4 text-center">
            <div class="mb-6 flex size-24 items-center justify-center rounded-full bg-white/5 text-gray-500">
                <svg class="size-12" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.49-2.1-4.5-4.69-4.5-1.94 0-3.6 1.13-4.31 2.73a4.72 4.72 0 0 0-4.31-2.73C5.1 3.75 3 5.76 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                </svg>
            </div>
            <h2 class="text-lg font-bold text-white sm:text-xl">Danh sách yêu thích trống</h2>
            <p class="mt-2 mb-6 max-w-sm text-sm text-gray-400">Bạn chưa lưu bất kỳ sản phẩm nào. Hãy khám phá và lưu lại những sản phẩm bạn thích nhé!</p>
            <a href="{{ route('home') }}#san-pham" class="rounded-xl bg-brand-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-brand-600/20 transition-all duration-200 hover:bg-brand-500 hover:-translate-y-0.5">
                Tiếp tục mua sắm
            </a>
        </div>
    @else
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
            @foreach ($wishlists as $wishlist)
                @php
                    $product = $wishlist->product;
                    $discount = null;
                    if ($product->sale_price && $product->sale_price < $product->price) {
                        $discount = (int) round((($product->price - $product->sale_price) / $product->price) * 100);
                    }
                @endphp
                
                <div class="wishlist-item relative">
                    <x-product-card
                        :id="$product->id"
                        :name="$product->name"
                        :image="$product->thumbnail ?: asset('images/placeholder.svg')"
                        :price="$product->effective_price"
                        :old-price="$product->sale_price ? $product->price : null"
                        :discount="$discount"
                        :rating="$product->rating_average ? round($product->rating_average, 1) : null"
                        :sold="$product->sold_count ? number_format($product->sold_count, 0, ',', '.') : null"
                        :href="route('products.show', $product)"
                    />
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
