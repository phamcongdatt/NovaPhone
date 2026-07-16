<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Support\Collection;

class CouponService
{
    /**
     * Kiểm tra tính hợp lệ và áp dụng 1 mã giảm giá độc lập.
     */
    public function apply(string $code, ?User $user, Collection $cartItems, float $totalAmount): array
    {
        return $this->applyMultiple([$code], $user, $cartItems, $totalAmount);
    }

    /**
     * Kiểm tra và áp dụng nhiều mã giảm giá.
     * Trả về tổng discount_amount và chi tiết từng mã.
     */
    public function applyMultiple(array $codes, ?User $user, Collection $cartItems, float $totalAmount): array
    {
        $appliedCoupons = [];
        $totalDiscountAmount = 0;
        $rewardPoints = 0;
        $hasNonStackable = false;

        // Lấy danh sách coupons hợp lệ từ database
        $coupons = Coupon::with(['categories', 'products', 'eligibleUsers'])
            ->whereIn('code', array_map('strtoupper', $codes))
            ->get();

        if ($coupons->isEmpty() && !empty($codes)) {
            return $this->error('Mã giảm giá không tồn tại.');
        }

        foreach ($codes as $code) {
            $coupon = $coupons->firstWhere('code', strtoupper($code));

            if (!$coupon) {
                return $this->error("Mã giảm giá {$code} không tồn tại.");
            }

            // Kiểm tra stackable
            if (!$coupon->is_stackable && count($appliedCoupons) > 0) {
                return $this->error("Mã {$coupon->code} không thể dùng chung với các mã khác.");
            }
            if ($hasNonStackable) {
                return $this->error("Bạn đang dùng một mã không cho phép cộng dồn.");
            }
            if (!$coupon->is_stackable) {
                $hasNonStackable = true;
            }

            // Kiểm tra tính hợp lệ chung
            $validation = $this->validateCoupon($coupon, $user, $totalAmount);
            if (!$validation['success']) {
                return $validation; // Dừng lại nếu 1 mã bất kỳ không hợp lệ
            }

            // Kiểm tra Danh mục & Sản phẩm áp dụng, và tính toán
            $calcResult = $this->calculateForCoupon($coupon, $cartItems, $totalAmount);
            if (!$calcResult['success']) {
                return $calcResult;
            }

            $discountAmount = $calcResult['discount_amount'];
            
            // Đảm bảo tổng giảm không vượt quá tổng đơn
            if ($totalDiscountAmount + $discountAmount > $totalAmount) {
                $discountAmount = $totalAmount - $totalDiscountAmount;
            }

            $totalDiscountAmount += $discountAmount;

            $appliedCoupons[] = [
                'coupon' => $coupon,
                'discount_amount' => $discountAmount,
            ];
        }

        return [
            'success' => true,
            'message' => 'Áp dụng mã giảm giá thành công!',
            'discount_amount' => $totalDiscountAmount,
            'gift_product_id' => null, // Đã bị loại bỏ trong hệ thống
            'reward_points' => 0, // Đã bị loại bỏ hoặc giữ ở 0
            'coupons' => $appliedCoupons,
        ];
    }

    private function validateCoupon(Coupon $coupon, ?User $user, float $totalAmount): array
    {
        if (!$coupon->is_active) {
            return $this->error("Mã {$coupon->code} đã bị vô hiệu hóa.");
        }

        $now = now();
        if ($coupon->starts_at && $now->lt($coupon->starts_at)) {
            return $this->error("Mã {$coupon->code} chưa đến thời gian sử dụng.");
        }

        if ($coupon->expires_at && $now->gt($coupon->expires_at)) {
            return $this->error("Mã {$coupon->code} đã hết hạn.");
        }

        if ($coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) {
            return $this->error("Mã {$coupon->code} đã hết lượt sử dụng.");
        }

        if ($user) {
            if ($coupon->per_user_limit !== null) {
                $userUsedCount = $user->orders()->where('coupon_id', $coupon->id)->count();
                // Check in order_coupons too if needed, but keeping simple for now
                if ($userUsedCount >= $coupon->per_user_limit) {
                    return $this->error("Bạn đã hết lượt sử dụng mã {$coupon->code}.");
                }
            }

            if ($coupon->eligibleUsers->isNotEmpty()) {
                if (!$coupon->eligibleUsers->contains('id', $user->id)) {
                    return $this->error("Bạn không thuộc danh sách được dùng mã {$coupon->code}.");
                }
            }
        } elseif ($coupon->per_user_limit !== null || $coupon->eligibleUsers->isNotEmpty()) {
            return $this->error("Vui lòng đăng nhập để sử dụng mã {$coupon->code}.");
        }

        if ($coupon->min_order_amount > 0 && $totalAmount < $coupon->min_order_amount) {
            return $this->error("Giá trị đơn hàng chưa đạt tối thiểu (" . number_format($coupon->min_order_amount, 0, ',', '.') . "đ) để dùng mã {$coupon->code}.");
        }

        return ['success' => true];
    }

