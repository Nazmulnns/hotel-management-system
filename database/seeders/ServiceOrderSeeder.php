<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\ServiceOrder;
use Illuminate\Database\Seeder;

class ServiceOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Booking::whereIn('status', ['checked_in', 'checked_out'])
            ->doesntHave('serviceOrders')
            ->get()
            ->each(fn (Booking $booking) => ServiceOrder::factory()
                ->count(fake()->numberBetween(0, 3))
                ->for($booking)
                ->create());
    }
}
