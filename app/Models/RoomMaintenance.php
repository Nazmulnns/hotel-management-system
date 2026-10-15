<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomMaintenance extends Model
{
    protected $fillable = [
        'issue_description',
        'maintenance_status'
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
