<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'description', 'type', 'value', 'max_discount',
        'min_order_amount', 'usage_limit', 'used_count', 'per_user_limit',
        'starts_at', 'expires_at', 'is_active',
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