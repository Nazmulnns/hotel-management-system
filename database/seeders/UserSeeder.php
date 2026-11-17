<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->manager()->create([
            'name' => 'Hotel Manager',
            'email' => 'manager@hms.test',
        ]);

        User::factory()->create([
            'name' => 'Front Desk',
            'email' => 'reception@hms.test',
        ]);

        User::factory()->housekeeping()->create([
            'name' => 'Housekeeping Staff',
            'email' => 'housekeeping@hms.test',
        ]);

        User::factory()->inactive()->create([
            'name' => 'Former Staff',
            'email' => 'former@hms.test',
        ]);

        User::factory(5)
            ->state(fn () => ['role' => fake()->randomElement(UserRole::cases())])
            ->create();
    }
}
