<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingRoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'label' => "{$this->floor}{$this->room_number}",
            'room_type' => $this->whenLoaded('roomType', fn () => $this->roomType->name),
            'price_per_night' => $this->pivot->price_per_night,
            'guests' => $this->pivot->guests,
        ];
    }
}
