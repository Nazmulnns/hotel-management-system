<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyalityProgram extends Model
{
    protected $fillable = ['guest_id', 'points'];

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }
}
