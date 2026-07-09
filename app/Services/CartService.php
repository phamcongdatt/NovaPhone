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

        // Lấy từ session nếu chưa đăng nhập
        $sessionCart = session()->get('cart', []);
        $items = collect();

        if (empty($sessionCart)) {
            return $items;
        }

        // Load trước tất cả products và variants để tránh N+1 query trong session
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

            // Tạo đối tượng ảo giống CartItem để View render đồng nhất
            $mockItem = new CartItem([
                'product_id' => $item['product_id'],
                'variant_id' => $item['variant_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);

            // Thiết lập quan hệ ảo
            $mockItem->setRelation('product', $product);
            if ($variant) {
                $mockItem->setRelation('variant', $variant);
            }

            // Gán id ảo là key của session để dễ xử lý xóa/cập nhật
            $mockItem->id = $key;

            $items->push($mockItem);
        }

        return $items;
    }

    /**
     * Thêm sản phẩm vào giỏ hàng.
     */
    public function add(int $productId, ?int $variantId, int $quantity = 1): CartItem
    {
        $product = Product::findOrFail($productId);
        $variant = $variantId ? ProductVariant::findOrFail($variantId) : null;

        // Tính giá sản phẩm
        $price = $product->effective_price;
        if ($variant) {
            $price += (float) $variant->additional_price;
        }

        // Kiểm tra tồn kho khả dụng
        $availableQuantity = $this->getAvailableStock($product, $variant);
        if ($availableQuantity < $quantity) {
            throw new Exception("Sản phẩm này chỉ còn lại {$availableQuantity} sản phẩm trong kho.");
        }

        $activeSale = $product->activeFlashSaleItem;
        $maxFlashSaleQty = $activeSale ? $activeSale->max_per_user : null;

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
                if ($maxFlashSaleQty && $newQty > $maxFlashSaleQty) {
                    throw new Exception("Sản phẩm này đang trong Flash Sale, chỉ được mua tối đa {$maxFlashSaleQty} sản phẩm.");
                }
                $cartItem->update(['quantity' => $newQty]);
            } else {
                if ($maxFlashSaleQty && $quantity > $maxFlashSaleQty) {
                    throw new Exception("Sản phẩm này đang trong Flash Sale, chỉ được mua tối đa {$maxFlashSaleQty} sản phẩm.");
                }
                $cartItem = CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $productId,
                    'variant_id' => $variantId,
                    'quantity' => $quantity,
                    'price' => $price,
                ]);
            }

            return $cartItem;
        }

        // Lưu vào session nếu là khách vãng lai
        $sessionCart = session()->get('cart', []);
        $key = $this->generateSessionKey($productId, $variantId);

        if (isset($sessionCart[$key])) {
            $newQty = $sessionCart[$key]['quantity'] + $quantity;
            if ($availableQuantity < $newQty) {
                throw new Exception("Không thể thêm số lượng đã chọn. Kho chỉ còn lại {$availableQuantity} sản phẩm.");
            }
            if ($maxFlashSaleQty && $newQty > $maxFlashSaleQty) {
                throw new Exception("Sản phẩm này đang trong Flash Sale, chỉ được mua tối đa {$maxFlashSaleQty} sản phẩm.");
            }
            $sessionCart[$key]['quantity'] = $newQty;
        } else {
            if ($maxFlashSaleQty && $quantity > $maxFlashSaleQty) {
                throw new Exception("Sản phẩm này đang trong Flash Sale, chỉ được mua tối đa {$maxFlashSaleQty} sản phẩm.");
            }
            $sessionCart[$key] = [
                'product_id' => $productId,
                'variant_id' => $variantId,
                'quantity' => $quantity,
                'price' => $price,
            ];
        }

        session()->put('cart', $sessionCart);

        $mockItem = new CartItem($sessionCart[$key]);
        $mockItem->id = $key;
        $mockItem->setRelation('product', $product);
        if ($variant) {
            $mockItem->setRelation('variant', $variant);
        }

        return $mockItem;
    }

    /**
     * Cập nhật số lượng của một item trong giỏ hàng.
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
            if ($activeSale && $quantity > $activeSale->max_per_user) {
                throw new Exception("Sản phẩm đang Flash Sale, tối đa {$activeSale->max_per_user} sản phẩm.");
            }

            $cartItem->update(['quantity' => $quantity]);
            return $cartItem;
        }

        // Cập nhật session
        $sessionCart = session()->get('cart', []);
        if (! isset($sessionCart[$itemIdOrKey])) {
            throw new Exception("Sản phẩm không có trong giỏ hàng.");
        }

        $itemData = $sessionCart[$itemIdOrKey];
        $product = Product::findOrFail($itemData['product_id']);
        $variant = $itemData['variant_id'] ? ProductVariant::findOrFail($itemData['variant_id']) : null;

        $availableQuantity = $this->getAvailableStock($product, $variant);
        if ($availableQuantity < $quantity) {
            throw new Exception("Kho chỉ còn lại {$availableQuantity} sản phẩm khả dụng.");
        }

        $activeSale = $product->activeFlashSaleItem;
        if ($activeSale && $quantity > $activeSale->max_per_user) {
            throw new Exception("Sản phẩm đang Flash Sale, tối đa {$activeSale->max_per_user} sản phẩm.");
        }

        $sessionCart[$itemIdOrKey]['quantity'] = $quantity;
        session()->put('cart', $sessionCart);

        $mockItem = new CartItem($sessionCart[$itemIdOrKey]);
        $mockItem->id = $itemIdOrKey;
        $mockItem->setRelation('product', $product);
        if ($variant) {
            $mockItem->setRelation('variant', $variant);
        }

        return $mockItem;
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng.
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
                // Bỏ qua nếu có lỗi tồn kho khi merge, tránh làm gián đoạn đăng nhập
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
