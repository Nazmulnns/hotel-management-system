<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_name'
    ];

    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(Room::class);
    }

    protected function facilityName(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => strtoupper(trim($value, " \"")),
        );
    }
}
