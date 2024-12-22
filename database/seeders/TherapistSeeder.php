<?php

namespace Database\Seeders;

use App\Models\Therapist;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TherapistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Therapist::firstOrCreate(
            [
                Therapist::ID_COLUMN => 1,
                Therapist::USER_ID => Therapist::MAIN_THERAPIST_ID
            ]);
    }
}
