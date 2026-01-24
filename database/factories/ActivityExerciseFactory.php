<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ActivityExercise>
 */
class ActivityExerciseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $activities = ['Correr', 'Gimnasio', 'Natación', 'Caminata', 'Yoga', 'Ciclismo'];

        return [
            'date' => fake()->dateTimeBetween('-3 months', 'now'),
            'title' => fake()->randomElement($activities),
            'description' => fake()->sentence(),
            'duration_minutes' => fake()->numberBetween(30, 90),
        ];
    }
}
