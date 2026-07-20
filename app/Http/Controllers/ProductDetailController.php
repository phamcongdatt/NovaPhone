<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductDetailController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function show(Request $request, Product $product): View
    {
        abort_unless($product->is_active, 404);

        $product->increment('view_count');
        $reviewContext = $this->reviewContext($request, $product);

        return view('products.show', [
            'product' => $product->refresh()->loadMissing($this->relations()),
            'cartCount' => $this->cartService->getCount(),
            'detail' => $this->detailPayload($product),
            'reviewStatus' => $reviewContext['status'],
            'reviewOrderId' => $reviewContext['order_id'],
        ]);
    }

    public function apiShow(Product $product): JsonResponse
    {
        abort_unless($product->is_active, 404);

        return response()->json([
            'data' => $this->detailPayload($product->loadMissing($this->relations())),
        ]);
    }

    private function relations(): array
    {
        return [
            'brand',
            'category',
            'images' => fn ($query) => $query->orderByDesc('is_primary')->orderBy('sort_order'),
            'variants.inventory',
            'inventory',
            'reviews' => fn ($query) => $query->where('is_visible', true)->latest()->with('user:id,name'),
        ];
    }

    private function reviewContext(Request $request, Product $product): array
    {
        $user = $request->user();

        if (! $user) {
            return ['status' => null, 'order_id' => null];
        }

        if ($user->reviews()->where('product_id', $product->id)->exists()) {
            return ['status' => 'reviewed', 'order_id' => null];
        }

        $eligibleOrderQuery = $user->orders()
            ->where('status', 'delivered')
            ->where('payment_status', 'paid')
            ->whereHas('items', fn ($query) => $query->where('product_id', $product->id));

        if ($request->integer('order') > 0) {
            $eligibleOrderQuery->whereKey($request->integer('order'));
        }

        $eligibleOrder = $eligibleOrderQuery->latest()->first();

        return $eligibleOrder
            ? ['status' => 'eligible', 'order_id' => $eligibleOrder->id]
            : ['status' => 'purchase_required', 'order_id' => null];
    }

    private function detailPayload(Product $product): array
    {
        $images = $product->images
            ->map(fn ($image) => [
                'url' => str_starts_with($image->image_url, 'images/') ? asset($image->image_url) : asset('storage/' . $image->image_url),
                'is_primary' => $image->is_primary,
                'sort_order' => $image->sort_order,
            ])
            ->values();

        if ($images->isEmpty()) {
            $images = collect([[
                'url' => $product->thumbnail ? (str_starts_with($product->thumbnail, 'images/') ? asset($product->thumbnail) : asset('storage/' . $product->thumbnail)) : asset('images/placeholder.svg'),
                'is_primary' => true,
                'sort_order' => 0,
            ]]);
        }

        $reviews = $product->reviews;
        $averageRating = round((float) $reviews->avg('rating'), 1);

        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'sku' => $product->sku,
            'description' => $product->description,
            'content' => $product->content,
            'brand' => $product->brand?->only(['id', 'name', 'slug']),
            'category' => $product->category?->only(['id', 'name', 'slug']),
            'price' => (float) $product->price,
            'sale_price' => $product->sale_price ? (float) $product->sale_price : null,
            'effective_price' => $product->effective_price,
            'discount_percent' => $this->discountPercent($product),
            'images' => $images,
            'variants' => $product->variants
                ->where('is_active', true)
                ->map(fn ($variant) => [
                    'id' => $variant->id,
                    'name' => $variant->name,
                    'storage' => $variant->storage,
                    'color' => $variant->color,
                    'color_code' => $variant->color_code,
                    'additional_price' => (float) $variant->additional_price,
                    'sku' => $variant->sku,
                    'available_quantity' => $variant->inventory?->available_quantity ?? 0,
                ])
                ->values(),
            'specifications' => $this->specifications($product),
            'rating' => [
                'average' => $averageRating,
                'count' => $reviews->count(),
                'breakdown' => collect(range(5, 1))->mapWithKeys(
                    fn ($star) => [$star => $reviews->where('rating', $star)->count()]
                ),
            ],
            'reviews' => $reviews
                ->map(fn ($review) => [
                    'id' => $review->id,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'images' => collect($review->images ?? [])
                        ->map(fn ($image) => $this->reviewImageUrl($image))
                        ->values(),
                    'user' => $review->user?->only(['id', 'name']),
                    'created_at' => $review->created_at?->toDateString(),
                ])
                ->values(),
            'inventory' => [
                'available_quantity' => $product->inventory?->available_quantity
                    ?? $product->variants->sum(fn ($variant) => $variant->inventory?->available_quantity ?? 0),
            ],
            'sold_count' => $product->sold_count,
            'view_count' => $product->view_count,
        ];
    }

    private function specifications(Product $product): array
    {
        return [
            ['label' => 'Thương hiệu', 'value' => $product->brand?->name ?? 'Đang cập nhật'],
            ['label' => 'Danh mục', 'value' => $product->category?->name ?? 'Đang cập nhật'],
            ['label' => 'Mã sản phẩm', 'value' => $product->sku ?? 'Đang cập nhật'],
            ['label' => 'Bộ nhớ', 'value' => $product->variants->pluck('storage')->filter()->unique()->join(', ') ?: 'Đang cập nhật'],
            ['label' => 'Màu sắc', 'value' => $product->variants->pluck('color')->filter()->unique()->join(', ') ?: 'Đang cập nhật'],
            ['label' => 'Tình trạng', 'value' => $product->is_active ? 'Đang kinh doanh' : 'Ngừng kinh doanh'],
        ];
    }

    private function reviewImageUrl(string $image): string
    {
        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            return $image;
        }

        if (str_starts_with($image, 'images/') || str_starts_with($image, 'storage/')) {
            return asset($image);
        }

        return asset('storage/' . ltrim($image, '/'));
    }

    private function discountPercent(Product $product): ?int
    {
        if (! $product->sale_price || $product->sale_price >= $product->price) {
            return null;
        }

        return (int) round((($product->price - $product->sale_price) / $product->price) * 100);
    }
}
