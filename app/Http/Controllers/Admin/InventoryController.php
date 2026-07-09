<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\InventoryHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    /**
     * Danh sách sản phẩm và tồn kho.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $categoryId = $request->input('category_id');
        $stockStatus = $request->input('status');

        $query = Inventory::with(['product.category', 'variant']);

        // Tìm kiếm theo tên sản phẩm
        if ($search) {
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Lọc theo danh mục
        if ($categoryId) {
            $query->whereHas('product', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        // Lọc theo trạng thái tồn kho
        if ($stockStatus) {
            if ($stockStatus === 'in_stock') {
                $query->where('quantity', '>', 5);
            } elseif ($stockStatus === 'low_stock') {
                $query->whereBetween('quantity', [1, 5]);
            } elseif ($stockStatus === 'out_of_stock') {
                $query->where('quantity', '=', 0);
            }
        }

        // Phân trang danh sách tồn kho
        $inventories = $query->latest('updated_at')->paginate(15)->withQueryString();

        // ─── Thống kê Dashboard Kho ─────────────────────────
        $totalProducts = DB::table('inventories')->distinct('product_id')->count('product_id');
        $totalItemsInStock = DB::table('inventories')->sum('quantity');
        $lowStockCount = DB::table('inventories')->whereBetween('quantity', [1, 5])->count();
        $outOfStockCount = DB::table('inventories')->where('quantity', '=', 0)->count();

        // Lấy danh sách danh mục để hiển thị trong bộ lọc
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('admin.inventory.index', [
            'inventories' => $inventories,
            'categories' => $categories,
            'filters' => [
                'search' => $search,
                'category_id' => $categoryId,
                'status' => $stockStatus,
            ],
            'stats' => [
                'total_products' => $totalProducts,
                'total_stock' => $totalItemsInStock,
                'low_stock' => $lowStockCount,
                'out_of_stock' => $outOfStockCount,
            ],
        ]);
    }

    /**
     * Nhập kho sản phẩm.
     */
    public function import(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
            'note' => ['nullable', 'string', 'max:255'],
        ], [
            'quantity.required' => 'Vui lòng nhập số lượng.',
            'quantity.integer' => 'Số lượng phải là số nguyên.',
            'quantity.min' => 'Số lượng nhập phải lớn hơn 0.',
        ]);

        DB::transaction(function () use ($inventory, $validated) {
            $inventory->increment('quantity', $validated['quantity']);

            InventoryHistory::create([
                'product_id' => $inventory->product_id,
                'variant_id' => $inventory->variant_id,
                'type' => 'import',
                'quantity' => $validated['quantity'],
                'note' => $validated['note'] ?? 'Nhập kho thủ công',
                'user_id' => Auth::id(),
            ]);
        });

        return back()->with('success', 'Nhập kho thành công số lượng ' . $validated['quantity'] . ' sản phẩm.');
    }

    /**
     * Xuất kho sản phẩm.
     */
    public function export(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:' . $inventory->quantity],
            'note' => ['nullable', 'string', 'max:255'],
        ], [
            'quantity.required' => 'Vui lòng nhập số lượng.',
            'quantity.integer' => 'Số lượng phải là số nguyên.',
            'quantity.min' => 'Số lượng xuất phải lớn hơn 0.',
            'quantity.max' => 'Số lượng xuất không được vượt quá số lượng tồn hiện có (' . $inventory->quantity . ').',
        ]);

        DB::transaction(function () use ($inventory, $validated) {
            $inventory->decrement('quantity', $validated['quantity']);

            InventoryHistory::create([
                'product_id' => $inventory->product_id,
                'variant_id' => $inventory->variant_id,
                'type' => 'export',
                'quantity' => $validated['quantity'],
                'note' => $validated['note'] ?? 'Xuất kho thủ công',
                'user_id' => Auth::id(),
            ]);
        });

        return back()->with('success', 'Xuất kho thành công số lượng ' . $validated['quantity'] . ' sản phẩm.');
    }

    /**
     * Điều chỉnh tồn kho.
     */
    public function adjust(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:0'],
            'note' => ['nullable', 'string', 'max:255'],
        ], [
            'quantity.required' => 'Vui lòng nhập số lượng tồn mới.',
            'quantity.integer' => 'Số lượng phải là số nguyên.',
            'quantity.min' => 'Số lượng tồn không thể là số âm.',
        ]);

        $oldQty = $inventory->quantity;
        $newQty = $validated['quantity'];
        $diff = $newQty - $oldQty;

        if ($diff === 0) {
            return back()->with('success', 'Số lượng tồn không đổi. Không có điều chỉnh nào được ghi nhận.');
        }

        DB::transaction(function () use ($inventory, $oldQty, $newQty, $diff, $validated) {
            $inventory->update(['quantity' => $newQty]);

            InventoryHistory::create([
                'product_id' => $inventory->product_id,
                'variant_id' => $inventory->variant_id,
                'type' => 'adjust',
                'quantity' => $diff, // Lưu chênh lệch (+ hoặc -)
                'note' => $validated['note'] ?? "Điều chỉnh số lượng tồn từ {$oldQty} thành {$newQty}",
                'user_id' => Auth::id(),
            ]);
        });

        return back()->with('success', "Điều chỉnh tồn kho thành công (Từ {$oldQty} thành {$newQty}).");
    }

    /**
     * Xem lịch sử kho.
     */
    public function history(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('type');

        $query = InventoryHistory::with(['product', 'variant', 'user']);

        // Tìm kiếm theo tên sản phẩm, SKU hoặc ghi chú
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('product', function ($p) use ($search) {
                    $p->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
                })->orWhere('note', 'like', "%{$search}%");
            });
        }

        // Lọc theo loại biến động kho
        if ($type && in_array($type, ['import', 'export', 'adjust'])) {
            $query->where('type', $type);
        }

        $histories = $query->latest()->paginate(20)->withQueryString();

        return view('admin.inventory.history', [
            'histories' => $histories,
            'filters' => [
                'search' => $search,
                'type' => $type,
            ],
        ]);
    }
}
