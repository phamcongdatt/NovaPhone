<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductReviewRequest;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\JsonResponse;

class ProductReviewController extends Controller
{
    public function store(StoreProductReviewRequest $request, Product $product): JsonResponse
    {
        $user = $request->user();

        if ($user->reviews()
            ->where('order_id', $request->integer('order_id'))
            ->where('product_id', $product->id)
            ->exists()) {
            return response()->json([
                'message' => 'Bạn đã đánh giá sản phẩm này trong đơn hàng này rồi.',
            ], 409);
        }

        $order = $user->orders()
            ->whereKey($request->integer('order_id'))
            ->where('status', 'delivered')
            ->where('payment_status', 'paid')
            ->whereHas('items', fn ($query) => $query->where('product_id', $product->id))
            ->latest()
            ->first();

        if (! $order) {
            return response()->json([
                'message' => 'Đơn hàng không hợp lệ hoặc chưa được giao và thanh toán thành công.',
            ], 403);
        }

        $imagePaths = collect($request->file('images', []))
            ->map(fn ($image) => $image->store('reviews', 'public'))
            ->values()
            ->all();

        $review = Review::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'order_id' => $order->id,
            'rating' => $request->integer('rating'),
            'comment' => $request->input('comment'),
            'images' => $imagePaths ?: null,
            'is_visible' => true,
        ]);

        return response()->json([
            'message' => 'Đánh giá sản phẩm thành công.',
            'data' => [
                'id' => $review->id,
                'product_id' => $review->product_id,
                'order_id' => $review->order_id,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'images' => $review->images ?? [],
                'is_visible' => $review->is_visible,
                'created_at' => $review->created_at?->toISOString(),
            ],
        ], 201);
    }
}