    private function calculateForCoupon(Coupon $coupon, Collection $cartItems, float $totalAmount): array
    {
        $validItemsTotal = 0;
        $hasValidItem = false;
        $requiresSpecificItems = $coupon->categories->isNotEmpty() || $coupon->products->isNotEmpty();

        foreach ($cartItems as $item) {
            $product = $item->product;
            $variant = $item->variant;
            $isValidForThisItem = true;

            // Kiểm tra Flash Sale
            $isFlashSaleItem = false;
            $activeSale = $product->activeFlashSaleItem;
            if ($activeSale) {
                $flashSalePrice = (float) ($product->price * (1 - $activeSale->discount_percent / 100));
                $basePrice = $flashSalePrice + ($variant ? (float) $variant->additional_price : 0);
                if (abs((float)$item->price - $basePrice) < 0.01) {
                    $isFlashSaleItem = true;
                }
            }

            // Kiểm tra Sale thường
            $isSaleItem = false;
            if (!$isFlashSaleItem && $product->sale_price > 0 && $product->sale_price < $product->price) {
                $isSaleItem = true;
            }

            if ($isFlashSaleItem && !$coupon->is_apply_flash_sale) {
                $isValidForThisItem = false;
            }

            if ($isSaleItem && !$coupon->is_apply_sale) {
                $isValidForThisItem = false;
            }

            if ($isValidForThisItem && $requiresSpecificItems) {
                $isValidForThisItem = false;
                
                // Kiểm tra Product
                if ($coupon->products->isNotEmpty() && $coupon->products->contains('id', $product->id)) {
                    $isValidForThisItem = true;
                }
                
                // Kiểm tra Category
                if (!$isValidForThisItem && $coupon->categories->isNotEmpty() && $coupon->categories->contains('id', $product->category_id)) {
                    $isValidForThisItem = true;
                }
            }

            if ($isValidForThisItem) {
                $hasValidItem = true;
                $validItemsTotal += ($item->price * $item->quantity);
            }
        }

        if (!$hasValidItem) {
            return $this->error("Mã {$coupon->code} không áp dụng cho sản phẩm nào trong giỏ hàng (hoặc không hỗ trợ hàng Sale/Flash Sale).");
        }

        $baseAmountForDiscount = $requiresSpecificItems ? $validItemsTotal : $validItemsTotal;
        // Wait, if !requiresSpecificItems, should it apply to totalAmount? No, it should only apply to valid items! 
        // Example: If is_apply_sale is false, the sale items are NOT valid items. So base amount must be validItemsTotal.

        $discountAmount = 0;
        switch ($coupon->type) {
            case 'percent':
                $discountAmount = $baseAmountForDiscount * ($coupon->value / 100);
                if ($coupon->max_discount > 0 && $discountAmount > $coupon->max_discount) {
                    $discountAmount = $coupon->max_discount;
                }
                break;

            case 'fixed':
                $discountAmount = $coupon->value;
                if ($discountAmount > $baseAmountForDiscount) {
                    $discountAmount = $baseAmountForDiscount;
                }
                break;
        }

        return [
            'success' => true,
            'discount_amount' => $discountAmount,
        ];
    }

    private function error(string $message): array
    {
        return [
            'success' => false,
            'message' => $message,
            'discount_amount' => 0,
            'gift_product_id' => null,
            'reward_points' => 0,
            'coupons' => [],
        ];
    }
}
