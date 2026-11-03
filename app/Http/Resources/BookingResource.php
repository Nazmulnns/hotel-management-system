<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
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
            'reference' => $this->reference,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'booking_date' => $this->booking_date->toDateString(),
            'check_in' => $this->check_in->toDateString(),
            'check_out' => $this->check_out->toDateString(),
            'nights' => (int) $this->check_in->diffInDays($this->check_out),
            'total_amount' => $this->total_amount,
            'guest_id' => $this->guest_id,
            'guest' => $this->whenLoaded('guest', fn () => [
                'id' => $this->guest->id,
                'full_name' => $this->guest->full_name,
                'phone_number' => $this->guest->phone_number,
            ]),
            'rooms' => BookingRoomResource::collection($this->whenLoaded('rooms')),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
