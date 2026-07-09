<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FlashSale;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FlashSaleController extends Controller
{
    public function index()
    {
        $flashSales = FlashSale::withCount('items')->latest()->paginate(10);
        return view('admin.flash_sales.index', compact('flashSales'));
    }

    public function create()
    {
        $flashSale = new FlashSale();
        $products = Product::where('is_active', true)->select('id', 'name', 'price')->get();
        return view('admin.flash_sales.form', compact('flashSale', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'is_active' => 'boolean',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.discount_percent' => 'required|integer|min:1|max:99',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.max_per_user' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $flashSale = FlashSale::create([
                'name' => $request->name,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'is_active' => $request->boolean('is_active', true),
            ]);

            foreach ($request->items as $item) {
                $flashSale->items()->create([
                    'product_id' => $item['product_id'],
                    'discount_percent' => $item['discount_percent'],
                    'quantity' => $item['quantity'],
                    'max_per_user' => $item['max_per_user'],
                ]);
            }
        });

        return redirect()->route('admin.flash-sales.index')->with('success', 'Tạo chiến dịch Flash Sale thành công.');
    }

    public function edit(FlashSale $flashSale)
    {
        $flashSale->load('items.product');
        $products = Product::where('is_active', true)->select('id', 'name', 'price')->get();
        return view('admin.flash_sales.form', compact('flashSale', 'products'));
    }

    public function update(Request $request, FlashSale $flashSale)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'is_active' => 'boolean',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.discount_percent' => 'required|integer|min:1|max:99',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.max_per_user' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request, $flashSale) {
            $flashSale->update([
                'name' => $request->name,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'is_active' => $request->boolean('is_active', true),
            ]);

            $flashSale->items()->delete();

            foreach ($request->items as $item) {
                $flashSale->items()->create([
                    'product_id' => $item['product_id'],
                    'discount_percent' => $item['discount_percent'],
                    'quantity' => $item['quantity'],
                    'max_per_user' => $item['max_per_user'],
                    'sold' => $item['sold'] ?? 0, // keep sold if passed
                ]);
            }
        });

        return redirect()->route('admin.flash-sales.index')->with('success', 'Cập nhật chiến dịch Flash Sale thành công.');
    }

    public function destroy(FlashSale $flashSale)
    {
        $flashSale->delete();
        return redirect()->route('admin.flash-sales.index')->with('success', 'Đã xóa chiến dịch Flash Sale.');
    }
}
