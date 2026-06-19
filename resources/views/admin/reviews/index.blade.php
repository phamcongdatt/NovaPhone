@extends('admin.layout')

@section('title', 'Bình luận')
@section('page-title', 'Bình luận')
@section('page-subtitle', 'Quản lý đánh giá & bình luận sản phẩm')

@php
    $img = function ($path) {
        if (! $path) return asset('images/placeholder.png');
        return str_starts_with($path, 'http') ? $path : asset('storage/' . $path);
    };
@endphp

@section('content')

    {{-- ══════════ Thẻ thống kê ══════════ --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        @php
            $statCards = [
                ['Tổng bình luận', $stats['total'], 'bg-brand-500/15 text-brand-400', 'M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.068.157 2.148.279 3.238.364.466.037.893.281 1.153.671L12 21l2.652-3.978c.26-.39.687-.634 1.153-.67 1.09-.086 2.17-.208 3.238-.365 1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z'],
                ['Đang hiển thị', $stats['visible'], 'bg-emerald-500/15 text-emerald-400', 'M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178ZM15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z'],
                ['Đang ẩn', $stats['hidden'], 'bg-gray-500/15 text-gray-400', 'M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88'],
                ['Điểm trung bình', number_format($stats['avg'], 1, ',', '.'), 'bg-amber-500/15 text-amber-400', 'M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z'],
            ];
        @endphp
        @foreach ($statCards as [$label, $value, $box, $icon])
            <div class="rounded-2xl border border-white/5 bg-night-card p-4 sm:p-5">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-500">{{ $label }}</p>
                    <span class="flex size-9 items-center justify-center rounded-xl {{ $box }}">
                        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/></svg>
                    </span>
                </div>
                <p class="mt-2 text-2xl font-extrabold text-white">{{ is_numeric($value) && $value == (int) $value ? number_format($value, 0, ',', '.') : $value }}</p>
            </div>
        @endforeach
    </div>

    {{-- ══════════ Bộ lọc ══════════ --}}
    <form method="GET" class="mt-4 flex flex-col gap-3 rounded-2xl border border-white/5 bg-night-card p-4 sm:flex-row sm:items-center">
        <div class="relative flex-1">
            <svg class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Tìm theo nội dung, khách hàng, sản phẩm..."
                   class="w-full rounded-xl border border-white/10 bg-white/5 py-2.5 pl-10 pr-4 text-sm text-gray-200 placeholder:text-gray-500 focus:border-brand-500/50 focus:outline-none">
        </div>
        <select name="status" class="rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 text-sm text-gray-200 focus:border-brand-500/50 focus:outline-none">
            <option value="" class="bg-night-card">Tất cả trạng thái</option>
            <option value="visible" class="bg-night-card" @selected(($filters['status'] ?? '') === 'visible')>Đang hiển thị</option>
            <option value="hidden" class="bg-night-card" @selected(($filters['status'] ?? '') === 'hidden')>Đang ẩn</option>
        </select>
        <select name="rating" class="rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 text-sm text-gray-200 focus:border-brand-500/50 focus:outline-none">
            <option value="" class="bg-night-card">Mọi đánh giá</option>
            @for ($s = 5; $s >= 1; $s--)
                <option value="{{ $s }}" class="bg-night-card" @selected((string) ($filters['rating'] ?? '') === (string) $s)>{{ $s }} sao</option>
            @endfor
        </select>
        <button class="rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-brand-500">Lọc</button>
        @if (array_filter($filters ?? []))
            <a href="{{ route('admin.reviews.index') }}" class="rounded-xl border border-white/10 px-4 py-2.5 text-center text-sm font-semibold text-gray-400 transition hover:text-white">Xóa lọc</a>
        @endif
    </form>

    {{-- ══════════ Bảng bình luận ══════════ --}}
    <div class="mt-4 overflow-hidden rounded-2xl border border-white/5 bg-night-card">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[820px] text-sm">
                <thead>
                    <tr class="border-b border-white/5 bg-white/[0.02] text-left text-xs uppercase tracking-wider text-gray-500">
                        <th class="px-5 py-3.5 font-semibold">Khách hàng</th>
                        <th class="px-5 py-3.5 font-semibold">Sản phẩm</th>
                        <th class="px-5 py-3.5 font-semibold">Đánh giá & nội dung</th>
                        <th class="px-5 py-3.5 font-semibold">Ngày</th>
                        <th class="px-5 py-3.5 text-center font-semibold">Trạng thái</th>
                        <th class="px-5 py-3.5 text-right font-semibold">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse ($reviews as $review)
                        <tr class="align-top transition hover:bg-white/[0.02]">
                            {{-- Khách hàng --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2.5">
                                    <span class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 text-sm font-bold text-white">{{ mb_strtoupper(mb_substr($review->user->name ?? 'K', 0, 1)) }}</span>
                                    <div class="min-w-0">
                                        <p class="truncate font-semibold text-gray-200">{{ $review->user->name ?? 'Khách' }}</p>
                                        <p class="truncate text-xs text-gray-500">{{ $review->user->email ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            {{-- Sản phẩm --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2.5">
                                    <img src="{{ $img($review->product->thumbnail ?? null) }}" alt="" class="size-9 shrink-0 rounded-lg border border-white/5 bg-white/5 object-contain p-0.5">
                                    <span class="block max-w-[160px] truncate text-gray-300">{{ $review->product->name ?? 'Sản phẩm đã xóa' }}</span>
                                </div>
                            </td>
                            {{-- Đánh giá + nội dung --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-0.5">
                                    @for ($s = 1; $s <= 5; $s++)
                                        <svg class="size-4 {{ $s <= $review->rating ? 'text-amber-400' : 'text-gray-600' }}" fill="currentColor" viewBox="0 0 24 24"><path d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"/></svg>
                                    @endfor
                                </div>
                                <p class="mt-1.5 max-w-sm text-gray-300">{{ $review->comment ?: '(Không có nội dung)' }}</p>
                            </td>
                            {{-- Ngày --}}
                            <td class="whitespace-nowrap px-5 py-4 text-gray-400">{{ $review->created_at?->format('d/m/Y') }}</td>
                            {{-- Trạng thái --}}
                            <td class="px-5 py-4 text-center">
                                @if ($review->is_visible)
                                    <span class="inline-flex rounded-full border border-emerald-500/20 bg-emerald-500/10 px-2.5 py-1 text-xs font-semibold text-emerald-400">Hiển thị</span>
                                @else
                                    <span class="inline-flex rounded-full border border-gray-500/20 bg-gray-500/10 px-2.5 py-1 text-xs font-semibold text-gray-400">Đã ẩn</span>
                                @endif
                            </td>
                            {{-- Thao tác --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <form method="POST" action="{{ route('admin.reviews.toggle', $review) }}">
                                        @csrf @method('PATCH')
                                        <button title="{{ $review->is_visible ? 'Ẩn bình luận' : 'Hiển thị bình luận' }}"
                                                class="rounded-lg border border-white/10 p-2 text-gray-400 transition hover:border-brand-500/40 hover:text-brand-400">
                                            @if ($review->is_visible)
                                                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                                            @else
                                                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178ZM15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                                            @endif
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('Xóa bình luận này?');">
                                        @csrf @method('DELETE')
                                        <button title="Xóa bình luận"
                                                class="rounded-lg border border-white/10 p-2 text-gray-400 transition hover:border-red-500/40 hover:text-red-400">
                                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-16 text-center">
                                <svg class="mx-auto size-12 text-gray-700" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-.923 1.785A5.969 5.969 0 0 0 6 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337Z"/></svg>
                                <p class="mt-3 text-sm font-semibold text-gray-400">Chưa có bình luận nào</p>
                                <p class="mt-1 text-xs text-gray-600">Bình luận của khách hàng về sản phẩm sẽ hiển thị tại đây.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($reviews->hasPages())
        <div class="mt-4">{{ $reviews->links() }}</div>
    @endif

@endsection
