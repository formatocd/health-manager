<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MeasurementHeart>
 */
class MeasurementHeartFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date' => fake()->dateTimeBetween('-3 months', 'now'),
            'bpm' => fake()->numberBetween(60, 100),
            'systolic' => fake()->numberBetween(110, 140),
            'diastolic' => fake()->numberBetween(70, 90),
        ];
    }
}
