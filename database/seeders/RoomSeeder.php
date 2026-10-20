<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\Room;
use App\Models\RoomImage;
use App\Models\RoomMaintenance;
use App\Models\RoomType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $facilities = Facility::all();
        $roomPlan = [
            'Single' => ['count' => 40, 'min_price' => 2000, 'max_price' => 3000, 'max_occupancy' => 1],
            'Double' => ['count' => 35, 'min_price' => 3500, 'max_price' => 5000, 'max_occupancy' => 2],
            'Deluxe' => ['count' => 15, 'min_price' => 6000, 'max_price' => 9000, 'max_occupancy' => 3],
            'Suite' => ['count' => 10, 'min_price' => 12000, 'max_price' => 15000, 'max_occupancy' => 4],
        ];

        foreach ($roomPlan as $roomTypeName => $plan) {
            $roomType = RoomType::where('name', $roomTypeName)->firstOrFail();
            Room::factory($plan['count'])
                ->for($roomType)
                ->state(fn (array $attributes) => [
                    'price_per_night' => fake()->randomFloat(2, $plan['min_price'], $plan['max_price']),
                    'max_occupancy' => $plan['max_occupancy'],
                ])
                ->has(RoomImage::factory()->count(3), 'images')
                ->has(RoomMaintenance::factory()->count(1), 'maintenances')
                ->create()
                ->each(function (Room $room) use ($facilities): void {
                    $room->facilities()->attach(
                        $facilities->random(fake()->numberBetween(2, 5))->pluk('id');
                    )
                });
        }
    }
}
