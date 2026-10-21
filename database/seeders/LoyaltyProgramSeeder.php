<?php

namespace Database\Seeders;

use App\Models\Guest;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LoyaltyProgramSeeder extends Seeder
{
    private const POINTS_PER_STAY = 100;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Guest::doesntHave('loyaltyProgram')
                ->withCount(['bookings as completed_stays_count' => fn ($query) => $query->where('status', 'checked_out')])
                ->get()
                ->filter(fn (Guest $guest) => $guest->completed_stays_count > 0)
                ->each(fn (Guest $guest) => $guest->loyaltyProgram()->create([
                    'points' => $guest->completed_stays_count * self::POINTS_PER_STAY,
                ]));
    }
}
