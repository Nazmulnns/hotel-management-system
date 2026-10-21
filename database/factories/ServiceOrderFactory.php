<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Service;
use App\Models\ServiceOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceOrder>
 */
class ServiceOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory()->withRoom(),
            'service_id' => fn () => Service::inRandomOrder()->value('id') ?? Service::factory(),
            'quantity' => fake()->numberBetween(1, 10),
            'total_price' => function (array $attributes) {
                $service = Service::findOrFail($attributes['service_id']);

                return round($service->price * $attributes['quantity'], 2);
            },
        ];
    }
}
