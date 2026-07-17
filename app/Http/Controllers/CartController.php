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

    /**
     * Hiển thị trang giỏ hàng.
     */
    public function index()
    {
        $items = $this->cartService->getItems();
        $total = $this->cartService->getTotal();

        return view('cart.index', compact('items', 'total'));
    }

    /**
     * Thêm sản phẩm vào giỏ hàng.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'variant_id' => 'nullable|integer|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            $item = $this->cartService->add(
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

    /**
     * Cập nhật số lượng qua AJAX.
     */
    public function update(Request $request, $idOrKey)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        try {
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

    /**
     * Xóa sản phẩm khỏi giỏ hàng.
     */
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

    /**
     * Mua ngay một sản phẩm từ trang chi tiết.
     * Lưu thông tin sản phẩm vào session và chuyển hướng đến trang thanh toán.
     * KHÔNG thêm vào giỏ hàng để tránh xung đột với các sản phẩm đã có trong giỏ.
     */
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

            // Lấy thông tin sản phẩm để kiểm tra tồn kho
            $product = \App\Models\Product::findOrFail($productId);
            $variant = $variantId ? \App\Models\ProductVariant::findOrFail($variantId) : null;

            // Tính giá
            $price = $product->effective_price;
            if ($variant) {
                $price += (float) $variant->additional_price;
            }

            // Kiểm tra tồn kho
            $availableQuantity = $this->cartService->getAvailableStock($product, $variant);
            if ($availableQuantity < $quantity) {
                throw new Exception("Sản phẩm này chỉ còn lại {$availableQuantity} sản phẩm trong kho.");
            }

            // Lưu thông tin sản phẩm vào session với key 'buy_now_item'
            // Điều này sẽ được sử dụng trong CheckoutController::index
            session()->put('buy_now_item', [
                'product_id' => $productId,
                'variant_id' => $variantId,
                'quantity' => $quantity,
                'price' => $price,
            ]);

            // Chuyển hướng đến trang thanh toán
            return redirect()->route('checkout')->with('success', 'Tiếp tục thanh toán.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }
}
