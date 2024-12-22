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

        $locations = Location::pluck('id')->toArray();
        $therapists = Therapist::pluck('id')->toArray();

        foreach ($locations as $locationId) {

            $assignedTherapists = array_rand(array_flip($therapists), rand(1, count($therapists)));

            $assignedTherapists = is_array($assignedTherapists) ? $assignedTherapists : [$assignedTherapists];

            Location::find($locationId)->therapists()->attach($assignedTherapists);
        }
    }
}
