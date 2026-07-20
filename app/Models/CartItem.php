<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = ['cart_id', 'product_id', 'variant_id', 'quantity', 'price'];

    /**
     * Định danh dùng để hiển thị/target DOM/gọi API - KHÔNG dùng $this->id trực tiếp
     * cho các mock item (giỏ hàng session của guest, hoặc item "Mua ngay") vì Eloquent
     * luôn ép khóa chính về kiểu int mặc định, khiến các key dạng chuỗi như "5-3" hay
     * "buy_now_0" bị cắt/ép thành số (vd. "5-3" -> 5), gây trùng id giữa các dòng khác
     * nhau. displayId được set thủ công qua setDisplayId() cho mock item; với CartItem
     * thật lấy từ DB, nó mặc định bằng $this->id (int) như bình thường.
     */
    protected ?string $displayId = null;

    public function setDisplayId(string $displayId): static
    {
        $this->displayId = $displayId;

        return $this;
    }

    public function getDisplayIdAttribute(): string
    {
        return $this->displayId ?? (string) $this->id;
    }

    public function getSubtotalAttribute(): float
    {
        return $this->price * $this->quantity;
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}
