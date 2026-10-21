<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\BookingRoom;
use App\Models\Cancellation;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cancellation>
 */
class CancellationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory()->cancelled()->withRoom(),
            'cancellation_reason' => fake()->randomElement(['Change of travel plans', 'Found a cheaper option', 'Medical emergency', 'Flight cancelled', 'Booked by mistake', 'Visa not approved',]),
            'cancellation_date' => function (array $attributes) {
                $booking = Booking::find($attributes['booking_id']);
                return CarbonImmutable::parse(fake()->dateTimeBetween(CarbonImmutable::parse($booking->booking_date)->min('today'), CarbonImmutable::parse($booking->check_in)->min('today')));
            }
         ];
    }
}
