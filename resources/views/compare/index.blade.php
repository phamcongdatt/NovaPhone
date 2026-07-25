@extends('layouts.app')

@section('title', 'So sánh sản phẩm - NovaPhone')

@section('content')
    @php
        $comparisonRows = [
            ['key' => 'brand', 'label' => 'Thương hiệu'],
            ['key' => 'category', 'label' => 'Danh mục'],
            ['key' => 'effective_price', 'label' => 'Giá bán'],
            ['key' => 'rating_average', 'label' => 'Đánh giá'],
            ['key' => 'storage_options', 'label' => 'Dung lượng'],
            ['key' => 'color_options', 'label' => 'Màu sắc'],
            ['key' => 'available_quantity', 'label' => 'Tồn kho'],
        ];
        $performanceSpecs = $products->isNotEmpty()
            ? ($payload[$products->first()->id]['performance_specs'] ?? [])
            : [];
    @endphp

    <div class="space-y-5">
        <nav class="flex flex-wrap items-center gap-2 text-xs text-[#7a7a7a]" aria-label="Breadcrumb">
            <a href="{{ route('products.index') }}" class="transition hover:text-black">Sản phẩm</a>
            <span aria-hidden="true">/</span>
            <span class="font-semibold text-[#222]">So sánh sản phẩm</span>
        </nav>

        <section class="rounded-[28px] border border-[#ece8e2] bg-white p-5 shadow-[0_10px_35px_rgba(0,0,0,.04)] sm:p-7">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[#8b8b8b]">So sánh</p>
                    <h1 class="mt-2 text-2xl font-bold tracking-tight text-[#171717]">So sánh sản phẩm</h1>
                    <p class="mt-2 text-sm text-[#777]">
                        @if ($products->isNotEmpty())
                            Đang so sánh {{ $products->count() }} sản phẩm bạn đã chọn.
                        @else
                            Chọn sản phẩm để đối chiếu thông số và giá bán.
                        @endif
                    </p>
                </div>
                @if ($products->isNotEmpty())
                    <button type="button" data-compare-clear data-clear-url="{{ route('compare.clear') }}" class="inline-flex w-fit items-center justify-center rounded-full border border-[#e8e4de] bg-white px-4 py-2.5 text-sm font-semibold text-[#333] transition-colors duration-300 hover:border-red-300 hover:bg-red-50 hover:text-red-600">Xóa tất cả</button>
                @endif
            </div>

            @if ($products->isNotEmpty())
                <div class="mt-7 overflow-x-auto rounded-[22px] border border-[#ece8e2] [scrollbar-width:thin]">
                    <table class="min-w-[900px] w-full border-separate border-spacing-0 text-left text-sm">
                        <thead>
                            <tr class="bg-[#fbfaf8]">
                                <th scope="col" class="sticky left-0 z-20 w-44 border-b border-r border-[#ece8e2] bg-[#fbfaf8] px-4 py-5 align-bottom text-xs font-semibold uppercase tracking-[0.08em] text-[#777]">Thông số</th>
                                @foreach ($products as $product)
                                    @php
                                        $thumbnail = $product->thumbnail;
                                        $thumbnailUrl = ! $thumbnail
                                            ? asset('images/placeholder.svg')
                                            : (str_starts_with($thumbnail, 'http')
                                                ? $thumbnail
                                                : (str_starts_with($thumbnail, 'images/') || str_starts_with($thumbnail, 'storage/')
                                                    ? asset($thumbnail)
                                                    : asset('storage/' . $thumbnail)));
                                        $productPayload = $payload[$product->id] ?? [];
                                    @endphp
                                    <th scope="col" class="min-w-[220px] border-b border-[#ece8e2] px-4 py-4 align-top last:border-r-0">
                                        <div class="relative">
                                            <button type="button" data-compare-remove data-remove-url="{{ route('compare.remove', $product->id) }}" class="absolute right-0 top-0 grid size-7 place-items-center rounded-full border border-[#e8e4de] bg-white text-sm text-[#777] transition-colors duration-300 hover:border-red-200 hover:bg-red-50 hover:text-red-600" aria-label="Xóa {{ $product->name }} khỏi so sánh">×</button>
                                            <a href="{{ route('products.show', $product) }}" class="flex h-28 items-center justify-center pr-8">
                                                <img src="{{ $thumbnailUrl }}" alt="{{ $product->name }}" loading="lazy" decoding="async" class="max-h-24 max-w-full object-contain transition-transform duration-300 hover:scale-105">
                                            </a>
                                            <a href="{{ route('products.show', $product) }}" class="mt-3 block line-clamp-2 text-sm font-semibold leading-5 text-[#171717] transition-colors duration-300 hover:text-[#1677d2]">{{ $product->name }}</a>
                                            <p class="mt-2 text-base font-bold text-[#171717]">{{ number_format($productPayload['effective_price'] ?? $product->effective_price, 0, ',', '.') }}₫</p>
                                            @if (!empty($productPayload['discount_percent']))
                                                <p class="mt-1 text-xs font-semibold text-[#d14b4b]">Giảm {{ $productPayload['discount_percent'] }}%</p>
                                            @endif
                                            <a href="{{ route('products.show', $product) }}" class="mt-4 inline-flex rounded-full border border-[#e8e4de] bg-white px-3 py-1.5 text-xs font-semibold text-[#222] transition-colors duration-300 hover:border-black hover:bg-[#faf9f7]">Xem sản phẩm</a>
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#ece8e2]">
                            <tr>
                                <th colspan="{{ $products->count() + 1 }}" class="sticky left-0 z-10 bg-white px-4 py-3 text-xs font-bold uppercase tracking-[0.1em] text-[#777]">Thông tin chung</th>
                            </tr>
                            @foreach ($comparisonRows as $row)
                                <tr>
                                    <th scope="row" class="sticky left-0 z-10 border-r border-[#ece8e2] bg-white px-4 py-3.5 font-medium text-[#666]">{{ $row['label'] }}</th>
                                    @foreach ($products as $product)
                                        @php
                                            $value = $payload[$product->id][$row['key']] ?? null;
                                        @endphp
                                        <td class="px-4 py-3.5 font-semibold text-[#222]">
                                            @if ($row['key'] === 'effective_price')
                                                {{ $value !== null ? number_format($value, 0, ',', '.') . '₫' : 'Chưa cập nhật' }}
                                            @elseif ($row['key'] === 'rating_average')
                                                @if ($value !== null)
                                                    <span class="inline-flex items-center gap-1.5 text-[#222]"><span class="text-[#f59e0b]">★</span>{{ number_format($value, 1) }}/5 <span class="font-normal text-[#888]">({{ $payload[$product->id]['rating_count'] ?? 0 }})</span></span>
                                                @else
                                                    <span class="font-normal text-[#888]">Chưa có đánh giá</span>
                                                @endif
                                            @elseif (in_array($row['key'], ['storage_options', 'color_options'], true))
                                                {{ !empty($value) ? implode(', ', $value) : 'Chưa cập nhật' }}
                                            @elseif ($row['key'] === 'available_quantity')
                                                {{ $value !== null ? $value : 'Chưa cập nhật' }}
                                            @else
                                                {{ $value ?: 'Chưa cập nhật' }}
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach

                            @if ($performanceSpecs)
                                <tr>
                                    <th colspan="{{ $products->count() + 1 }}" class="sticky left-0 z-10 bg-[#fbfaf8] px-4 py-3 text-xs font-bold uppercase tracking-[0.1em] text-[#777]">Thông số kỹ thuật</th>
                                </tr>
                                @foreach ($performanceSpecs as $specification)
                                    <tr>
                                        <th scope="row" class="sticky left-0 z-10 border-r border-[#ece8e2] bg-white px-4 py-3.5 font-medium text-[#666]">{{ $specification['label'] }}</th>
                                        @foreach ($products as $product)
                                            @php
                                                $comparisonSpec = collect($payload[$product->id]['performance_specs'] ?? [])->firstWhere('key', $specification['key']);
                                            @endphp
                                            <td class="px-4 py-3.5 font-semibold text-[#222]">
                                                @if ($comparisonSpec && ($comparisonSpec['value'] ?? null) !== null)
                                                    {{ $comparisonSpec['value'] }}{{ $comparisonSpec['unit'] ?? '' }}
                                                @else
                                                    <span class="font-normal text-[#999]">Chưa cập nhật</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                <p class="mt-3 text-xs text-[#8b8b8b]">Kéo ngang để xem đầy đủ khi màn hình nhỏ.</p>
            @else
                <div class="mt-7 rounded-[22px] border border-dashed border-[#dfdad2] bg-[#fbfaf8] px-5 py-14 text-center">
                    <svg class="mx-auto size-10 text-[#a6a09a]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3.75H5.5A1.75 1.75 0 0 0 3.75 5.5v13A1.75 1.75 0 0 0 5.5 20.25h2.75m7.5-16.5h2.75A1.75 1.75 0 0 1 20.25 5.5v13a1.75 1.75 0 0 1-1.75 1.75h-2.75M8.25 8.25h7.5m-7.5 7.5h7.5M12 5.25v13.5"/></svg>
                    <h2 class="mt-4 text-lg font-bold text-[#171717]">Chưa có sản phẩm để so sánh</h2>
                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-[#777]">Thêm sản phẩm từ danh sách hoặc trang chi tiết sản phẩm để bắt đầu so sánh.</p>
                    <a href="{{ route('products.index') }}" class="mt-6 inline-flex rounded-full bg-black px-5 py-2.5 text-sm font-semibold text-white transition-colors duration-300 hover:bg-[#222]">Khám phá sản phẩm</a>
                </div>
            @endif
        </section>
    </div>
@endsection
