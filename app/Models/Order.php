<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'customer_email', 'order_code', 'status',
        'payment_method', 'payment_status',
        'subtotal', 'discount_amount', 'tax_amount', 'shipping_fee', 'total_amount',
        'coupon_id', 'coupon_code',
        'shipping_full_name', 'shipping_phone', 'shipping_address',
        'shipping_ward', 'shipping_district', 'shipping_province',
        'shipping_province_code', 'shipping_ward_code', 'administrative_version',
        'note', 'cancelled_reason', 'cancelled_by', 'user_received_at', 'delivered_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'shipping_fee' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'user_received_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    /**
     * Các trạng thái đơn hàng được tính vào doanh số / sản phẩm bán chạy.
     */
    public const SALES_STATUSES = ['confirmed', 'processing', 'shipping', 'delivered', 'received'];

    /**
     * Đơn hàng đã giao hoặc người dùng đã xác nhận nhận hàng đều có thể review.
     */
    public const REVIEWABLE_STATUSES = ['delivered', 'received'];

    public function canBeReviewed(): bool
    {
        return in_array($this->status, self::REVIEWABLE_STATUSES, true)
            && ($this->payment_status === 'paid'
                || ($this->payment_method === 'cod' && $this->payment_status === 'pending'));
    }

    public function scopeReviewable(Builder $query): Builder
    {
        return $query
            ->whereIn('status', self::REVIEWABLE_STATUSES)
            ->where(function (Builder $query) {
                $query->where('payment_status', 'paid')
                    ->orWhere(function (Builder $query) {
                        $query->where('payment_method', 'cod')
                            ->where('payment_status', 'pending');
                    });
            });
    }

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            $order->order_code ??= 'NVP-'.strtoupper(Str::random(10));
        });
    }

    public function isCancellable(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function orderCoupons(): HasMany
    {
        return $this->hasMany(OrderCoupon::class);
    }

    public function returnRequest(): HasOne
    {
        return $this->hasOne(ReturnRequest::class);
    }

    public function canRequestReturn(): bool
    {
        return $this->status === 'received'
            && $this->delivered_at !== null
            && ! $this->returnRequest()->exists()
            && $this->delivered_at->gte(now()->subDays(ReturnRequest::RETURN_WINDOW_DAYS));
    }

    public function returnDeadline(): ?Carbon
    {
        return $this->delivered_at?->copy()->addDays(ReturnRequest::RETURN_WINDOW_DAYS);
    }
}
