<?php

namespace Database\Factories;

use App\Models\AvailableTerm;
use App\Models\Location;
use App\Models\Therapist;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<AvailableTerm>
 */
class AvailableTermFactory extends Factory
{
    protected $model = AvailableTerm::class;

    public function definition(): array
    {

        return [
            AvailableTerm::LOCATION_ID => Location::inRandomOrder()->first()->id,
            AvailableTerm::DATE => $this->faker->dateTimeBetween('+1 days', '+30 days')->format('Y-m-d'),
            AvailableTerm::TIME => $this->faker->numberBetween(0, 23),
            AvailableTerm::STATUS => $this->faker->randomElement(AvailableTerm::STATUS_ENUM),
            AvailableTerm::THERAPIST_ID => Therapist::MAIN_THERAPIST_ID,
            AvailableTerm::CREATED_AT => now(),
            AvailableTerm::UPDATED_AT => now(),
            AvailableTerm::CREATED_BY => null,
            AvailableTerm::UPDATED_BY => null,
        ];
    }
}
