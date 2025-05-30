<?php

namespace Database\Factories;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

class RestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $date = new CarbonImmutable('10:01:00');

        return [
            'attendance_id' => $this->faker->unique->numberBetween($min = 1, $max = 50),
            'start_time' => $this->faker->dateTimeBetween($date, $date->modify('+20minute'))->format('H:i:s'),
            'end_time' => $this->faker->dateTimeBetween($date->modify('+30minute'), $date->modify('+40minute'))->format('H:i:s'),
        ];
    }
}
