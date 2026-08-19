<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ReturnRequest extends Model
{
    public const RETURN_WINDOW_DAYS = 30;

    public const STATUS_LABELS = [
        'requested' => 'Chờ gửi hàng về cửa hàng',
        'return_shipping' => 'Đang vận chuyển về cửa hàng',
        'store_received' => 'Cửa hàng đã nhận hàng',
        'approved' => 'Đã duyệt hoàn hàng',
        'refund_processing' => 'VNPay đang xử lý hoàn tiền',
        'refund_review_required' => 'Cần đối soát VNPay thủ công',
        'refund_failed' => 'VNPay từ chối hoàn tiền',
        'rejected' => 'Từ chối hoàn hàng',
        'completed' => 'Đã hoàn tiền và đóng yêu cầu',
    ];

    protected $fillable = [
        'order_id', 'user_id', 'return_code', 'status', 'reason', 'note',
        'shipping_carrier', 'tracking_code', 'shipped_at', 'store_received_at',
        'reviewed_at', 'reviewed_by', 'admin_note', 'refund_amount',
        'refund_method', 'refund_reference', 'refund_bank_name', 'refund_bank_account',
        'refund_account_name', 'original_shipping_refund',
        'return_shipping_fee', 'refunded_at', 'completed_at',
        'refund_requested_at', 'last_refund_checked_at', 'refund_check_attempts',
        'refund_failure_reason',
    ];

    protected function casts(): array
    {
        return [
            'refund_amount' => 'decimal:2', 'shipped_at' => 'datetime',
            'original_shipping_refund' => 'decimal:2', 'return_shipping_fee' => 'decimal:2',
            'refund_bank_account' => 'encrypted',
            'store_received_at' => 'datetime', 'reviewed_at' => 'datetime',
            'refunded_at' => 'datetime', 'completed_at' => 'datetime',
            'refund_requested_at' => 'datetime', 'last_refund_checked_at' => 'datetime',
            'refund_check_attempts' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (ReturnRequest $returnRequest) {
            $returnRequest->return_code ??= 'RTN-'.strtoupper(Str::random(10));
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ReturnRequestItem::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(ReturnRequestMedia::class);
    }

    public function calculatedRefundAmount(): float
    {
        $this->loadMissing('items');

        return (float) $this->items->sum('refund_amount')
            + (float) $this->original_shipping_refund
            + (float) $this->return_shipping_fee;
    }
}
