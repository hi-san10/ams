<?php

namespace Database\Factories;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

class StampCorrectionRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $carbon = new CarbonImmutable();

        return [
            'user_id' => $this->faker->numberBetween($min = 1, $max = 10),
            'attendance_id' => $this->faker->numberBetween($min = 1, $max = 10),
            'is_approval' => $this->faker->numberBetween($min = 0, $max = 1),
            'target_date' => $carbon,
            'request_date' => $carbon,
            'request_reason' => $this->faker->realText(10)
        ];
    }
}
