<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call([
            RoomTypeSeeder::class,
            FacilitySeeder::class,
            ServiceSeeder::class,
            DiscountSeeder::class,
        ]);

        if (! app()->isProduction()) {
            $this->call([
                RoomSeeder::class,
                GuestSeeder::class,
                BookingSeeder::class,
                CancellationSeeder::class,
                ServiceOrderSeeder::class,
                AppliedDiscountSeeder::class,
                InvoiceSeeder::class,
                PaymentSeeder::class,
                RevenueSeeder::class,
                FeedbackSeeder::class,
                ReviewSeeder::class,
                SupportRequestSeeder::class,
                LoyaltyProgramSeeder::class,
            ]);
        }
    }
}
