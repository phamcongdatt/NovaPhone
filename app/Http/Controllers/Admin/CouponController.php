<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CouponController extends Controller
{
    /**
     * Display a listing of the coupons.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $coupons = Coupon::when($search, function ($query, $search) {
            return $query->where('code', 'like', "%{$search}%")
                         ->orWhere('description', 'like', "%{$search}%");
        })->latest()->paginate(10)->withQueryString();

        return view('admin.coupons.index', compact('coupons', 'search'));
    }

    /**
     * Show the form for creating a new coupon.
     */
    public function create()
    {
        return view('admin.coupons.create');
    }

    /**
     * Store a newly created coupon in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'description' => 'nullable|string|max:255',
            'type' => 'required|in:fixed,percent',
            'value' => 'required|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'per_user_limit' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'boolean',
        ]);

        $validated['code'] = Str::upper($validated['code']);
        $validated['is_active'] = $request->has('is_active');
        $validated['min_order_amount'] = $validated['min_order_amount'] ?? 0;

        Coupon::create($validated);

        return redirect()->route('admin.coupons.index')->with('success', 'Tạo mã giảm giá thành công.');
    }

    /**
     * Show the form for editing the specified coupon.
     */
    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    /**
     * Update the specified coupon in storage.
     */
    public function update(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'description' => 'nullable|string|max:255',
            'type' => 'required|in:fixed,percent',
            'value' => 'required|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'per_user_limit' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        $validated['code'] = Str::upper($validated['code']);
        $validated['is_active'] = $request->has('is_active');
        $validated['min_order_amount'] = $validated['min_order_amount'] ?? 0;

        $coupon->update($validated);

        return redirect()->route('admin.coupons.index')->with('success', 'Cập nhật mã giảm giá thành công.');
    }

    /**
     * Remove the specified coupon from storage.
     */
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()->route('admin.coupons.index')->with('success', 'Xóa mã giảm giá thành công.');
    }
}
