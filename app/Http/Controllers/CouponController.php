<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CouponController extends Controller
{
    /**
     * Hiển thị Kho Voucher.
     */
    public function index()
    {
        $coupons = Coupon::where('is_active', true)
            ->where(function ($query) {
                $now = now();
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($query) {
                $now = now();
                $query->whereNull('expires_at')->orWhere('expires_at', '>=', $now);
            })
            // Tuỳ chọn: Lọc những mã chưa hết số lượng
            ->whereRaw('(usage_limit IS NULL OR used_count < usage_limit)')
            ->orderBy('id', 'desc')
            ->paginate(12);

        // Lấy danh sách ID các mã đã lưu của user hiện tại
        $savedCouponIds = [];
        if (Auth::check()) {
            $savedCouponIds = Auth::user()->savedCoupons()->pluck('coupons.id')->toArray();
        }

        return view('coupons.index', compact('coupons', 'savedCouponIds'));
    }

    /**
     * Lưu mã giảm giá vào ví (AJAX).
     */
    public function save(Request $request, Coupon $coupon)
    {
        if (! Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập để lưu mã.'], 401);
        }

        if (! $coupon->is_active) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn.'], 400);
        }

        $user = Auth::user();
        
        // Kiểm tra xem đã lưu chưa
        if ($user->savedCoupons()->where('coupon_id', $coupon->id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Bạn đã lưu mã này rồi.']);
        }

        $user->savedCoupons()->attach($coupon->id);

        return response()->json(['success' => true, 'message' => 'Đã lưu mã giảm giá thành công!']);
    }
}
