@extends('layouts.app')

@section('title', ($product->name ?? 'Sản phẩm') . ' - NovaPhone')

@php
    $gallery = collect($detail['images'] ?? [])->pluck('url')->filter()->unique()->values();
    $activePrice = $detail['effective_price'] ?? $product->sale_price ?? $product->price;
    $flashSaleDiscount = $detail['discount_percent'] ?? null;
@endphp

@section('content')
<div class="space-y-4">
    <div class="flex items-center gap-2 text-sm text-[#8b8b8b]">
        <a href="{{ route('home') }}" class="hover:text-black">Trang chủ</a>
        <span>/</span>
        <a href="{{ route('products.index') }}" class="hover:text-black">Sản phẩm</a>
        <span>/</span>
        <span class="text-black">{{ $product->name }}</span>
    </div>

    <section class="rounded-[28px] border border-[#ece8e2] bg-white p-4 shadow-[0_10px_35px_rgba(0,0,0,.04)]">
        <div class="grid gap-6 lg:grid-cols-[92px_1fr_1.05fr]">
            <div
            data-product-thumbnails
            class="flex gap-2 lg:flex-col">
                @foreach ($gallery->take(4) as $img)
                    <button type="button" class="thumbnail-btn overflow-hidden rounded-[16px] border border-[#ece8e2] bg-[#fbfaf8] p-2 transition hover:border-black" data-image="{{ $img }}">
                        <img src="{{ $img }}" class="size-20 object-contain" alt="thumbnail" loading="lazy" decoding="async">
                    </button>
                @endforeach
            </div>

            <div class="flex items-center justify-center rounded-[24px] bg-[#fbfaf8] p-4">
                <img id="main-image" src="{{ $gallery->first() ?? asset('images/placeholder.svg') }}" alt="{{ $product->name }}" fetchpriority="high" decoding="async" class="max-h-[520px] w-full object-contain">
            </div>

            <div class="space-y-5">
                <div>
                    <p class="text-sm text-[#9a9a9a]">{{ $product->brand->name ?? 'Điện thoại' }}</p>
                    <h1 class="mt-2 text-2xl font-bold text-[#171717] sm:text-3xl">{{ $product->name }}</h1>
                    @if ($product->reviews->isNotEmpty())
                        <div class="mt-3 flex items-center gap-2">
                            @php
                                $avgRating = $product->reviews->avg('rating');
                                $reviewCount = $product->reviews->count();
                            @endphp
                            <div class="flex gap-0.5">
                                @for ($i = 0; $i < 5; $i++)
                                    <span class="text-lg {{ $i < floor($avgRating) ? 'text-[#f59e0b]' : 'text-[#ddd]' }}">★</span>
                                @endfor
                            </div>
                            <span class="text-sm font-semibold text-[#111]">{{ number_format($avgRating, 1) }}</span>
                            <span class="text-sm text-[#8b8b8b]">({{ $reviewCount }} đánh giá)</span>
                        </div>
                    @endif
                </div>

                <div class="space-y-2">
                    <div class="flex items-baseline gap-3">
                        <div class="text-3xl font-extrabold text-[#111]">{{ number_format($activePrice, 0, ',', '.') }}đ</div>
                        @if ($flashSaleDiscount)
                            <div class="rounded-full bg-[#f04c3e] px-3 py-1 text-sm font-bold text-white">-{{ $flashSaleDiscount }}%</div>
                        @endif
                    </div>
                    @if ($flashSaleDiscount)
                        <p class="text-xs text-[#f59e0b]">Flash Sale - Ưu đãi giới hạn</p>
                    @endif
                </div>

                @if ($product->description)
                    <p class="text-sm leading-6 text-[#6f6f6f]">{{ \Illuminate\Support\Str::limit($product->description, 180) }}</p>
                @endif

                @php
                    $variants = collect($detail['variants'] ?? []);
                    $colors = $variants->pluck('color')->unique()->filter()->values();
                    $storages = $variants->pluck('storage')->unique()->filter()->values();
                @endphp

                @if ($colors->isNotEmpty())
                <div>
                    <label class="text-sm font-semibold text-[#171717]">Màu sắc</label>
                    <div class="mt-3 grid grid-cols-3 gap-2 md:grid-cols-4">
                        @foreach ($colors as $color)
                            <button type="button" class="color-btn rounded-[12px] border-2 border-[#ece8e2] bg-white px-3 py-2 text-xs font-medium transition hover:border-black" data-color="{{ $color }}">
                                {{ $color }}
                            </button>
                        @endforeach
                    </div>
                </div>
                @endif

                @if ($storages->isNotEmpty())
                <div>
                    <label class="text-sm font-semibold text-[#171717]">Dung lượng</label>
                    <div class="mt-3 grid grid-cols-3 gap-2 md:grid-cols-4">
                        @foreach ($storages as $storage)
                            <button type="button" class="storage-btn rounded-[12px] border-2 border-[#ece8e2] bg-white px-3 py-2 text-xs font-medium transition hover:border-black" data-storage="{{ $storage }}">
                                {{ $storage }}
                            </button>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-semibold text-[#171717]">Số lượng</label>
                        <div class="flex items-center gap-2 rounded-[12px] border border-[#ece8e2] w-fit">
                            <button type="button" class="qty-minus px-3 py-2 text-[#8b8b8b] hover:text-black">-</button>
                            <input type="number" id="quantity" value="1" min="1" max="100" class="w-12 border-0 bg-transparent text-center text-sm font-semibold outline-none">
                            <button type="button" class="qty-plus px-3 py-2 text-[#8b8b8b] hover:text-black">+</button>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <form method="POST" action="{{ route('cart.store') }}" id="add-to-cart-form" data-cart-add-form>
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="variant_id" id="add-to-cart-variant" value="">
                            <input type="hidden" name="quantity" id="add-to-cart-qty" value="1">
                            <button type="submit" class="w-full rounded-full bg-black px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#222]">Thêm vào giỏ hàng</button>
                        </form>
                        <form method="POST" action="{{ route('cart.buy-now') }}" id="buy-now-form" data-buy-now-form>
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="variant_id" id="buy-now-variant" value="">
                            <input type="hidden" name="quantity" id="buy-now-qty" value="1">
                            <button type="submit" class="w-full rounded-full border-2 border-black bg-white px-5 py-3 text-sm font-semibold text-black transition hover:bg-black hover:text-white">Mua ngay</button>
                        </form>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 pt-2">
                    <div class="rounded-[16px] border border-[#ece8e2] bg-[#fbfaf8] p-3 text-center text-xs">
                        <div class="font-semibold text-[#171717]">Miễn phí ship</div>
                        <p class="mt-1 text-[#8b8b8b]">Cho đơn đủ điều kiện</p>
                    </div>
                    <div class="rounded-[16px] border border-[#ece8e2] bg-[#fbfaf8] p-3 text-center text-xs">
                        <div class="font-semibold text-[#171717]">Bảo hành</div>
                        <p class="mt-1 text-[#8b8b8b]">12-24 tháng</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if (!empty($detail['specifications']))
        <section class="rounded-[28px] border border-[#ece8e2] bg-white p-5 shadow-[0_10px_35px_rgba(0,0,0,.04)]">
            <h2 class="text-lg font-bold text-[#171717]">Thông số kỹ thuật</h2>
            <div class="mt-4 grid gap-3 md:grid-cols-2 lg:grid-cols-3">
                @php
                    $specs = $detail['specifications'] ?? [];
                @endphp
                @forelse ($specs as $spec)
                    <div class="rounded-[16px] border border-[#ece8e2] bg-[#fbfaf8] p-4">
                        <p class="text-xs text-[#8b8b8b]">{{ $spec['label'] }}</p>
                        <p class="mt-1 font-semibold text-[#171717]">{{ $spec['value'] }}</p>
                    </div>
                @empty
                    <p class="col-span-full text-sm text-[#8b8b8b]">Không có thông số kỹ thuật</p>
                @endforelse
            </div>
        </section>
    @endif

    @if ($product->description)
        <section class="rounded-[28px] border border-[#ece8e2] bg-white p-5 shadow-[0_10px_35px_rgba(0,0,0,.04)]">
            <h2 class="text-lg font-bold text-[#171717]">Mô tả chi tiết</h2>
            <div class="mt-4 space-y-3 text-sm leading-6 text-[#6f6f6f]">
                {!! nl2br(e($product->description)) !!}
            </div>
        </section>
    @endif

    <section class="rounded-[28px] border border-[#ece8e2] bg-white p-5 shadow-[0_10px_35px_rgba(0,0,0,.04)]">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-[#171717]">Đánh giá từ khách hàng</h2>
        </div>

        @auth
            @if ($reviewStatus === 'eligible')
                <form method="POST" action="{{ route('products.review.store', $product->id) }}" enctype="multipart/form-data" data-review-form class="mt-5 space-y-3 rounded-[18px] border border-[#ece8e2] bg-[#fbfaf8] p-4">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $reviewOrderId }}">
                    <div class="grid gap-3 sm:grid-cols-2">
                        <label class="text-sm font-semibold text-[#171717]">
                            Số sao
                            <select name="rating" required class="mt-2 w-full rounded-[12px] border border-[#e8e4de] bg-white px-3 py-2.5 text-sm outline-none focus:border-black">
                                @for ($rating = 5; $rating >= 1; $rating--)
                                    <option value="{{ $rating }}">{{ $rating }} sao</option>
                                @endfor
                            </select>
                        </label>
                        <label class="text-sm font-semibold text-[#171717]">
                            Ảnh đính kèm
                            <input type="file" name="images[]" accept="image/jpeg,image/png,image/webp" multiple class="mt-2 block w-full rounded-[12px] border border-[#e8e4de] bg-white px-3 py-2 text-xs">
                        </label>
                    </div>
                    <label class="block text-sm font-semibold text-[#171717]">
                        Nội dung đánh giá
                        <textarea name="comment" rows="3" maxlength="5000" class="mt-2 w-full rounded-[12px] border border-[#e8e4de] bg-white px-3 py-2.5 text-sm outline-none focus:border-black" placeholder="Chia sẻ trải nghiệm của bạn"></textarea>
                    </label>
                    <button type="submit" class="rounded-full bg-black px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#222]">Gửi đánh giá</button>
                </form>
            @elseif ($reviewStatus === 'reviewed')
                <p class="mt-4 text-sm text-[#777]">Bạn đã đánh giá sản phẩm này.</p>
            @else
                <p class="mt-4 text-sm text-[#777]">Bạn có thể đánh giá sau khi đơn hàng đã giao và thanh toán thành công.</p>
            @endif
        @endauth

        @if ($product->reviews->isNotEmpty())
            <div class="mt-4 space-y-4">
                @foreach ($product->reviews->take(5) as $review)
                    <div class="border-b border-[#ece8e2] pb-4 last:border-0">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="font-semibold text-[#171717]">{{ $review->user->name ?? 'Khách hàng' }}</p>
                                <div class="mt-1 flex gap-0.5">
                                    @for ($i = 0; $i < 5; $i++)
                                        <span class="text-sm {{ $i < $review->rating ? 'text-[#f59e0b]' : 'text-[#ddd]' }}">★</span>
                                    @endfor
                                </div>
                            </div>
                            <p class="text-xs text-[#8b8b8b]">{{ $review->created_at->format('d/m/Y') }}</p>
                        </div>
                        <p class="mt-2 text-sm text-[#6f6f6f]">{{ $review->comment }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <p class="mt-4 text-center text-sm text-[#8b8b8b]">Chưa có đánh giá nào. Hãy là người đầu tiên đánh giá!</p>
        @endif
    </section>

    @if ($relatedProducts->isNotEmpty())
        <section class="rounded-[28px] border border-[#ece8e2] bg-white p-5 shadow-[0_10px_35px_rgba(0,0,0,.04)]">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-bold text-[#171717]">Sản phẩm liên quan</h2>
                <a href="{{ route('products.index') }}" class="text-xs font-semibold text-[#111] hover:text-[#666]">Xem tất cả</a>
            </div>
            <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
                @foreach ($relatedProducts as $relatedProduct)
                    <x-product-card
                        :id="$relatedProduct->id"
                        :name="$relatedProduct->name"
                        :image="$relatedProduct->thumbnail ? (str_starts_with($relatedProduct->thumbnail, 'http') ? $relatedProduct->thumbnail : (str_starts_with($relatedProduct->thumbnail, 'images/') || str_starts_with($relatedProduct->thumbnail, 'storage/') ? asset($relatedProduct->thumbnail) : asset('storage/' . $relatedProduct->thumbnail))) : asset('images/placeholder.svg')"
                        :price="$relatedProduct->effective_price"
                        :old-price="$relatedProduct->sale_price && $relatedProduct->sale_price < $relatedProduct->price ? $relatedProduct->price : null"
                        :href="route('products.show', $relatedProduct)"
                    />
                @endforeach
            </div>
        </section>
    @endif
</div>

<script>
    document.querySelector('.qty-minus').addEventListener('click', function() {
        const qty = document.getElementById('quantity');
        if (qty.value > 1) qty.value = parseInt(qty.value) - 1;
        document.getElementById('add-to-cart-qty').value = qty.value;
        document.getElementById('buy-now-qty').value = qty.value;
    });

    document.querySelector('.qty-plus').addEventListener('click', function() {
        const qty = document.getElementById('quantity');
        qty.value = Math.min(100, parseInt(qty.value || '1', 10) + 1);
        document.getElementById('add-to-cart-qty').value = qty.value;
        document.getElementById('buy-now-qty').value = qty.value;
    });

    document.getElementById('quantity').addEventListener('input', function () {
        this.value = Math.max(1, Math.min(100, parseInt(this.value || '1', 10)));
        document.getElementById('add-to-cart-qty').value = this.value;
        document.getElementById('buy-now-qty').value = this.value;
    });

    const variants = @json($detail['variants'] ?? []);
    const defaultGallery = @json($detail['images'] ?? []);
    const placeholderImage = @json(asset('images/placeholder.svg'));
    const mainImage = document.getElementById('main-image');
    const thumbnailContainer = document.querySelector('[data-product-thumbnails]');
    let selectedColor = null;
    let selectedStorage = null;

    function bindThumbnailEvents() {
        document.querySelectorAll('.thumbnail-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                document
                    .querySelectorAll('.thumbnail-btn')
                    .forEach(item => item.classList.remove('border-black'));

                this.classList.add('border-black');
                mainImage.src = this.dataset.image;
            });
        });
    }

    function renderGallery(images) {
        const usableImages = (images || [])
            .filter(image => image?.url)
            .slice(0, 4);

        if (!thumbnailContainer) {
            return;
        }

        thumbnailContainer.replaceChildren();

        usableImages.forEach((item, index) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = `thumbnail-btn overflow-hidden rounded-[16px] border-2 ${index === 0 ? 'border-black' : 'border-[#ece8e2]'} bg-[#fbfaf8] p-2 transition hover:border-black`;
            button.dataset.image = item.url;

            const image = document.createElement('img');
            image.src = item.url;
            image.alt = 'Ảnh sản phẩm';
            image.loading = 'lazy';
            image.decoding = 'async';
            image.className = 'size-20 object-contain';

            button.appendChild(image);
            thumbnailContainer.appendChild(button);
        });

        if (usableImages[0]) {
            mainImage.src = usableImages[0].url;
        } else {
            mainImage.src = placeholderImage;
        }

        bindThumbnailEvents();
    }

    function renderVariantImage(variant) {
        if (variant?.image_url) {
            renderGallery([{ url: variant.image_url }]);
            return;
        }

        renderGallery(defaultGallery);
    }

    function syncVariant() {
        const variant = variants.find(item =>
            (!selectedColor || item.color === selectedColor) &&
            (!selectedStorage || item.storage === selectedStorage)
        );

        const variantId = variant?.id || '';
        document.getElementById('add-to-cart-variant').value = variantId;
        document.getElementById('buy-now-variant').value = variantId;

        if (selectedColor || selectedStorage) {
            renderVariantImage(variant);
        }
    }

    document.querySelectorAll('.color-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.color-btn').forEach(item => item.classList.remove('border-black', 'bg-black', 'text-white'));
            this.classList.add('border-black', 'bg-black', 'text-white');
            selectedColor = this.dataset.color;
            syncVariant();
        });
    });

    document.querySelectorAll('.storage-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.storage-btn').forEach(item => item.classList.remove('border-black', 'bg-black', 'text-white'));
            this.classList.add('border-black', 'bg-black', 'text-white');
            selectedStorage = this.dataset.storage;
            syncVariant();
        });
    });

    renderGallery(defaultGallery);
</script>
@endsection
