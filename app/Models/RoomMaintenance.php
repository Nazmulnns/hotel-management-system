<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomMaintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'issue_description',
        'maintenance_status'
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
