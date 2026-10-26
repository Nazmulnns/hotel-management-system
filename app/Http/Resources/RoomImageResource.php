<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RoomImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('public');
        return [
            'id' => $this->id,
            'image_url' => Str::startsWith($this->image_url, 'http') ? $this->image_url : $disk->url($this->image_url),
        ];
    }
}
