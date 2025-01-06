<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Location2Therapist;
use App\Models\Therapist;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Location2TherapistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {

        $locationsId = Location::pluck(Location::ID_COLUMN)->toArray();
        $therapistId = Therapist::MAIN_THERAPIST_ID;

        foreach ($locationsId as $locationId) {
            Location::find($locationId)->therapists()->attach($therapistId);
        }
    }
}
