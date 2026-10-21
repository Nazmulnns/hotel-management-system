<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Feedback;
use Illuminate\Database\Seeder;

class FeedbackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Booking::where('status', 'checked_out')
                ->doesntHave('feedbacks')
                ->get()
                ->each(function (Booking $booking): void {
                    if (fake()->boolean(60)) {
                        Feedback::factory()->for($booking)->create();
                    }
                });
    }
}
