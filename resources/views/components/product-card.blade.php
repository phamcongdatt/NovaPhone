@props([
    'name',
    'image',
    'price',
    'oldPrice' => null,
    'discount' => null,
    'rating' => null,
    'sold' => null,
    'soldPercent' => null,
    'badge' => null,
    'href' => '#',
])

<article {{ $attributes->merge(['class' => 'group relative flex flex-col rounded-2xl border border-white/5 bg-night-card p-3 transition-all duration-200 ease-in-out hover:-translate-y-1.5 hover:border-brand-500/40 hover:shadow-xl hover:shadow-black/50']) }}>
    @if ($discount)
        <span class="absolute left-3 top-3 z-10 rounded-lg bg-red-600 px-2 py-1 text-[11px] font-bold text-white shadow-sm">
            -{{ $discount }}%
        </span>
    @endif

    @if ($badge)
        <span class="absolute right-3 top-3 z-10 rounded-lg bg-white/10 px-2 py-1 text-[10px] font-semibold text-white backdrop-blur">
            {{ $badge }}
        </span>
    @endif

    <a href="{{ $href }}" class="skeleton mb-3 block overflow-hidden rounded-xl bg-white/5">
        <img
            src="{{ $image }}"
            alt="{{ $name }}"
            loading="lazy"
            data-skeleton
            class="aspect-square w-full object-cover transition-transform duration-300 ease-in-out group-hover:scale-105"
        >
    </a>

    <h3 class="mb-1.5 min-h-10 text-sm font-semibold leading-snug text-gray-100">
        <a href="{{ $href }}" class="line-clamp-2 transition-colors duration-200 group-hover:text-brand-400">
            {{ $name }}
        </a>
    </h3>

    <div class="mb-2 flex items-baseline gap-2">
        <p class="text-base font-extrabold text-brand-400">{{ number_format($price, 0, ',', '.') }}đ</p>
        @if ($oldPrice)
            <p class="text-xs text-gray-500 line-through">{{ number_format($oldPrice, 0, ',', '.') }}đ</p>
        @endif
    </div>

    @if (! is_null($soldPercent))
        @php
            $soldWidth = max(0, min(100, (int) $soldPercent));
        @endphp

        <div class="mt-auto">
            <div class="h-1.5 overflow-hidden rounded-full bg-white/10">
                <div
                    class="h-full rounded-full bg-gradient-to-r from-red-500 to-amber-400 [width:var(--sold-width)]"
                    style="--sold-width: {{ $soldWidth }}%;"
                ></div>
            </div>
            <p class="mt-1.5 text-[11px] text-gray-400">Đã bán {{ $sold }}</p>
        </div>
    @else
        <div class="mt-auto flex items-center gap-1 text-xs text-gray-400">
            @if ($rating)
                <svg class="size-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.05 2.93c.3-.92 1.6-.92 1.9 0l1.07 3.29a1 1 0 0 0 .95.69h3.46c.97 0 1.37 1.24.59 1.81l-2.8 2.03a1 1 0 0 0-.36 1.12l1.07 3.29c.3.92-.76 1.69-1.54 1.12l-2.8-2.03a1 1 0 0 0-1.18 0l-2.8 2.03c-.78.57-1.84-.2-1.54-1.12l1.07-3.29a1 1 0 0 0-.36-1.12L3 8.72c-.78-.57-.38-1.8.59-1.8h3.46a1 1 0 0 0 .95-.7l1.07-3.29Z"/>
                </svg>
                <span class="font-semibold text-gray-200">{{ $rating }}</span>
            @endif

            @if ($sold)
                <span class="text-gray-600">•</span>
                <span>Đã bán {{ $sold }}</span>
            @endif
        </div>
    @endif
</article>
