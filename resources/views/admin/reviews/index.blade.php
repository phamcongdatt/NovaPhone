@extends('admin.layout')

@section('title', 'Đánh giá')
@section('page-title', 'Quản lý đánh giá')

@section('content')

    <div class="mb-5 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <form method="GET" action="{{ route('admin.reviews.index') }}" class="flex flex-1 flex-wrap items-center gap-2">
            <div class="relative min-w-[260px] flex-1">
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}"
                    placeholder="Tìm theo tên sản phẩm, người dùng hoặc nội dung..."
                    class="w-full rounded-xl border border-white/10 bg-white/5 py-2.5 pl-10 pr-4 text-sm text-white outline-none transition-all duration-200 placeholder:text-gray-500 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/25">
                <svg class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-gray-500"
                    fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m21 21-4.35-4.35M17 10.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z" />
                </svg>
            </div>

            <select name="status"
                class="rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 text-sm text-white outline-none focus:border-brand-500">
                <option value="">Tất cả</option>
                <option value="pending" @selected(($filters['status'] ?? null) === 'pending')>Chờ duyệt</option>
                <option value="approved" @selected(($filters['status'] ?? null) === 'approved')>Đã duyệt</option>
            </select>

            <button type="submit"
                class="rounded-xl bg-white/5 px-4 py-2.5 text-sm font-semibold text-white transition-all duration-200 hover:bg-white/10">Lọc</button>

            @if (collect($filters)->filter()->isNotEmpty())
                <a href="{{ route('admin.reviews.index') }}"
                    class="text-sm text-gray-500 transition-colors hover:text-gray-300">Xoá lọc</a>
            @endif
        </form>

    </div>

    <div class="overflow-x-auto rounded-2xl border border-white/5 bg-night-soft">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b border-white/5 text-xs uppercase tracking-wider text-gray-500">
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Sản phẩm</th>
                    <th class="px-4 py-3">Người đánh giá</th>
                    <th class="px-4 py-3">Đánh giá</th>
                    <th class="px-4 py-3">Nội dung</th>
                    <th class="px-4 py-3">Trạng thái</th>
                    <th class="px-4 py-3">Ngày tạo</th>
                    <th class="px-4 py-3 text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse ($reviews as $review)
                    <tr class="transition-colors duration-150 hover:bg-white/[0.03]">
                        <td class="px-4 py-3 text-gray-300">{{ $review->id }}</td>
                        <td class="px-4 py-3">
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-white">{{ $review->product->name ?? '—' }}</p>
                                <p class="mt-1 text-xs text-gray-500">Đơn: {{ $review->order->order_code ?? 'Dữ liệu cũ' }}</p>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-300">{{ $review->user->name ?? 'Khách' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $review->rating)
                                        <svg class="size-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.966a1 1 0 0 0 .95.69h4.176c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 0 0-.364 1.118l1.287 3.966c.3.921-.755 1.688-1.54 1.118L10 15.347l-3.393 2.403c-.784.57-1.838-.197-1.539-1.118l1.287-3.966a1 1 0 0 0-.364-1.118L2.607 9.393c-.783-.57-.38-1.81.588-1.81h4.176a1 1 0 0 0 .95-.69L9.05 2.927z" />
                                        </svg>
                                    @else
                                        <svg class="size-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.966a1 1 0 0 0 .95.69h4.176c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 0 0-.364 1.118l1.287 3.966c.3.921-.755 1.688-1.54 1.118L10 15.347l-3.393 2.403c-.784.57-1.838-.197-1.539-1.118l1.287-3.966a1 1 0 0 0-.364-1.118L2.607 9.393c-.783-.57-.38-1.81.588-1.81h4.176a1 1 0 0 0 .95-.69L9.05 2.927z" />
                                        </svg>
                                    @endif
                                @endfor
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-300">
                            <p>{{ \Illuminate\Support\Str::limit($review->comment, 120) ?: 'Không có nội dung' }}</p>
                            @if (! empty($review->images))
                                <div class="mt-2 flex max-w-[240px] flex-wrap gap-1.5">
                                    @foreach ($review->images as $image)
                                        @php
                                            $reviewImageUrl = str_starts_with($image, 'http://') || str_starts_with($image, 'https://')
                                                ? $image
                                                : asset(
                                                    str_starts_with($image, 'images/') || str_starts_with($image, 'storage/')
                                                        ? $image
                                                        : 'storage/' . ltrim($image, '/')
                                                );
                                        @endphp
                                        <a href="{{ $reviewImageUrl }}" target="_blank" rel="noopener noreferrer"
                                           class="block overflow-hidden rounded-lg border border-white/10 transition hover:border-brand-500/50">
                                            <img src="{{ $reviewImageUrl }}" alt="Ảnh đánh giá" class="size-10 object-cover" loading="lazy">
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if ($review->is_visible)
                                <span
                                    class="inline-flex items-center rounded-full bg-green-500/10 px-3 py-1 text-xs font-semibold text-green-400">Active</span>
                            @else
                                <span
                                    class="inline-flex items-center rounded-full bg-yellow-500/10 px-3 py-1 text-xs font-semibold text-yellow-400">Đã
                                    bị ẩn</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-400">{{ $review->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex items-center gap-1.5">
                                @if (!$review->is_visible)
                                    <form method="POST" action="{{ route('admin.reviews.approve', $review) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="flex size-8 items-center justify-center rounded-lg bg-white/5 text-gray-400 transition-all duration-200 hover:bg-green-500/20 hover:text-green-400"
                                            title="Duyệt">
                                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="1.8"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.reviews.hide', $review) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="flex size-8 items-center justify-center rounded-lg bg-white/5 text-gray-400 transition-all duration-200 hover:bg-yellow-500/20 hover:text-yellow-400"
                                            title="Ẩn">
                                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="1.8"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M13.875 18.825A10.05 10.05 0 0 1 12 19.5c-5.523 0-10-4.477-10-10 0-1.03.146-2.025.418-2.943M3 3l18 18" />
                                            </svg>
                                        </button>
                                    </form>
                                @endif

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-gray-500">Chưa có đánh giá nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">
        {{ $reviews->links() }}
    </div>

@endsection
