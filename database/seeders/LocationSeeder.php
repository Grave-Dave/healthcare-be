<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Location::firstOrCreate(
            [
                Location::LOCATION_NAME => 'Obornicka 77k/1b, 51-114 Wrocław'
            ]
        );

        Location::firstOrCreate(
            [
                Location::LOCATION_NAME => 'Legnicka 55a/3, 54-234 Wrocław'
            ]
        );

        Location::firstOrCreate(
            [
                Location::LOCATION_NAME => 'Otmuchowska 7/4, 50-505 Wrocław'
            ]
        );
    }
}
