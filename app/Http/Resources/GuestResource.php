<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\FilesystemAdapter;

class GuestResource extends JsonResource
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
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'identity_number' => $this->identity_number,
            'date_of_birth' => $this->date_of_birth,
            'nationality' => $this->nationality,
            'address' => $this->address,
            'has_identity' => $this->hasIdentity(),
            'identity_front_image_url' => $this->privateImageUrl($this->identity_front_image),
            'identity_back_image_url' => $this->privateImageUrl($this->identity_back_image),
            'guest_photo_url' => $this->guest_photo ? Storage::disk('public')->url($this->guest_photo) : null,
            'bookings_count' => $this->whenCounted('bookings'),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }

    private function privateImageUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('local');

        return $disk->temporaryUrl($path, now()->plus(minutes: 5));
    }
}
