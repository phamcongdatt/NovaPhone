<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image',
        'badge',
        'highlights',
        'buy_url',
        'detail_url',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'highlights' => 'array',
        'is_active'  => 'boolean',
    ];
}
