<?php

namespace Database\Seeders;

use App\Models\AvailableTerm;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AvailableTermSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        AvailableTerm::factory(10)->create();
    }
}
