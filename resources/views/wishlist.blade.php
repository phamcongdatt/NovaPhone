@extends('layouts.app')
@section('title', 'Sản phẩm yêu thích - NovaPhone')

@section('content')
<div class="space-y-3">
    <div class="flex">
        <span class="rounded-full border border-[#e8e4de] bg-white px-3 py-1 text-[11px] font-semibold text-[#444] shadow-sm">Sản phẩm yêu thích</span>
    </div>
    <section class="rounded-[28px] border border-[#ece8e2] bg-white p-5 shadow-[0_10px_35px_rgba(0,0,0,.04)]">
        <h1 class="text-2xl font-bold">Sản phẩm yêu thích ({{ count($wishlists) }})</h1>
        <div class="mt-5 space-y-3">
            @forelse($wishlists as $wishlist)
                <div class="flex items-center gap-4 rounded-[22px] border border-[#ece8e2] p-4 transition hover:shadow-md">
                    <span class="text-red-500 text-lg">♥</span>
                    <img src="{{ $wishlist->product->thumbnail ? (str_starts_with($wishlist->product->thumbnail, 'images/') ? asset($wishlist->product->thumbnail) : asset('storage/' . $wishlist->product->thumbnail)) : asset('images/placeholder.svg') }}" alt="{{ $wishlist->product->name }}" class="size-16 rounded-xl object-cover bg-[#faf9f7]" loading="lazy" decoding="async">
                    <div class="min-w-0 flex-1">
                        <a href="{{ route('products.show', $wishlist->product) }}" class="text-sm font-semibold hover:text-blue-600">
                            {{ $wishlist->product->name }}
                        </a>
                        <div class="mt-1 text-sm font-bold">{{ number_format($wishlist->product->sale_price ?? $wishlist->product->price ?? 0, 0, ',', '.') }}đ</div>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('products.show', $wishlist->product) }}" class="rounded-full border border-[#e8e4de] px-4 py-2 text-sm font-semibold transition hover:bg-black hover:text-white">
                            Xem sản phẩm
                        </a>
                        <button class="text-red-500 hover:text-red-700 transition remove-wishlist-btn" data-product-id="{{ $wishlist->product->id }}" title="Xóa">
                            ✕
                        </button>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <p class="text-[#8b8b8b] mb-4">Chưa có sản phẩm yêu thích nào</p>
                    <a href="{{ route('products.index') }}" class="inline-block rounded-lg bg-black px-6 py-2 text-sm font-semibold text-white transition hover:bg-[#222]">
                        Tiếp tục mua sắm
                    </a>
                </div>
            @endforelse
        </div>
    </section>
</div>

<script>
    document.querySelectorAll('.remove-wishlist-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const productId = this.dataset.productId;
            fetch('{{ route("wishlist.toggle") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ product_id: productId })
            })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'removed') {
                    this.closest('.flex').remove();
                    location.reload();
                }
            });
        });
    });
</script>
@endsection
