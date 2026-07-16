@extends('layouts.app')

@section('title', 'So sánh sản phẩm | NovaPhone')

@php
    $money = fn ($value) => number_format((float) $value, 0, ',', '.').'đ';

    $firstPayload = $payload[array_key_first($payload)] ?? [];
    $performanceRows = collect($firstPayload['performance_specs'] ?? [])
        ->map(function (array $specification) use ($payload) {
            $values = collect($payload)->mapWithKeys(function (array $item, int $productId) use ($specification) {
                $spec = collect($item['performance_specs'] ?? [])
                    ->firstWhere('key', $specification['key']);

                return [$productId => $spec['value'] ?? null];
            });

            $filledValues = $values->filter(fn ($value) => filled($value));
            $canHighlight = ($specification['higher_is_better'] ?? false)
                && $filledValues->count() >= 2
                && $filledValues->every(fn ($value) => is_numeric($value));

            return [
                ...$specification,
                'values' => $values,
                'best_value' => $canHighlight ? $filledValues->map(fn ($value) => (float) $value)->max() : null,
            ];
        })
        ->values();
@endphp

@section('content')
<div class="min-h-screen bg-night pb-14 text-gray-100">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6">
        <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-brand-400">NovaPhone</p>
                <h1 class="mt-1 text-3xl font-black text-white sm:text-4xl">So sánh sản phẩm</h1>
                <p class="mt-2 text-sm text-gray-400">Đối chiếu tối đa 4 điện thoại để chọn sản phẩm phù hợp nhất.</p>
            </div>

            @if ($products->isNotEmpty())
                <button type="button" data-compare-clear
                        class="rounded-xl border border-red-400/30 px-4 py-2.5 text-sm font-bold text-red-300 transition hover:bg-red-500/10">
                    Xóa tất cả
                </button>
            @endif
        </div>

        @if ($products->isEmpty())
            <section class="rounded-2xl border border-white/5 bg-night-soft px-6 py-16 text-center shadow-xl shadow-black/20">
                <div class="mx-auto flex size-20 items-center justify-center rounded-full bg-brand-600/10 text-brand-300">
                    <svg class="size-10" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3.75H5.5A1.75 1.75 0 0 0 3.75 5.5v13A1.75 1.75 0 0 0 5.5 20.25h2.75m7.5-16.5h2.75A1.75 1.75 0 0 1 20.25 5.5v13a1.75 1.75 0 0 1-1.75 1.75h-2.75M8.25 8.25h7.5m-7.5 7.5h7.5M12 5.25v13.5"/></svg>
                </div>
                <h2 class="mt-5 text-xl font-extrabold text-white">Chưa có sản phẩm để so sánh</h2>
                <p class="mx-auto mt-2 max-w-md text-sm leading-relaxed text-gray-400">Thêm điện thoại từ trang danh sách hoặc trang chi tiết sản phẩm, sau đó quay lại đây để xem đối chiếu.</p>
                <a href="{{ route('products.index') }}" class="mt-7 inline-flex rounded-xl bg-brand-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-brand-500">Khám phá sản phẩm</a>
            </section>
        @else
            <section class="overflow-hidden rounded-2xl border border-white/5 bg-night-soft shadow-xl shadow-black/20">
                <div class="overflow-x-auto">
                    <table class="min-w-[760px] w-full border-collapse" data-compare-table>
                        <thead>
                            <tr class="border-b border-white/10">
                                <th class="sticky left-0 z-20 w-40 min-w-40 bg-night-soft px-5 py-5 text-left text-sm font-bold text-gray-400 sm:w-52"></th>
                                @foreach ($products as $product)
                                    @php($item = $payload[$product->id])
                                    <th class="min-w-56 px-4 py-5 align-top text-center" data-compare-column="{{ $product->id }}">
                                        <div class="relative mx-auto max-w-48">
                                            <button type="button" data-compare-remove="{{ $product->id }}" aria-label="Xóa {{ $item['name'] }} khỏi so sánh"
                                                    class="absolute -right-2 -top-2 z-10 flex size-7 items-center justify-center rounded-full bg-red-500 text-white shadow-lg transition hover:bg-red-400">
                                                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m6 6 12 12M18 6 6 18"/></svg>
                                            </button>
                                            <a href="{{ route('products.show', $item['slug']) }}" class="block overflow-hidden rounded-xl bg-white/5 p-3">
                                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="mx-auto aspect-square w-full object-contain">
                                            </a>
                                            <a href="{{ route('products.show', $item['slug']) }}" class="mt-3 line-clamp-2 block text-sm font-bold leading-snug text-white transition hover:text-brand-300">{{ $item['name'] }}</a>
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-white/10">
                                <th class="sticky left-0 z-10 bg-night-soft px-5 py-4 text-left text-sm font-semibold text-gray-400">Giá bán</th>
                                @foreach ($products as $product)
                                    @php($item = $payload[$product->id])
                                    <td class="px-4 py-4 text-center" data-compare-column="{{ $product->id }}">
                                        <p class="text-lg font-black text-brand-400">{{ $money($item['effective_price']) }}</p>
                                        @if ($item['sale_price'])
                                            <p class="mt-1 text-xs text-gray-500 line-through">{{ $money($item['price']) }}</p>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                            <tr class="border-b border-white/10">
                                <th class="sticky left-0 z-10 bg-night-soft px-5 py-4 text-left text-sm font-semibold text-gray-400">Đánh giá</th>
                                @foreach ($products as $product)
                                    @php($item = $payload[$product->id])
                                    <td class="px-4 py-4 text-center text-sm" data-compare-column="{{ $product->id }}">
                                        <span class="font-bold text-amber-400">★ {{ $item['rating_average'] ?? 'Chưa có' }}</span>
                                        @if ($item['rating_count'])
                                            <span class="text-gray-500">({{ $item['rating_count'] }})</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                            <tr class="border-b border-white/10">
                                <th class="sticky left-0 z-10 bg-night-soft px-5 py-4 text-left text-sm font-semibold text-gray-400">Tình trạng kho</th>
                                @foreach ($products as $product)
                                    @php($item = $payload[$product->id])
                                    <td class="px-4 py-4 text-center text-sm font-semibold {{ $item['available_quantity'] > 0 ? 'text-emerald-400' : 'text-red-400' }}" data-compare-column="{{ $product->id }}">
                                        {{ $item['available_quantity'] > 0 ? 'Còn hàng ('.$item['available_quantity'].')' : 'Hết hàng' }}
                                    </td>
                                @endforeach
                            </tr>
                            @foreach ($performanceRows as $row)
                                <tr class="border-b border-white/10">
                                    <th class="sticky left-0 z-10 bg-night-soft px-5 py-4 text-left text-sm font-semibold text-gray-400">
                                        {{ $row['label'] }}
                                        @if (($row['higher_is_better'] ?? false) && isset($row['best_value']))
                                            <svg class="inline-block ml-1 size-3 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 18.75 7.5-7.5 7.5 7.5"/></svg>
                                        @endif
                                    </th>
                                    @foreach ($products as $product)
                                        @php($item = $payload[$product->id])
                                        @php($value = $row['values'][$product->id] ?? null)
                                        @php($isBest = isset($row['best_value']) && is_numeric($value) && (float) $value === $row['best_value'])
                                        <td class="px-4 py-4 text-center text-sm" data-compare-column="{{ $product->id }}">
                                            @if (! filled($value))
                                                <span class="text-gray-600 italic">Chưa có dữ liệu</span>
                                            @else
                                                <span class="{{ $isBest ? 'font-black text-emerald-400' : 'text-gray-200' }}">
                                                    @if (is_numeric($value) && ($row['higher_is_better'] ?? false))
                                                        {{ number_format((float) $value, 0, ',', '.') }}{{ $row['unit'] ?? '' }}
                                                    @else
                                                        {{ $value }}
                                                    @endif
                                                </span>
                                                @if ($isBest)
                                                    <span class="ml-1 inline-block rounded bg-emerald-400/10 px-1.5 py-0.5 text-[10px] font-bold text-emerald-400">Tốt nhất</span>
                                                @endif
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
@endforeach
                            <tr>
                                <th class="sticky left-0 z-10 bg-night-soft px-5 py-4 text-left text-sm font-semibold text-gray-400">Thao tác</th>
                                @foreach ($products as $product)
                                    @php($item = $payload[$product->id])
                                    <td class="px-4 py-4 text-center" data-compare-column="{{ $product->id }}">
                                        <a href="{{ route('products.show', $item['slug']) }}" class="inline-flex rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-brand-500">Xem sản phẩm</a>
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            @if ($products->count() < 2)
                <div class="mt-5 rounded-xl border border-amber-400/20 bg-amber-400/10 px-4 py-3 text-sm text-amber-200">
                    Hãy thêm ít nhất một sản phẩm nữa để có thể đối chiếu các thông số.
                </div>
            @endif
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        const updateBadge = (count) => {
            const badge = document.getElementById('compare-count-badge');
            if (!badge) return;

            badge.textContent = count;
            badge.classList.toggle('hidden', count === 0);
            badge.classList.toggle('flex', count > 0);
        };

        const request = async (url) => {
            const response = await fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
            });
            const data = await response.json();
            if (!response.ok || !data.success) throw new Error(data.message || 'Không thể cập nhật danh sách so sánh.');
            return data;
        };

        document.addEventListener('click', async (event) => {
            const remove = event.target.closest('[data-compare-remove]');
            const clear = event.target.closest('[data-compare-clear]');
            if (!remove && !clear) return;

            const target = remove || clear;
            target.disabled = true;

            try {
                const url = remove
                    ? `{{ url('/compare') }}/${remove.dataset.compareRemove}`
                    : '{{ route('compare.clear') }}';
                const data = await request(url);
                updateBadge(data.count);
                window.location.reload();
            } catch (error) {
                target.disabled = false;
                window.alert(error.message || 'Không thể cập nhật danh sách so sánh.');
            }
        });
    });
</script>
@endpush