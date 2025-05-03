<?php

namespace Database\Factories;

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
        return [
            'attendance_id' => $this->faker->numberBetween($min = 1, $max = 50),
            'start_time' => $this->faker->time(),
            'end_time' => $this->faker->time(),
            'total_time' => $this->faker->time(),
        ];
    }
}
