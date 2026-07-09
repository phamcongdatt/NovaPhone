<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Hiển thị danh sách sản phẩm yêu thích
     */
    public function index()
    {
        $wishlists = Auth::user()->wishlists()->with('product')->latest()->get();
        return view('wishlist', compact('wishlists'));
    }

    /**
     * Thêm/xóa sản phẩm khỏi danh sách yêu thích (Ajax)
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $user = Auth::user();
        $productId = $request->product_id;

        $wishlist = Wishlist::where('user_id', $user->id)
                            ->where('product_id', $productId)
                            ->first();

        if ($wishlist) {
            // Đã tồn tại -> Xóa
            $wishlist->delete();
            $status = 'removed';
        } else {
            // Chưa tồn tại -> Thêm
            Wishlist::create([
                'user_id' => $user->id,
                'product_id' => $productId,
            ]);
            $status = 'added';
        }

        // Lấy tổng số lượng mới nhất
        $count = $user->wishlists()->count();

        return response()->json([
            'status' => $status,
            'count' => $count,
            'message' => $status === 'added' ? 'Đã thêm vào danh sách yêu thích' : 'Đã xóa khỏi danh sách yêu thích',
        ]);
    }
}
