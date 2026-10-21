<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Cancellation;
use Illuminate\Database\Seeder;

class CancellationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Booking::where('status', 'cancelled')
            ->doesntHave('cancellation')
            ->get()
            ->each(fn (Booking $booking) => Cancellation::factory()->for($booking)->create());
    }
}
