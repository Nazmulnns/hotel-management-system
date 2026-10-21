<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cancellation extends Model
{
    use HasFactory;

    protected $fillable = ['booking_id', 'cancellation_reason', 'cancellation_date'];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
