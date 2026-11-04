<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupportRequestResource extends JsonResource
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
            'guest_id' => $this->guest_id,
            'guest' => $this->whenLoaded('guest', fn () => [
                'id' => $this->guest->id,
                'full_name' => $this->guest->full_name,
                'phone_number' => $this->guest->phone_number,
            ]),
            'room_id' => $this->room_id,
            'room' => $this->whenLoaded('room', fn () => $this->room ? [
                'id' => $this->room->id,
                'label' => "{$this->room->floor}{$this->room->room_number}",
            ] : null),
            'issue' => $this->issue,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'reported_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
