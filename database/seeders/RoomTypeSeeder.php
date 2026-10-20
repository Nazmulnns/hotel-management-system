<?php

namespace Database\Seeders;

use App\Models\RoomType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roomTypes = [
            ['name' => 'Single', 'description' => 'One single bed, ideal for solo travellers.'],
            ['name' => 'Double', 'description' => 'One double bed for two guests.'],
            ['name' => 'Deluxe', 'description' => 'Spacious room with city view.'],
            ['name' => 'Suite', 'description' => 'Separate living area and premium amenities.'],
        ];

        foreach ($roomTypes as $roomType) {
            RoomType::updateOrCreate(['name' => $roomType['name']], ['description' => $roomType['description']]);
        }
    }
}
