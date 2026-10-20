<?php

namespace Database\Seeders;

use App\Models\Facility;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $facilities = [
            'Air Conditioning', 'Free WiFi', 'Smart TV', 'Attached Bathroom',
            'Hot Water', 'Mini Fridge', 'Electric Kettle', 'Room Service',
            'Safe Locker', 'Daily Housekeeping', 'Hair Dryer', 'Balcony',
            'Work Desk', 'Bathtub', 'Iron & Ironing Board', 'Non-smoking Room',
        ];

        foreach ($facilities as $name) {
            Facility::firstOrCreate(['facility_name' => $name]);
        }
    }
}
