<?php

namespace App\Models;

use App\Enums\SupportRequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportRequest extends Model
{
    use HasFactory;

    protected $fillable = ['guest_id', 'room_id', 'issue', 'status'];

    protected function casts(): array
    {
        return ['status' => SupportRequestStatus::class];
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
