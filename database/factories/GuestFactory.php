<?php

namespace Database\Factories;

use App\Models\Guest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Guest>
 */
class GuestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'full_name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone_number' => '01' . fake()->numberBetween(3, 9) . fake()->unique()->numerify('########'),
            'identity_number' => fake()->unique()->numerify('###########'),
            'identity_front_image' => 'guests/identity/' . fake()->uuid() . '-front.jpg',
            'identity_back_image' => 'guests/identity/' . fake()->uuid() . '-back.jpg',
            'date_of_birth' => fake()->dateTimeBetween('-75 years', '-18 years'),
            'nationality' => fake()->randomElement(['Bangladeshi', 'Bangladeshi', 'Bangladeshi', 'Nepali', 'Indian', 'American', 'Pakistani']),
            'address' => fake()->address(),
            'guest_photo' => 'guest/photo/' . fake()->uuid() . '.jpg',
        ];
    }
}
