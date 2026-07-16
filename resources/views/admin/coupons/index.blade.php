@extends('admin.layout')

@section('title', 'Mã giảm giá')
@section('page-title', 'Quản lý mã giảm giá')

@section('content')

<div class="flex flex-col gap-5">

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="rounded-xl border border-green-500/20 bg-green-500/10 px-4 py-3 text-sm text-green-400">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->has('error'))
        <div class="rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-sm text-red-400">
            {{ $errors->first('error') }}
        </div>
    @endif

    {{-- Thanh công cụ: tìm kiếm + nút thêm --}}
    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <form method="GET" action="{{ route('admin.coupons.index') }}" class="relative min-w-[240px] flex-1 lg:max-w-sm">
            <input
                type="text" name="search" value="{{ $search }}"
                placeholder="Tìm mã hoặc mô tả..."
                class="w-full rounded-xl border border-white/10 bg-white/5 py-2.5 pl-10 pr-4 text-sm text-white outline-none transition-all duration-200 placeholder:text-gray-500 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/25">
            <svg class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 10.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z"/></svg>
        </form>

        <a
            href="{{ route('admin.coupons.create') }}"
            class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-brand-600/25 transition-all duration-200 hover:-translate-y-0.5 hover:bg-brand-500">
            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Thêm mã giảm giá
        </a>
    </div>

    {{-- Bảng danh sách --}}
    <div class="overflow-x-auto rounded-2xl border border-white/5 bg-night-soft">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b border-white/5 text-xs uppercase tracking-wider text-gray-500">
                    <th class="px-4 py-3">Mã giảm giá</th>
                    <th class="px-4 py-3">Loại / Giá trị</th>
                    <th class="px-4 py-3">Điều kiện</th>
                    <th class="px-4 py-3 text-center">Đã dùng / Giới hạn</th>
                    <th class="px-4 py-3 text-center">Trạng thái</th>
                    <th class="px-4 py-3 text-right">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($coupons as $coupon)
                    <tr class="transition-colors duration-150 hover:bg-white/[0.03]">
                        <td class="px-4 py-3">
                            <p class="font-bold text-brand-400">{{ $coupon->code }}</p>
                            @if ($coupon->description)
                                <p class="mt-0.5 max-w-[200px] truncate text-xs text-gray-500" title="{{ $coupon->description }}">{{ $coupon->description }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-300">
                            @if ($coupon->type === 'percent')
                                Giảm {{ (float) $coupon->value }}%
                                @if ($coupon->max_discount)
                                    <br><span class="text-xs text-gray-500">Tối đa {{ number_format($coupon->max_discount) }}đ</span>
                                @endif
                            @elseif ($coupon->type === 'fixed')
                                Giảm {{ number_format($coupon->value) }}đ
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-300">
                            @if ($coupon->min_order_amount > 0)
                                Đơn tối thiểu: <span class="font-semibold">{{ number_format($coupon->min_order_amount) }}đ</span><br>
                            @endif
                            @if ($coupon->starts_at || $coupon->expires_at)
                                <span class="text-xs text-gray-500 block mb-1">
                                    {{ $coupon->starts_at ? $coupon->starts_at->format('d/m/Y') : '...' }} - {{ $coupon->expires_at ? $coupon->expires_at->format('d/m/Y') : '...' }}
                                </span>
                            @endif
                            <div class="flex flex-wrap gap-1 mt-1">
                                @if ($coupon->is_apply_sale)
                                    <span class="inline-block rounded-md bg-white/10 px-1.5 py-0.5 text-[10px] text-gray-300" title="Áp dụng cùng hàng Sale">Hàng Sale</span>
                                @endif
                                @if ($coupon->is_apply_flash_sale)
                                    <span class="inline-block rounded-md bg-red-500/20 px-1.5 py-0.5 text-[10px] text-red-300" title="Áp dụng cùng Flash Sale">Flash Sale</span>
                                @endif
                                @if ($coupon->is_stackable)
                                    <span class="inline-block rounded-md bg-blue-500/20 px-1.5 py-0.5 text-[10px] text-blue-300" title="Cho phép cộng dồn với mã khác">Cộng dồn</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-300">
                            {{ $coupon->used_count }} / {{ $coupon->usage_limit ?: '∞' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if ($coupon->isUsable())
                                <span class="rounded-full bg-green-500/15 px-3 py-1 text-xs font-bold text-green-400">
                                    Hiệu lực
                                </span>
                            @else
                                <span class="rounded-full bg-red-500/15 px-3 py-1 text-xs font-bold text-red-400">
                                    Vô hiệu
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex items-center gap-1.5">
                                <a href="{{ route('admin.coupons.edit', $coupon) }}"
                                   class="flex size-8 items-center justify-center rounded-lg bg-white/5 text-gray-400 transition-all duration-200 hover:bg-brand-600/20 hover:text-brand-400"
                                   title="Sửa">
                                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/></svg>
                                </a>
                                <form
                                    method="POST"
                                    action="{{ route('admin.coupons.destroy', $coupon) }}"
                                    onsubmit="return confirm('Xoá mã giảm giá này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        title="Xoá"
                                        class="flex size-8 items-center justify-center rounded-lg bg-white/5 text-gray-400 transition-all duration-200 hover:bg-red-500/20 hover:text-red-400">
                                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                            Chưa có mã giảm giá nào.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($coupons->hasPages())
        <div class="mt-4">
            {{ $coupons->links() }}
        </div>
    @endif

</div>

@endsection
