<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    protected $fillable = [
        'user_id', 'name', 'full_name', 'phone',
        'street', 'address', 'ward', 'district', 'province', 'is_default',
    ];

    protected function casts(): array
    {
        return ['is_default' => 'boolean'];
    }

    public function getFullAddressAttribute(): string
    {
        return "{$this->address}, {$this->ward}, {$this->district}, {$this->province}";
    }

    public function getNameAttribute(): ?string
    {
        return $this->full_name;
    }

    public function getStreetAttribute(): ?string
    {
        return $this->address;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
