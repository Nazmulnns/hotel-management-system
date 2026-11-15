<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingNightsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'month' => $this->month,
            'booking_count' => (int) $this->booking_count,
            'total_nights' => (int) $this->total_nights,
            'average_nights' => (float) $this->average_nights,
        ];
    }
}
