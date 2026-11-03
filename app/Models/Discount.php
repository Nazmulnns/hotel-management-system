<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discount extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $timestamps = false;
    protected $fillable = ['discount_code', 'percentage', 'expiry_date'];

    protected function casts(): array
    {
        return ['percentage' => 'decimal:2', 'expiry_date' => 'date'];
    }

    public function bookings(): BelongsToMany
    {
        return $this->belongsToMany(Booking::class, 'applied_discounts')
            ->withPivot('amount_saved')
            ->withTimestamps();
    }
}
