<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MedicalAppointment>
 */
class MedicalAppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $doctors = ['Dentista', 'Cardiólogo', 'Análisis de Sangre', 'Fisioterapeuta', 'Revisión General'];

        return [
            'date' => fake()->dateTimeBetween('-1 month', '+1 month'), // Citas pasadas y futuras
            'title' => fake()->randomElement($doctors),
            'description' => fake()->sentence(),
        ];
    }
}
