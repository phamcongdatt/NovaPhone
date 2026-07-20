<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class CartService
{
    /**
     * Lấy toàn bộ items trong giỏ hàng (hợp nhất cấu trúc giữa DB và Session).
     */
    public function getItems(): Collection
    {
        if (Auth::check()) {
            $cart = Cart::where('user_id', Auth::id())->first();
            if (! $cart) {
                return collect();
            }

            return $cart->items()->with(['product', 'variant'])->get();
        }

        $sessionCart = session()->get('cart', []);
        $items = collect();

        if (empty($sessionCart)) {
            return $items;
        }

        $productIds = collect($sessionCart)->pluck('product_id')->unique();
        $variantIds = collect($sessionCart)->pluck('variant_id')->filter()->unique();

        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
        $variants = ProductVariant::whereIn('id', $variantIds)->get()->keyBy('id');

        foreach ($sessionCart as $key => $item) {
            $product = $products->get($item['product_id']);
            if (! $product) {
                continue;
            }

            $variant = $item['variant_id'] ? $variants->get($item['variant_id']) : null;

            $mockItem = new CartItem([
                'product_id' => $item['product_id'],
                'variant_id' => $item['variant_id'],
                'quantity'   => $item['quantity'],
                'price'      => $item['price'],
            ]);

            $mockItem->setRelation('product', $product);
            if ($variant) {
                $mockItem->setRelation('variant', $variant);
            }

            $mockItem->setDisplayId((string) $key);
            $items->push($mockItem);
        }

        return $items;
    }

    /**
     * Lấy các item trong giỏ hàng đã được người dùng chọn để checkout.
     * Selection được lưu tạm trong session('checkout_selected_items') (danh sách id/key),
     * set bởi CartController::setSelection() khi bấm "Tiến hành thanh toán".
     *
     * Nếu không có session selection (vd. truy cập /checkout trực tiếp), fallback lấy toàn bộ giỏ hàng
     * để không phá vỡ hành vi hiện có.
     */
    public function getSelectedItems(): Collection
    {
        $items = $this->getItems();

        if (! session()->has('checkout_selected_items')) {
            return $items;
        }

        $selectedIds = array_map('strval', session()->get('checkout_selected_items', []));

        return $items->filter(fn (CartItem $item) => in_array($item->display_id, $selectedIds, true))->values();
    }

    /**
     * Xóa nhiều item khỏi giỏ hàng theo danh sách id/key (dùng sau khi đặt hàng thành công
     * để chỉ xóa các item đã checkout, giữ lại các item không được chọn).
     */
    public function removeMany(array $itemIdsOrKeys): void
    {
        foreach ($itemIdsOrKeys as $idOrKey) {
            $this->remove($idOrKey);
        }
    }

    /**
     * Thêm sản phẩm vào giỏ hàng.
     */
    public function add(int $productId, ?int $variantId, int $quantity = 1): CartItem
    {
        $product = Product::findOrFail($productId);
        $variant = $variantId ? ProductVariant::findOrFail($variantId) : null;

        $price = $product->effective_price;
        if ($variant) {
            $price += (float) $variant->additional_price;
        }

        $availableQuantity = $this->getAvailableStock($product, $variant);
        if ($availableQuantity < $quantity) {
            throw new Exception("Sản phẩm này chỉ còn lại {$availableQuantity} sản phẩm trong kho.");
        }

        $activeSale = $product->activeFlashSaleItem;
        $remainingFlashSaleQty = Auth::check() ? $product->getFlashSaleRemainingQuota() : ($activeSale ? $activeSale->max_per_user : null);

        if (Auth::check()) {
            $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $productId)
                ->where('variant_id', $variantId)
                ->first();

            if ($cartItem) {
                $newQty = $cartItem->quantity + $quantity;
                if ($availableQuantity < $newQty) {
                    throw new Exception("Không thể thêm số lượng đã chọn. Kho chỉ còn lại {$availableQuantity} sản phẩm.");
                }
                
                // Nếu chưa hết quota flash sale, chặn nếu cố tình mua vượt quota để hưởng giá rẻ
                if ($activeSale && $remainingFlashSaleQty > 0 && $newQty > $remainingFlashSaleQty) {
                    throw new Exception("Sản phẩm đang Flash Sale. Bạn chỉ còn {$remainingFlashSaleQty} lượt mua giá sốc. Vui lòng giảm số lượng.");
                }
                
                $cartItem->update(['quantity' => $newQty]);
            } else {
                if ($activeSale && $remainingFlashSaleQty > 0 && $quantity > $remainingFlashSaleQty) {
                    throw new Exception("Sản phẩm đang Flash Sale. Bạn chỉ còn {$remainingFlashSaleQty} lượt mua giá sốc. Vui lòng giảm số lượng.");
                }
                $cartItem = CartItem::create([
                    'cart_id'    => $cart->id,
                    'product_id' => $productId,
                    'variant_id' => $variantId,
                    'quantity'   => $quantity,
                    'price'      => $price,
                ]);
            }

            return $cartItem;
        }

        $sessionCart = session()->get('cart', []);
        $key = $this->generateSessionKey($productId, $variantId);

        if (isset($sessionCart[$key])) {
            $newQty = $sessionCart[$key]['quantity'] + $quantity;
            if ($availableQuantity < $newQty) {
                throw new Exception("Không thể thêm số lượng đã chọn. Kho chỉ còn lại {$availableQuantity} sản phẩm.");
            }
            if ($activeSale && $remainingFlashSaleQty !== null && $remainingFlashSaleQty > 0 && $newQty > $remainingFlashSaleQty) {
                throw new Exception("Sản phẩm đang Flash Sale, chỉ được mua tối đa {$remainingFlashSaleQty} sản phẩm giá sốc.");
            }
            $sessionCart[$key]['quantity'] = $newQty;
        } else {
            if ($activeSale && $remainingFlashSaleQty !== null && $remainingFlashSaleQty > 0 && $quantity > $remainingFlashSaleQty) {
                throw new Exception("Sản phẩm đang Flash Sale, chỉ được mua tối đa {$remainingFlashSaleQty} sản phẩm giá sốc.");
            }
            $sessionCart[$key] = [
                'product_id' => $productId,
                'variant_id' => $variantId,
                'quantity'   => $quantity,
                'price'      => $price,
            ];
        }

        session()->put('cart', $sessionCart);

        $mockItem = new CartItem($sessionCart[$key]);
        $mockItem->setDisplayId((string) $key);
        $mockItem->setRelation('product', $product);
        if ($variant) {
            $mockItem->setRelation('variant', $variant);
        }

        return $mockItem;
    }

    /**
     * Cập nhật số lượng của một item trong giỏ hàng.
     *
     * Quan trọng: $itemIdOrKey định danh duy nhất một dòng (cart_items.id cho user đã login,
     * hoặc key "{product_id}-{variant_id}" cho guest) nên việc cập nhật số lượng KHÔNG được
     * phép suy luận lại product_id/variant_id từ nơi khác - phải luôn thao tác trên đúng
     * variant đã xác định bởi key này để tránh nhầm lẫn màu/phiên bản giữa các dòng.
     */
    public function update($itemIdOrKey, int $quantity): CartItem
    {
        if ($quantity <= 0) {
            return $this->remove($itemIdOrKey);
        }

        if (Auth::check()) {
            $cartItem = CartItem::whereHas('cart', function ($query) {
                $query->where('user_id', Auth::id());
            })->findOrFail($itemIdOrKey);

            $availableQuantity = $this->getAvailableStock($cartItem->product, $cartItem->variant);
            if ($availableQuantity < $quantity) {
                throw new Exception("Kho chỉ còn lại {$availableQuantity} sản phẩm khả dụng.");
            }

            $activeSale = $cartItem->product->activeFlashSaleItem;
            $remainingFlashSaleQty = $cartItem->product->getFlashSaleRemainingQuota();
            if ($activeSale && $remainingFlashSaleQty > 0 && $quantity > $remainingFlashSaleQty) {
                throw new Exception("Sản phẩm đang Flash Sale. Bạn chỉ còn {$remainingFlashSaleQty} lượt mua giá sốc. Vui lòng giảm số lượng.");
            }

            $cartItem->update(['quantity' => $quantity]);
            return $cartItem;
        }

        $sessionCart = session()->get('cart', []);
        if (! isset($sessionCart[$itemIdOrKey])) {
            throw new Exception("Sản phẩm không có trong giỏ hàng.");
        }

        $itemData = $sessionCart[$itemIdOrKey];
        $product  = Product::findOrFail($itemData['product_id']);
        $variant  = $itemData['variant_id'] ? ProductVariant::findOrFail($itemData['variant_id']) : null;

        $availableQuantity = $this->getAvailableStock($product, $variant);
        if ($availableQuantity < $quantity) {
            throw new Exception("Kho chỉ còn lại {$availableQuantity} sản phẩm khả dụng.");
        }

        $activeSale = $product->activeFlashSaleItem;
        $remainingFlashSaleQty = Auth::check() ? $product->getFlashSaleRemainingQuota() : ($activeSale ? $activeSale->max_per_user : null);
        
        if ($activeSale && $remainingFlashSaleQty !== null && $remainingFlashSaleQty > 0 && $quantity > $remainingFlashSaleQty) {
            throw new Exception("Sản phẩm đang Flash Sale, chỉ được mua tối đa {$remainingFlashSaleQty} sản phẩm giá sốc.");
        }

        $sessionCart[$itemIdOrKey]['quantity'] = $quantity;
        session()->put('cart', $sessionCart);

        $mockItem = new CartItem($sessionCart[$itemIdOrKey]);
        $mockItem->setDisplayId((string) $itemIdOrKey);
        $mockItem->setRelation('product', $product);
        if ($variant) {
            $mockItem->setRelation('variant', $variant);
        }

        return $mockItem;
    }

    /**
     * Xóa một sản phẩm khỏi giỏ hàng.
     * - Nếu đã đăng nhập: xóa CartItem trong DB.
     * - Nếu chưa đăng nhập: xóa khỏi session theo key.
     */
    public function remove($itemIdOrKey): ?CartItem
    {
        if (Auth::check()) {
            $cartItem = CartItem::whereHas('cart', function ($query) {
                $query->where('user_id', Auth::id());
            })->findOrFail($itemIdOrKey);

            $cartItem->delete();
            return $cartItem;
        }

        $sessionCart = session()->get('cart', []);
        if (isset($sessionCart[$itemIdOrKey])) {
            $itemData = $sessionCart[$itemIdOrKey];
            unset($sessionCart[$itemIdOrKey]);
            session()->put('cart', $sessionCart);

            return new CartItem($itemData);
        }

        return null;
    }

    /**
     * Xóa sạch toàn bộ giỏ hàng.
     */
    public function clear(): void
    {
        if (Auth::check()) {
            $cart = Cart::where('user_id', Auth::id())->first();
            if ($cart) {
                $cart->items()->delete();
            }
        } else {
            session()->forget('cart');
        }
    }

    /**
     * Đếm tổng số lượng sản phẩm có trong giỏ hàng.
     */
    public function getCount(): int
    {
        return (int) $this->getItems()->sum('quantity');
    }

    /**
     * Lấy tổng giá trị tiền của giỏ hàng.
     */
    public function getTotal(): float
    {
        return (float) $this->getItems()->sum(fn ($item) => $item->price * $item->quantity);
    }

    /**
     * Hợp nhất giỏ hàng từ Session vào DB sau khi đăng nhập thành công.
     */
    public function mergeSessionCartToDb(): void
    {
        $sessionCart = session()->get('cart', []);
        if (empty($sessionCart)) {
            return;
        }

        foreach ($sessionCart as $item) {
            try {
                $this->add($item['product_id'], $item['variant_id'], $item['quantity']);
            } catch (Exception $e) {
                continue;
            }
        }

        session()->forget('cart');
    }

    /**
     * Lấy số lượng tồn kho khả dụng của sản phẩm / variant.
     */
    public function getAvailableStock(Product $product, ?ProductVariant $variant): int
    {
        if ($variant) {
            $inventory = $variant->inventory;
        } else {
            $inventory = $product->inventory;
        }

        return $inventory ? $inventory->available_quantity : 0;
    }

    /**
     * Tạo mã key duy nhất cho item trong session.
     */
    private function generateSessionKey(int $productId, ?int $variantId): string
    {
        return $variantId ? "{$productId}-{$variantId}" : "{$productId}-0";
    }
}