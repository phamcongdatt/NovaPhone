@extends('admin.layout')

@section('title', 'Lịch sử biến động kho')
@section('page-title', 'Lịch sử kho hàng')
@section('page-subtitle', 'Nhật ký toàn bộ hoạt động nhập kho, xuất kho và điều chỉnh tồn kho')

@section('content')
<div class="space-y-5">

    {{-- Bộ lọc & Tìm kiếm --}}
    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <form method="GET" action="{{ route('admin.inventory.history') }}" class="flex flex-1 flex-wrap items-center gap-2">
            <div class="relative min-w-[240px] flex-1">
                <input
                    type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                    placeholder="Tìm theo sản phẩm, mã đơn hoặc ghi chú..."
                    class="w-full rounded-xl border border-white/10 bg-white/5 py-2.5 pl-10 pr-4 text-xs text-white outline-none transition-all duration-200 placeholder:text-gray-500 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/25"
                >
                <svg class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 10.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z"/></svg>
            </div>

            <select name="type" class="rounded-xl border border-white/10 bg-[#081321] px-3 py-2.5 text-xs text-white outline-none focus:border-brand-500">
                <option value="">Tất cả hoạt động</option>
                <option value="import" @selected(($filters['type'] ?? null) === 'import')>Nhập kho (Import)</option>
                <option value="export" @selected(($filters['type'] ?? null) === 'export')>Xuất kho (Export)</option>
                <option value="adjust" @selected(($filters['type'] ?? null) === 'adjust')>Điều chỉnh (Adjust)</option>
            </select>

            <button type="submit" class="rounded-xl bg-blue-600 px-4 py-2.5 text-xs font-semibold text-white transition-all duration-200 hover:bg-blue-500">
                Lọc nhật ký
            </button>

            @if (collect($filters)->filter()->isNotEmpty())
                <a href="{{ route('admin.inventory.history') }}" class="text-xs text-gray-500 transition-colors hover:text-gray-300">Xoá lọc</a>
            @endif
        </form>

        <a href="{{ route('admin.inventory.index') }}"
           class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-white/5 border border-white/10 px-5 py-2.5 text-xs font-bold text-white shadow-lg transition-all duration-200 hover:bg-white/10">
            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            Quay lại kho hàng
        </a>
    </div>

    {{-- Bảng lịch sử --}}
    <div class="overflow-x-auto rounded-2xl border border-white/5 bg-[#081321]">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="border-b border-white/5 text-[10px] uppercase tracking-wider text-gray-500">
                    <th class="px-4 py-3.5">Thời gian</th>
                    <th class="px-4 py-3.5">Sản phẩm</th>
                    <th class="px-4 py-3.5 text-center">Hoạt động</th>
                    <th class="px-4 py-3.5 text-center">Số lượng</th>
                    <th class="px-4 py-3.5">Ghi chú</th>
                    <th class="px-4 py-3.5">Người thực hiện</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse ($histories as $history)
                    @php
                        $product = $history->product;
                        $variant = $history->variant;
                    @endphp
                    <tr class="transition-colors duration-150 hover:bg-white/[0.02]">
                        {{-- Thời gian --}}
                        <td class="px-4 py-3.5 text-slate-400 whitespace-nowrap">
                            {{ $history->created_at->format('H:i:s d/m/Y') }}
                            <p class="text-[9px] text-gray-600">{{ $history->created_at->diffForHumans() }}</p>
                        </td>

                        {{-- Sản phẩm --}}
                        <td class="px-4 py-3.5">
                            <div class="min-w-0">
                                @if($product)
                                    <p class="font-semibold text-white text-xs truncate max-w-sm">{{ $product->name }}</p>
                                    @if ($variant)
                                        <p class="mt-0.5 text-[10px] text-brand-400 font-medium">Phiên bản: {{ $variant->name }} ({{ $variant->storage }} | {{ $variant->color }})</p>
                                    @endif
                                    <p class="mt-0.5 text-[9px] text-gray-500">SKU: {{ $variant->sku ?? $product->sku }}</p>
                                @else
                                    <p class="text-red-400 italic">Sản phẩm đã bị xóa</p>
                                @endif
                            </div>
                        </td>

                        {{-- Hoạt động --}}
                        <td class="px-4 py-3.5 text-center">
                            @if ($history->type === 'import')
                                <span class="rounded bg-emerald-500/10 border border-emerald-500/20 px-2 py-0.5 text-[10px] font-bold text-emerald-400 uppercase tracking-wider">
                                    Nhập kho
                                </span>
                            @elseif ($history->type === 'export')
                                <span class="rounded bg-red-500/10 border border-red-500/20 px-2 py-0.5 text-[10px] font-bold text-red-400 uppercase tracking-wider">
                                    Xuất kho
                                </span>
                            @elseif ($history->type === 'adjust')
                                <span class="rounded bg-blue-500/10 border border-blue-500/20 px-2 py-0.5 text-[10px] font-bold text-blue-400 uppercase tracking-wider">
                                    Điều chỉnh
                                </span>
                            @endif
                        </td>

                        {{-- Số lượng thay đổi --}}
                        <td class="px-4 py-3.5 text-center font-bold text-xs">
                            @if ($history->type === 'import')
                                <span class="text-emerald-400">+{{ $history->quantity }}</span>
                            @elseif ($history->type === 'export')
                                <span class="text-red-400">-{{ $history->quantity }}</span>
                            @elseif ($history->type === 'adjust')
                                @if ($history->quantity > 0)
                                    <span class="text-emerald-400">+{{ $history->quantity }}</span>
                                @elseif ($history->quantity < 0)
                                    <span class="text-red-400">{{ $history->quantity }}</span>
                                @else
                                    <span class="text-slate-400">0</span>
                                @endif
                            @endif
                        </td>

                        {{-- Ghi chú --}}
                        <td class="px-4 py-3.5 text-slate-300 text-xs italic">{{ $history->note ?? '—' }}</td>

                        {{-- Người thực hiện --}}
                        <td class="px-4 py-3.5 text-slate-400 font-medium">
                            @if ($history->user)
                                <div class="flex items-center gap-1.5">
                                    <span class="flex size-5 items-center justify-center rounded-full bg-white/5 text-[9px] font-bold text-slate-300">
                                        {{ mb_strtoupper(mb_substr($history->user->name, 0, 1)) }}
                                    </span>
                                    <span>{{ $history->user->name }}</span>
                                </div>
                            @else
                                <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-500">
                                    <span class="size-1 bg-slate-500 rounded-full"></span> Hệ thống
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-gray-500 text-xs">
                            Chưa có lịch sử giao dịch kho nào được ghi nhận.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Phân trang --}}
    <div class="mt-4">
        {{ $histories->links() }}
    </div>

</div>

{{-- BOOTSTRAP ICONS LINK --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endsection
