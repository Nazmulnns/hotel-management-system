<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            // Food & Drink
            'Water' => 100,
            'Breakfast' => 500,
            'Lunch' => 800,
            'Dinner' => 900,
            'Room Service Charge' => 200,
            'Tea / Coffee' => 150,
            'Minibar Refill' => 1000,

            // Laundry
            'Laundry (per piece)' => 80,
            'Dry Cleaning' => 300,
            'Ironing (per piece)' => 50,

            // Transport
            'Airport Pickup' => 2500,
            'Airport Drop' => 2500,
            'Car Rental (per day)' => 6000,
            'Parking' => 300,

            // Room Extras
            'Extra Bed' => 1500,
            'Early Check-in' => 2000,
            'Late Check-out' => 2000,
            'Baby Cot' => 500,

            // Wellness & Leisure
            'Spa' => 3000,
            'Gym Access' => 500,
            'Swimming Pool' => 700,
            'City Tour' => 4000,

            // Business
            'Printing (per page)' => 20,
            'Meeting Room (per hour)' => 3000,
        ];

        foreach ($services as $name => $price) {
            Service::updateOrCreate(['service_name' => $name], ['price' => $price]);
        }
    }
}
