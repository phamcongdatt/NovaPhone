@extends('layouts.app')

@section('title', 'Danh sách sản phẩm | NovaPhone')

@php
    $money = fn ($value) => number_format((float) $value, 0, ',', '.').'đ';
@endphp

@section('content')
<div class="bg-night text-gray-100 pb-12 min-h-screen">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6">
        
        <h1 class="text-3xl font-black text-white mb-8">
            @if(request('search'))
                Kết quả tìm kiếm cho: "{{ request('search') }}"
            @else
                Điện thoại di động
            @endif
        </h1>

        <div class="flex flex-col lg:flex-row gap-8">
            {{-- Sidebar Filter --}}
            <aside class="w-full lg:w-64 shrink-0 space-y-6">
                <form action="{{ route('products.index') }}" method="GET" id="filter-form" class="space-y-6">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    
                    {{-- Lọc Hãng --}}
                    <div class="rounded-xl border border-white/5 bg-night-soft p-5 shadow-lg shadow-black/20">
                        <h3 class="font-bold text-white mb-4">Thương hiệu</h3>
                        <div class="space-y-2 max-h-60 overflow-y-auto no-scrollbar">
                            @foreach($brands as $brand)
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="radio" name="brand" value="{{ $brand->id }}" 
                                           class="text-brand-500 focus:ring-brand-500 focus:ring-offset-night-soft bg-night border-white/20"
                                           {{ request('brand') == $brand->id ? 'checked' : '' }}
                                           onchange="document.getElementById('filter-form').submit()">
                                    <span class="text-sm text-gray-400 group-hover:text-gray-200 transition-colors">{{ $brand->name }}</span>
                                </label>
                            @endforeach
                            <label class="flex items-center gap-3 cursor-pointer group mt-2 pt-2 border-t border-white/10">
                                <input type="radio" name="brand" value="" 
                                       class="text-brand-500 focus:ring-brand-500 focus:ring-offset-night-soft bg-night border-white/20"
                                       {{ !request('brand') ? 'checked' : '' }}
                                       onchange="document.getElementById('filter-form').submit()">
                                <span class="text-sm font-medium text-brand-400">Tất cả hãng</span>
                            </label>
                        </div>
                    </div>

                    {{-- Lọc Giá --}}
                    <div class="rounded-xl border border-white/5 bg-night-soft p-5 shadow-lg shadow-black/20">
                        <h3 class="font-bold text-white mb-4">Mức giá</h3>
                        <div class="space-y-2">
                            @php
                                $priceRanges = [
                                    '' => 'Tất cả mức giá',
                                    'under-5m' => 'Dưới 5 triệu',
                                    '5m-10m' => 'Từ 5 - 10 triệu',
                                    '10m-20m' => 'Từ 10 - 20 triệu',
                                    'above-20m' => 'Trên 20 triệu',
                                ];
                            @endphp
                            @foreach($priceRanges as $value => $label)
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="radio" name="price" value="{{ $value }}" 
                                           class="text-brand-500 focus:ring-brand-500 focus:ring-offset-night-soft bg-night border-white/20"
                                           {{ request('price') == $value ? 'checked' : '' }}
                                           onchange="document.getElementById('filter-form').submit()">
                                    <span class="text-sm text-gray-400 group-hover:text-gray-200 transition-colors {{ !$value ? 'font-medium text-brand-400' : '' }}">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </form>
            </aside>

            {{-- Main Content --}}
            <div class="flex-1">
                <div class="mb-6 flex flex-wrap items-center justify-between gap-4 rounded-xl border border-white/5 bg-night-soft p-4 shadow-lg shadow-black/20">
                    <p class="text-sm text-gray-400">Tìm thấy <strong class="text-white">{{ $products->total() }}</strong> sản phẩm</p>
                    <div class="flex items-center gap-3">
                        <label for="sort" class="text-sm text-gray-400">Sắp xếp:</label>
                        <select form="filter-form" name="sort" id="sort" onchange="document.getElementById('filter-form').submit()" 
                                class="rounded-lg border-white/10 bg-night px-3 py-1.5 text-sm text-gray-200 focus:border-brand-500 focus:ring-brand-500">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Mới nhất</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá: Thấp đến cao</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá: Cao đến thấp</option>
                        </select>
                    </div>
                </div>

                @if($products->isEmpty())
                    <div class="rounded-2xl border border-white/5 bg-night-soft p-12 text-center shadow-lg shadow-black/20">
                        <div class="mx-auto flex size-20 items-center justify-center rounded-full bg-white/5">
                            <svg class="size-10 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-bold text-white">Không tìm thấy sản phẩm</h3>
                        <p class="mt-2 text-sm text-gray-400">Không có sản phẩm nào phù hợp với tiêu chí tìm kiếm của bạn. Hãy thử đổi bộ lọc khác.</p>
                        <a href="{{ route('products.index') }}" class="mt-6 inline-flex items-center justify-center rounded-xl bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-500">Xóa bộ lọc</a>
                    </div>
                @else
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                        @foreach($products as $product)
                            @php
                                $thumb = $product->images->firstWhere('is_primary')?->image_url ?? $product->thumbnail;
                            @endphp
                            <a href="{{ route('products.show', $product->slug) }}" class="group relative flex flex-col overflow-hidden rounded-2xl border border-white/5 bg-night-card p-4 transition-all hover:-translate-y-1 hover:border-brand-500/50 hover:shadow-xl hover:shadow-brand-500/10">
                                @if ($product->sale_price && $product->sale_price < $product->price)
                                    <span class="absolute left-3 top-3 z-10 rounded-full bg-red-600 px-2 py-0.5 text-xs font-bold text-white">
                                        -{{ round((($product->price - $product->sale_price) / $product->price) * 100) }}%
                                    </span>
                                @endif
                                
                                <div class="relative aspect-square overflow-hidden rounded-xl bg-white/5 p-4 flex items-center justify-center">
                                    @if($thumb)
                                        <img src="{{ str_starts_with($thumb, 'http') ? $thumb : asset('storage/' . $thumb) }}" alt="{{ $product->name }}" class="h-full w-full object-contain transition-transform duration-500 group-hover:scale-110">
                                    @else
                                        <img src="{{ asset('images/placeholder.svg') }}" alt="{{ $product->name }}" class="h-full w-full object-contain">
                                    @endif
                                </div>
                                
                                <div class="mt-4 flex flex-1 flex-col">
                                    <p class="text-xs font-medium text-brand-400 mb-1">{{ $product->brand->name ?? 'Điện thoại' }}</p>
                                    <h3 class="line-clamp-2 text-sm font-bold text-gray-100 group-hover:text-brand-300 flex-1">{{ $product->name }}</h3>
                                    <div class="mt-3 flex flex-wrap items-baseline gap-2">
                                        <span class="text-lg font-black text-brand-400">{{ $money($product->effective_price) }}</span>
                                        @if ($product->sale_price && $product->sale_price < $product->price)
                                            <span class="text-xs text-gray-500 line-through">{{ $money($product->price) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <div class="mt-10">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
