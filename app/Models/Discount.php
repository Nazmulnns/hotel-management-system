<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Discount extends Model
{
    protected $fillable = ['discount_code', 'discount_percentage', 'expiry_date'];

    protected function casts(): array
    {
        return ['discount_percentage' => 'decimal:2', 'expiry_date' => 'date'];
    }

    public function bookings(): BelongsToMany
    {
        return $this->belongsToMany(Booking::class, 'applied_discounts')
            ->withPivot('amount_saved')
            ->withTimestamps();
    }
}
