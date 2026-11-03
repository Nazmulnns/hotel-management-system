<?php

namespace App\Models;

use App\Enums\MaintenanceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomMaintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'issue_description',
        'maintenance_status',
    ];

    protected function casts(): array
    {
        return ['maintenance_status' => MaintenanceStatus::class];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
