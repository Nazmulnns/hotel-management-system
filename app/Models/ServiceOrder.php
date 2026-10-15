<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceOrder extends Model
{
    protected $fillable = ['booking_id', 'service_id', 'quantity', 'total_price'];

    protected $touches = ['booking'];

    protected function casts(): array
    {
        return ['total_price' => 'decimal:2', 'quantity' => 'integer'];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
