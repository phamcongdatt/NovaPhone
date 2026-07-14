<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'description', 'type', 'value', 'gift_product_id', 'max_discount',
        'min_order_amount', 'usage_limit', 'used_count', 'per_user_limit',
        'starts_at', 'expires_at', 'is_active', 'is_apply_sale', 'is_apply_flash_sale', 'is_stackable',
    ];

    protected function casts(): array
    {
        return [
            'value'            => 'decimal:2',
            'max_discount'     => 'decimal:2',
            'min_order_amount' => 'decimal:2',
            'is_active'        => 'boolean',
            'starts_at'        => 'datetime',
            'expires_at'       => 'datetime',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'coupon_category');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'coupon_product');
    }

    public function eligibleUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'coupon_user_eligibility');
    }

    public function giftProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'gift_product_id');
    }

    /**
     * Coupon con hieu luc khong (active, chua het han, con luot dung).
     */
    public function isUsable(): bool
    {
        if (! $this->is_active) {
            return false;
        }
        $now = now();
        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }
        if ($this->expires_at && $now->gt($this->expires_at)) {
            return false;
        }
        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return false;
        }
        return true;
    }
}