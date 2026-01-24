<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MeasurementWeight>
 */
class MeasurementWeightFactory extends Factory
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
            'weight' => fake()->randomFloat(2, 60, 95), // Peso entre 60kg y 95kg
        ];
    }
}
