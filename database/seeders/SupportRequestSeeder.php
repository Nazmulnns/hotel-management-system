<?php

namespace Database\Seeders;

use App\Models\SupportRequest;
use Illuminate\Database\Seeder;

class SupportRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SupportRequest::factory(100)->create();
    }
}
