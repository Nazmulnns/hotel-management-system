<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
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
            'room_number' => $this->room_number,
            'floor' => $this->floor,
            'room_type_id' => new RoomTypeResource($this->whenLoaded('roomType')),
            'price_per_night' => $this->price_per_night,
            'max_occupancy' => $this->max_occupancy,
            'facilities' => FacilityResource::collection($this->whenLoaded('facilities')),
            'images' => RoomImageResource::collection($this->whenLoaded('images')),
            'maintenances' => RoomMaintenanceResource::collection($this->whenLoaded('maintenances')),
            'booked_dates' => $this->whenLoaded('bookings', fn () => $this->bookings->map(fn ($booking) => [
                'from' => $booking->check_in->toDateString(),
                'to' => $booking->check_out->copy()->subDay()->toDateString(),
            ])),
            'reviews_count' => $this->whenCounted('reviews'),
            'average_rating' => $this->whenAggregated('reviews', 'rating', 'avg', fn ($average) => round((float) $average, 1)),
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
        ];
    }
}
