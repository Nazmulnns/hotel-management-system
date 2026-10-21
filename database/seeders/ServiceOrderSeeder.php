<?php

namespace Database\Seeders;

use App\Models\ServiceOrder;
use Illuminate\Database\Seeder;

class ServiceOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ServiceOrder::factory(60)->create();
    }
}
