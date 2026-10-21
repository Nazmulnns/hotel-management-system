<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Booking::where('status', 'checked_out')
            ->doesntHave('reviews')
            ->get()
            ->each(function (Booking $booking): void {
                if (fake()->boolean(70)) {
                    Review::factory()->for($booking)->create();
                }
            });
    }
}
