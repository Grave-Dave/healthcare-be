<?php

namespace Database\Factories;

use App\Models\Therapist;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Therapist>
 */
class TherapistsFactory extends Factory
{
    protected $model = Therapist::class;

    public function definition(): array
    {
        return [
            Therapist::USER_ID => Therapist::MAIN_THERAPIST_ID
        ];
    }
}
