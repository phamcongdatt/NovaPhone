@extends('admin.layout')

@section('title', 'Sản phẩm')
@section('page-title', 'Quản lý sản phẩm')

@section('content')

{{-- Thanh công cụ: tìm kiếm + lọc + nút thêm --}}
<div class="mb-5 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
    <form method="GET" action="{{ route('admin.products.index') }}" class="flex flex-1 flex-wrap items-center gap-2">
        <div class="relative min-w-[220px] flex-1">
            <input
                type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                placeholder="Tìm theo tên hoặc SKU..."
                class="w-full rounded-xl border border-white/10 bg-white/5 py-2.5 pl-10 pr-4 text-sm text-white outline-none transition-all duration-200 placeholder:text-gray-500 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/25"
            >
            <svg class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 10.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z"/></svg>
        </div>

        <select name="category_id" class="rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 text-sm text-white outline-none focus:border-brand-500 [&>option]:bg-gray-900 [&>option]:text-white">
            <option value="" class="bg-gray-900 text-white">Tất cả danh mục</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" class="bg-gray-900 text-white" @selected(($filters['category_id'] ?? null) == $cat->id)>{{ $cat->name }}</option>
            @endforeach
        </select>

        <select name="brand_id" class="rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 text-sm text-white outline-none focus:border-brand-500 [&>option]:bg-gray-900 [&>option]:text-white">
            <option value="" class="bg-gray-900 text-white">Tất cả thương hiệu</option>
            @foreach ($brands as $brand)
                <option value="{{ $brand->id }}" class="bg-gray-900 text-white" @selected(($filters['brand_id'] ?? null) == $brand->id)>{{ $brand->name }}</option>
            @endforeach
        </select>

        <select name="status" class="rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 text-sm text-white outline-none focus:border-brand-500 [&>option]:bg-gray-900 [&>option]:text-white">
            <option value="" class="bg-gray-900 text-white">Tất cả trạng thái</option>
            <option value="active" class="bg-gray-900 text-white" @selected(($filters['status'] ?? null) === 'active')>Đang bán</option>
            <option value="inactive" class="bg-gray-900 text-white" @selected(($filters['status'] ?? null) === 'inactive')>Đã ẩn</option>
        </select>

        <button type="submit" class="rounded-xl bg-white/5 px-4 py-2.5 text-sm font-semibold text-white transition-all duration-200 hover:bg-white/10">
            Lọc
        </button>

        @if (collect($filters)->filter()->isNotEmpty())
            <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-500 transition-colors hover:text-gray-300">Xoá lọc</a>
        @endif
    </form>

    <a href="{{ route('admin.products.create') }}"
       class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-brand-600/25 transition-all duration-200 hover:-translate-y-0.5 hover:bg-brand-500">
        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Thêm sản phẩm
    </a>
</div>

{{-- Bảng sản phẩm --}}
<div class="overflow-x-auto rounded-2xl border border-white/5 bg-night-soft">
    <table class="w-full text-left text-sm">
        <thead>
            <tr class="border-b border-white/5 text-xs uppercase tracking-wider text-gray-500">
                <th class="px-4 py-3">Sản phẩm</th>
                <th class="px-4 py-3">Danh mục</th>
                <th class="px-4 py-3">Thương hiệu</th>
                <th class="px-4 py-3">Giá bán</th>
                <th class="px-4 py-3 text-center">Biến thể</th>
                <th class="px-4 py-3 text-center">Tồn kho</th>
                <th class="px-4 py-3 text-center">Trạng thái</th>
                <th class="px-4 py-3 text-right">Hành động</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-white/5">
            @forelse ($products as $product)
                <tr class="transition-colors duration-150 hover:bg-white/[0.03]">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <img
                                src="{{ $product->thumbnail ? (str_starts_with($product->thumbnail, 'images/') ? asset($product->thumbnail) : asset('storage/' . $product->thumbnail)) : asset('images/placeholder.svg') }}"
                                alt="{{ $product->name }}"
                                class="size-12 shrink-0 rounded-lg border border-white/10 object-cover"
                            >
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-white">{{ $product->name }}</p>
                                <p class="mt-0.5 text-xs text-gray-500">SKU: {{ $product->sku }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-400">{{ $product->category->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-400">{{ $product->brand->name ?? '—' }}</td>
                    <td class="px-4 py-3">
                        @if ($product->sale_price)
                            <p class="font-bold text-amber-400">{{ number_format($product->sale_price) }}₫</p>
                            <p class="text-xs text-gray-500 line-through">{{ number_format($product->price) }}₫</p>
                        @else
                            <p class="font-bold text-white">{{ number_format($product->price) }}₫</p>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center text-gray-400">{{ $product->variants_count }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="{{ ($product->inventories_sum_quantity ?? 0) <= 5 ? 'text-red-400' : 'text-gray-300' }} font-semibold">
                            {{ $product->inventories_sum_quantity ?? 0 }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <form method="POST" action="{{ route('admin.products.toggle-status', $product) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                class="rounded-full px-3 py-1 text-xs font-bold transition-all duration-200
                                       {{ $product->is_active ? 'bg-green-500/15 text-green-400 hover:bg-green-500/25' : 'bg-gray-500/15 text-gray-400 hover:bg-gray-500/25' }}">
                                {{ $product->is_active ? 'Đang bán' : 'Đã ẩn' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="inline-flex items-center gap-1.5">
                            <a href="{{ route('admin.products.edit', $product) }}"
                               class="flex size-8 items-center justify-center rounded-lg bg-white/5 text-gray-400 transition-all duration-200 hover:bg-brand-600/20 hover:text-brand-400"
                               title="Sửa">
                                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/></svg>
                            </a>
                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                                  onsubmit="return confirm('Xoá sản phẩm này? Sản phẩm sẽ được chuyển vào thùng rác.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="flex size-8 items-center justify-center rounded-lg bg-white/5 text-gray-400 transition-all duration-200 hover:bg-red-500/20 hover:text-red-400"
                                        title="Xoá">
                                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-12 text-center text-gray-500">
                        Chưa có sản phẩm nào. <a href="{{ route('admin.products.create') }}" class="text-brand-400 hover:underline">Thêm sản phẩm đầu tiên</a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
<div class="mt-5">
    {{ $products->links() }}
</div>

@endsection