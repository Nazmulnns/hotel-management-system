<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomOccupancyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'room_id' => (int) $this->room_id,
            'room_label' => $this->room_label,
            'booked_nights' => (int) $this->booked_nights,
            'total_nights' => (int) $this->total_nights,
            'occupancy_percent' => (float) $this->occupancy_percent,
        ];
    }
}
