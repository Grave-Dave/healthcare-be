<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\AvailableTerm;
use App\Models\Location;
use App\Models\Location2Therapist;
use App\Models\Therapist;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            LocationSeeder::class,
            TherapistSeeder::class,
            Location2TherapistSeeder::class,
//            AvailableTermSeeder::class,
        ]);
    }
}
