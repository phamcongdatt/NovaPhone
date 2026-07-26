@props([
    'id' => null,
    'name' => '',
    'image' => asset('images/placeholder.svg'),
    'price' => null,
    'oldPrice' => null,
    'discount' => null,
    'rating' => null,
    'sold' => null,
    'soldPercent' => null,
    'badge' => null,
    'isFlashSale' => false,
    'href' => null,
])

@php
    $isWishlisted = $id && in_array($id, $wishlistProductIds ?? []);
    $isCompared = $id && in_array($id, $compareProductIds ?? []);
@endphp

<article {{ $attributes->merge(['class' => 'group rounded-[22px] border border-[#ece8e2] bg-white p-3 shadow-[0_8px_30px_rgba(0,0,0,.03)] transition hover:-translate-y-1 hover:shadow-[0_12px_35px_rgba(0,0,0,.06)]']) }}>
    <div class="flex items-start justify-between gap-2">
        <div class="flex flex-col gap-1.5">
            @if ($isFlashSale)
                <span class="inline-flex w-fit items-center rounded-full bg-black px-2.5 py-1 text-[9px] font-bold text-white">Flash Sale</span>
            @endif
            @if ($discount)
                <span class="inline-flex w-fit items-center rounded-full bg-[#f04c3e] px-2.5 py-1 text-[9px] font-bold text-white">-{{ $discount }}%</span>
            @endif
            @if ($badge)
                <span class="inline-flex w-fit items-center rounded-full bg-[#f2f0ec] px-2.5 py-1 text-[9px] font-semibold text-[#5c5c5c]">{{ $badge }}</span>
            @endif
        </div>
        @if ($id)
            <div class="flex gap-1">
                <button type="button" class="grid size-7 place-items-center rounded-full border border-[#ece8e2] text-[#4d4d4d] transition hover:border-black hover:text-black" aria-label="Yêu thích" aria-pressed="{{ $isWishlisted ? 'true' : 'false' }}" data-wishlist-toggle data-product-id="{{ $id }}" data-wishlist-url="{{ route('wishlist.toggle') }}" data-login-url="{{ route('login') }}">
                    <svg class="size-3.5 {{ $isWishlisted ? 'fill-black' : '' }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.49-2.1-4.5-4.69-4.5-1.94 0-3.6 1.13-4.31 2.73a4.72 4.72 0 0 0-4.31-2.73C5.1 3.75 3 5.76 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/></svg>
                </button>
                <button type="button" class="grid size-7 place-items-center rounded-full border border-[#ece8e2] text-[#4d4d4d] transition hover:border-black hover:text-black" aria-label="So sánh" data-compare-toggle data-product-id="{{ $id }}" data-compare-url="{{ route('compare.add') }}">
                    <svg class="size-3.5 {{ $isCompared ? 'stroke-black' : '' }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3.75H5.5A1.75 1.75 0 0 0 3.75 5.5v13A1.75 1.75 0 0 0 5.5 20.25h2.75m7.5-16.5h2.75A1.75 1.75 0 0 1 20.25 5.5v13a1.75 1.75 0 0 1-1.75 1.75h-2.75M8.25 8.25h7.5m-7.5 7.5h7.5M12 5.25v13.5"/></svg>
                </button>
            </div>
        @endif
    </div>

    @if ($href)
        <a href="{{ $href }}" class="mt-2 block overflow-hidden rounded-[18px] bg-[#faf9f7]">
            <img src="{{ $image }}" alt="{{ $name }}" loading="lazy" fetchpriority="low" decoding="async" class="aspect-square w-full object-contain p-2 transition duration-300 group-hover:scale-[1.03]">
        </a>
    @else
        <div class="mt-2 block overflow-hidden rounded-[18px] bg-[#faf9f7]">
            <img src="{{ $image }}" alt="{{ $name }}" loading="lazy" fetchpriority="low" decoding="async" class="aspect-square w-full object-contain p-2">
        </div>
    @endif

    <h3 class="mt-3 line-clamp-2 min-h-10 text-[13px] font-medium leading-snug text-[#202020]">
        @if ($href)
            <a href="{{ $href }}" class="transition hover:text-black">{{ $name }}</a>
        @else
            {{ $name }}
        @endif
    </h3>

    <div class="mt-2 flex items-baseline gap-2">
        @if (!is_null($price))
            <div class="text-[15px] font-bold text-[#111]">{{ number_format((float) $price, 0, ',', '.') }}đ</div>
        @endif
        @if ($oldPrice)
            <div class="text-[11px] text-[#9d9d9d] line-through">{{ number_format((float) $oldPrice, 0, ',', '.') }}đ</div>
        @endif
    </div>

    @if ($id)
        <div class="mt-3 grid grid-cols-2 gap-2">
            <form method="POST" action="{{ route('cart.store') }}" data-cart-add-form>
                @csrf
                <input type="hidden" name="product_id" value="{{ $id }}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="w-full rounded-full border border-[#e8e4de] px-3 py-2 text-[11px] font-semibold text-[#171717] transition hover:border-black hover:bg-[#faf9f7]">Thêm giỏ hàng</button>
            </form>
            <form method="POST" action="{{ route('cart.buy-now') }}" data-buy-now-form>
                @csrf
                <input type="hidden" name="product_id" value="{{ $id }}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="w-full rounded-full bg-black px-3 py-2 text-[11px] font-semibold text-white transition hover:bg-[#222]">Mua ngay</button>
            </form>
        </div>
    @endif

    @if (!is_null($soldPercent))
        <div class="mt-3">
            <div class="h-1.5 overflow-hidden rounded-full bg-[#f0eeea]">
                <div class="h-full rounded-full bg-black" style="width: {{ max(0, min(100, (int) $soldPercent)) }}%"></div>
            </div>
            <p class="mt-1 text-[10px] text-[#898989]">Đã bán {{ $sold }}</p>
        </div>
    @elseif ($rating)
        <div class="mt-3 flex items-center gap-1.5 text-[11px] text-[#808080]">
            <span class="font-semibold text-[#202020]">{{ $rating }}</span>
            @if ($sold)
                <span>Đã bán {{ $sold }}</span>
            @endif
        </div>
    @endif
</article>
