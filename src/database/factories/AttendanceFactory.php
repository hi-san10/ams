<?php

namespace Database\Factories;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $date = new CarbonImmutable('08:00:00');

        return [
            'user_id' => $this->faker->numberBetween($min = 1, $max = 10),
            'date' => $this->faker->dateTimeBetween('-2month', '+2month'),
            'start_time' => $this->faker->dateTimeBetween($date, $date->modify('+2hour'))->format('H:i:s'),
            'end_time' => $this->faker->dateTimeBetween($date->modify('+8hour'), $date->modify('+10hour'))->format('H:i:s'),
        ];
    }
}
