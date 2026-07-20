<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Review extends Model
{
    protected $fillable = [
        'user_id', 'product_id', 'order_id',
        'rating', 'comment', 'images', 'is_visible',
    ];

    protected function casts(): array
    {
        return [
            'images'     => 'array',
            'is_visible' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (Review $review) {
            $localImages = collect($review->images ?? [])
                ->map(fn ($image) => preg_replace('#^storage/#', '', ltrim($image, '/')))
                ->filter(fn ($image) => str_starts_with($image, 'reviews/'))
                ->all();

            if ($localImages) {
                Storage::disk('public')->delete($localImages);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
