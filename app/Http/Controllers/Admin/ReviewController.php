<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /** Danh sách bình luận / đánh giá sản phẩm. */
    public function index(Request $request)
    {
        $query = Review::with(['user', 'product']);

        // Tìm theo nội dung, tên khách hàng hoặc tên sản phẩm
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('comment', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('product', fn ($p) => $p->where('name', 'like', "%{$search}%"));
            });
        }

        // Lọc theo trạng thái hiển thị
        if ($request->filled('status')) {
            $query->where('is_visible', $request->input('status') === 'visible');
        }

        // Lọc theo số sao
        if ($request->filled('rating')) {
            $query->where('rating', (int) $request->input('rating'));
        }

        $reviews = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'   => Review::count(),
            'visible' => Review::where('is_visible', true)->count(),
            'hidden'  => Review::where('is_visible', false)->count(),
            'avg'     => round((float) Review::avg('rating'), 1),
        ];

        return view('admin.reviews.index', [
            'reviews' => $reviews,
            'stats'   => $stats,
            'filters' => $request->only(['search', 'status', 'rating']),
        ]);
    }

    /** Ẩn / hiện bình luận. */
    public function toggle(Review $review)
    {
        $review->update(['is_visible' => ! $review->is_visible]);

        return back()->with(
            'success',
            $review->is_visible ? 'Đã hiển thị bình luận.' : 'Đã ẩn bình luận.'
        );
    }

    /** Xóa bình luận. */
    public function destroy(Review $review)
    {
        $review->delete();

        return back()->with('success', 'Đã xóa bình luận.');
    }
}
