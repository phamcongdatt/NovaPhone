<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description', 'content',
        'category_id', 'brand_id', 'price', 'sale_price',
        'thumbnail', 'sku', 'is_active', 'is_featured',
        'sold_count', 'view_count',
    ];

    protected function casts(): array
    {
        return [
            'price'       => 'decimal:2',
            'sale_price'  => 'decimal:2',
            'is_active'   => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function getFlashSalePurchasedCount(): int
    {
        $userId = \Illuminate\Support\Facades\Auth::id();
        if (!$userId) {
            return 0;
        }

        $activeSale = $this->activeFlashSaleItem;
        if (!$activeSale) {
            return 0;
        }

        $flashSale = $activeSale->flashSale;
        return \App\Models\OrderItem::where('product_id', $this->id)
            ->whereHas('order', function ($query) use ($userId, $flashSale) {
                $query->where('user_id', $userId)
                      ->whereNotIn('status', ['cancelled', 'returned'])
                      ->whereBetween('created_at', [$flashSale->start_time, $flashSale->end_time]);
            })
            ->sum('quantity');
    }

    public function getFlashSaleRemainingQuota(): int
    {
        $activeSale = $this->activeFlashSaleItem;
        if (!$activeSale) {
            return 0;
        }

        $purchasedCount = $this->getFlashSalePurchasedCount();
        $remaining = $activeSale->max_per_user - $purchasedCount;
        return $remaining > 0 ? $remaining : 0;
    }

    public function getEffectivePriceAttribute(): float
    {
        $activeSale = $this->activeFlashSaleItem;
        if ($activeSale) {
            // Kiểm tra giới hạn mua của người dùng hiện tại (nếu đã đăng nhập)
            $remaining = $this->getFlashSaleRemainingQuota();
            
            // Nếu người dùng đã mua hết hoặc vượt số lượng cho phép, trả về giá gốc (sale_price thường hoặc price)
            if ($remaining <= 0 && \Illuminate\Support\Facades\Auth::check()) {
                return (float) ($this->sale_price ?? $this->price);
            }
            
            return (float) ($this->price * (1 - $activeSale->discount_percent / 100));
        }
        return (float) ($this->sale_price ?? $this->price);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class)->whereNull('variant_id');
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function flashSaleItems(): HasMany
    {
        return $this->hasMany(FlashSaleItem::class);
    }

    public function activeFlashSaleItem(): HasOne
    {
        return $this->hasOne(FlashSaleItem::class)
            ->whereHas('flashSale', function ($q) {
                $q->where('is_active', true)
                  ->where('start_time', '<=', now())
                  ->where('end_time', '>=', now());
            })
            ->whereColumn('sold', '<', 'quantity');
    }
}
