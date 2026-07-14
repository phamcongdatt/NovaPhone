<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
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
        $categories = Category::all();
        $products = Product::select('id', 'name')->get();
        $users = User::select('id', 'name', 'email')->get();
        return view('admin.coupons.create', compact('categories', 'products', 'users'));
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
            'is_apply_sale' => 'boolean',
            'is_apply_flash_sale' => 'boolean',
            'is_stackable' => 'boolean',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'products' => 'nullable|array',
            'products.*' => 'exists:products,id',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
        ]);

        $validated['code'] = Str::upper($validated['code']);
        $validated['is_active'] = $request->has('is_active');
        $validated['is_apply_sale'] = $request->has('is_apply_sale');
        $validated['is_apply_flash_sale'] = $request->has('is_apply_flash_sale');
        $validated['is_stackable'] = $request->has('is_stackable');
        $validated['min_order_amount'] = $validated['min_order_amount'] ?? 0;
        
        if ($validated['type'] == 'percent' && $validated['value'] > 100) {
            return back()->withErrors(['value' => 'Phần trăm giảm giá không được vượt quá 100%'])->withInput();
        }
        $validated['gift_product_id'] = null; // Removed gift feature

        $coupon = Coupon::create($validated);

        if (!empty($validated['categories'])) {
            $coupon->categories()->sync($validated['categories']);
        }
        if (!empty($validated['products'])) {
            $coupon->products()->sync($validated['products']);
        }
        if (!empty($validated['users'])) {
            $coupon->eligibleUsers()->sync($validated['users']);
        }

        return redirect()->route('admin.coupons.index')->with('success', 'Tạo mã giảm giá thành công.');
    }

    /**
     * Show the form for editing the specified coupon.
     */
    public function edit(Coupon $coupon)
    {
        $categories = Category::all();
        $products = Product::select('id', 'name')->get();
        $users = User::select('id', 'name', 'email')->get();
        return view('admin.coupons.edit', compact('coupon', 'categories', 'products', 'users'));
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
            'is_active' => 'boolean',
            'is_apply_sale' => 'boolean',
            'is_apply_flash_sale' => 'boolean',
            'is_stackable' => 'boolean',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'products' => 'nullable|array',
            'products.*' => 'exists:products,id',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
        ]);

        $validated['code'] = Str::upper($validated['code']);
        $validated['is_active'] = $request->has('is_active');
        $validated['is_apply_sale'] = $request->has('is_apply_sale');
        $validated['is_apply_flash_sale'] = $request->has('is_apply_flash_sale');
        $validated['is_stackable'] = $request->has('is_stackable');
        $validated['min_order_amount'] = $validated['min_order_amount'] ?? 0;
        
        if ($validated['type'] == 'percent' && $validated['value'] > 100) {
            return back()->withErrors(['value' => 'Phần trăm giảm giá không được vượt quá 100%'])->withInput();
        }
        $validated['gift_product_id'] = null;

        $coupon->update($validated);

        $coupon->categories()->sync($validated['categories'] ?? []);
        $coupon->products()->sync($validated['products'] ?? []);
        $coupon->eligibleUsers()->sync($validated['users'] ?? []);

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
