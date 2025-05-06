<?php

namespace Database\Factories;

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
        return [
            'user_id' => $this->faker->numberBetween($min = 1, $max = 10),
            'date' => $this->faker->dateTimeBetween('-1 week', '+1 week'),
            'start_time' => $this->faker->time(),
            'end_time' => $this->faker->time(),
            'rest_total_time' => $this->faker->time(),
            'total_working_time' => $this->faker->time(),
        ];
    }
}
