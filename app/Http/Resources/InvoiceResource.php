<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
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
            'invoice_number' => $this->invoice_number,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'issue_date' => $this->issue_date->toDateString(),
            'due_date' => $this->due_date->toDateString(),
            'booking_id' => $this->booking_id,
            'booking' => $this->whenLoaded('booking', fn () => [
                'id' => $this->booking->id,
                'reference' => $this->booking->reference,
                'check_in' => $this->booking->check_in->toDateString(),
                'check_out' => $this->booking->check_out->toDateString(),
                'guest' => $this->booking->relationLoaded('guest') ? [
                    'id' => $this->booking->guest->id,
                    'full_name' => $this->booking->guest->full_name,
                    'phone_number' => $this->booking->guest->phone_number,
                ] : null,
            ]),
            'line_items' => $this->line_items ?? [],
            'room_total' => $this->room_total,
            'services_total' => $this->services_total,
            'subtotal' => $this->subtotal,
            'discount_total' => $this->discount_total,
            'tax_total' => $this->tax_total,
            'grand_total' => $this->grand_total,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
