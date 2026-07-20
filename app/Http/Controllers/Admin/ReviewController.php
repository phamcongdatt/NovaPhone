<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Display a listing of reviews with search, filter and pagination.
     */
    public function index(Request $request)
    {
        $filters = [
            'q' => $request->query('q'),
            'status' => $request->query('status'),
        ];

        $reviews = Review::with(['product', 'user', 'order:id,order_code'])
            ->when($filters['q'], function ($query, $q) {
                $query->where(function ($q2) use ($q) {
                    $q2->whereHas('product', function ($q3) use ($q) {
                        $q3->where('name', 'like', "%{$q}%");
                    })
                    ->orWhereHas('user', function ($q3) use ($q) {
                        $q3->where('name', 'like', "%{$q}%");
                    })
                    ->orWhere('comment', 'like', "%{$q}%");
                });
            })
            ->when($filters['status'] === 'approved', function ($query) {
                $query->where('is_visible', true);
            })
            ->when($filters['status'] === 'pending', function ($query) {
                $query->where('is_visible', false);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.reviews.index', compact('reviews', 'filters'));
    }

    /**
     * Approve a review (set visible).
     */
    public function approve($id)
    {
        $review = Review::find($id);
        if (! $review) {
            return redirect()->route('admin.reviews.index')->withErrors('Đánh giá không tồn tại.');
        }

        $review->is_visible = true;
        $review->save();

        return redirect()->route('admin.reviews.index')->with('success', 'Đã duyệt đánh giá.');
    }

    /**
     * Hide a review (set pending/not visible).
     */
    public function hide($id)
    {
        $review = Review::find($id);
        if (! $review) {
            return redirect()->route('admin.reviews.index')->withErrors('Đánh giá không tồn tại.');
        }

        $review->is_visible = false;
        $review->save();

        return redirect()->route('admin.reviews.index')->with('success', 'Đã ẩn đánh giá.');
    }

}
