<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Exception;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        $items = $this->cartService->getItems();
        $total = $this->cartService->getTotal();

        return view('cart.index', compact('items', 'total'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'variant_id' => 'nullable|integer|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            $this->cartService->add(
                $request->integer('product_id'),
                $request->input('variant_id') ? $request->integer('variant_id') : null,
                $request->integer('quantity')
            );

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đã thêm sản phẩm vào giỏ hàng!',
                    'cart_count' => $this->cartService->getCount(),
                    'cart_total' => $this->cartService->getTotal(),
                ]);
            }

            return redirect()->back()->with('success', 'Đã thêm sản phẩm vào giỏ hàng!');
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, $idOrKey)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            if ($idOrKey === 'buy_now_0') {
                return $this->updateBuyNowQuantity($request->integer('quantity'));
            }

            $item = $this->cartService->update($idOrKey, $request->integer('quantity'));
            $itemSubtotal = $item->price * $item->quantity;

            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật số lượng giỏ hàng.',
                'item_quantity' => $item->quantity,
                'item_subtotal' => number_format($itemSubtotal, 0, ',', '.') . 'đ',
                'cart_count' => $this->cartService->getCount(),
                'cart_total' => number_format($this->cartService->getTotal(), 0, ',', '.') . 'đ',
                'cart_total_raw' => $this->cartService->getTotal(),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    private function updateBuyNowQuantity(int $quantity)
    {
        if (! session()->has('buy_now_item')) {
            throw new Exception('Không tìm thấy sản phẩm "Mua ngay" trong phiên làm việc.');
        }

        $buyNowData = session()->get('buy_now_item');
        $product = \App\Models\Product::findOrFail($buyNowData['product_id']);
        if (! $product->is_active) {
            throw new Exception('Sản phẩm hiện không khả dụng.');
        }

        $variant = $buyNowData['variant_id'] ? \App\Models\ProductVariant::findOrFail($buyNowData['variant_id']) : null;
        $variant = $this->cartService->resolveVariant($product, $variant);
        if ($variant && ! $variant->is_active) {
            throw new Exception('Biến thể sản phẩm hiện không khả dụng.');
        }

        $availableQuantity = $this->cartService->getAvailableStock($product, $variant);
        if ($availableQuantity < $quantity) {
            throw new Exception("Kho chỉ còn lại {$availableQuantity} sản phẩm khả dụng.");
        }

        $buyNowData['quantity'] = $quantity;
        session()->put('buy_now_item', $buyNowData);

        $itemSubtotal = $buyNowData['price'] * $quantity;

        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật số lượng.',
            'item_quantity' => $quantity,
            'item_subtotal' => number_format($itemSubtotal, 0, ',', '.') . 'đ',
            'cart_count' => $this->cartService->getCount(),
            'cart_total' => number_format($itemSubtotal, 0, ',', '.') . 'đ',
            'cart_total_raw' => $itemSubtotal,
        ]);
    }

    public function destroy(Request $request, $idOrKey)
    {
        try {
            $this->cartService->remove($idOrKey);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đã xóa sản phẩm khỏi giỏ hàng.',
                    'cart_count' => $this->cartService->getCount(),
                    'cart_total' => number_format($this->cartService->getTotal(), 0, ',', '.') . 'đ',
                    'cart_total_raw' => $this->cartService->getTotal(),
                ]);
            }

            return redirect()->route('cart.index')->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->route('cart.index')->with('error', $e->getMessage());
        }
    }

    public function setSelection(Request $request)
    {
        $request->validate([
            'selected_item_ids' => 'required|array|min:1',
            'selected_item_ids.*' => 'required|string',
        ], [
            'selected_item_ids.required' => 'Vui lòng chọn ít nhất một sản phẩm để thanh toán.',
        ]);

        $validIds = $this->cartService->getItems()->pluck('display_id')->all();
        $selectedIds = array_values(array_intersect(
            array_map('strval', $request->input('selected_item_ids')),
            $validIds
        ));

        if (empty($selectedIds)) {
            return redirect()->route('cart.index')->with('error', 'Vui lòng chọn ít nhất một sản phẩm để thanh toán.');
        }

        session()->put('checkout_selected_items', $selectedIds);

        return redirect()->route('checkout');
    }

    public function buyNow(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'variant_id' => 'nullable|integer|exists:product_variants,id',
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        try {
            $productId = $request->integer('product_id');
            $variantId = $request->input('variant_id') ? $request->integer('variant_id') : null;
            $quantity = $request->integer('quantity');

            $product = \App\Models\Product::findOrFail($productId);
            if (! $product->is_active) {
                throw new Exception('Sản phẩm hiện không khả dụng.');
            }

            $variant = $variantId ? \App\Models\ProductVariant::findOrFail($variantId) : null;
            $variant = $this->cartService->resolveVariant($product, $variant);
            if ($variant && ! $variant->is_active) {
                throw new Exception('Biến thể sản phẩm hiện không khả dụng.');
            }
            $variantId = $variant?->id;

            $price = $product->effective_price;
            if ($variant) {
                $price += (float) $variant->additional_price;
            }

            $availableQuantity = $this->cartService->getAvailableStock($product, $variant);
            if ($availableQuantity < $quantity) {
                throw new Exception("Sản phẩm này chỉ còn lại {$availableQuantity} sản phẩm trong kho.");
            }

            session()->put('buy_now_item', [
                'product_id' => $productId,
                'variant_id' => $variantId,
                'quantity' => $quantity,
                'price' => $price,
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tiếp tục thanh toán.',
                    'redirect_url' => route('checkout'),
                ]);
            }

            return redirect()->route('checkout')->with('success', 'Tiếp tục thanh toán.');
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function clear(Request $request)
    {
        try {
            $this->cartService->clear();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đã xóa tất cả sản phẩm khỏi giỏ hàng.',
                ]);
            }

            return redirect()->route('cart.index')->with('success', 'Đã xóa tất cả sản phẩm khỏi giỏ hàng.');
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
