<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cancellation extends Model
{
    protected $fillable = ['booking_id', 'cancellation_reason', 'date'];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
