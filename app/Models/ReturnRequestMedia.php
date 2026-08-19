<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnRequestMedia extends Model
{
    protected $table = 'return_request_media';

    protected $fillable = ['return_request_id', 'path', 'media_type', 'original_name'];

    public function returnRequest(): BelongsTo
    {
        return $this->belongsTo(ReturnRequest::class);
    }
}
