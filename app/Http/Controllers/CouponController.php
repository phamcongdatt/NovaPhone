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

    /**
     * Áp dụng mã giảm giá (AJAX).
     */
    public function apply(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $coupon = Coupon::where('code', $request->code)
            ->where('is_active', true)
            ->first();

        if (!$coupon) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá không tồn tại hoặc đã hết hiệu lực.'], 400);
        }

        // Kiểm tra ngày hiệu lực
        $now = now();
        if ($coupon->starts_at && $coupon->starts_at > $now) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá chưa có hiệu lực.'], 400);
        }
        if ($coupon->expires_at && $coupon->expires_at < $now) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá đã hết hạn.'], 400);
        }

        // Kiểm tra giới hạn sử dụng
        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá đã hết lượt sử dụng.'], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Áp dụng mã thành công!',
            'discount' => $coupon->value,
            'discount_type' => $coupon->type,
        ]);
    }
}
