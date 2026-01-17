<?php

namespace Database\Seeders;

use App\Models\ActivityExercise;
use App\Models\MeasurementHeart;
use App\Models\MeasurementWeight;
use App\Models\MedicalAppointment;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Usuario Test',
            'email' => 'test@example.com',
            'password' => bcrypt('password'), // La contraseña será 'password'
        ]);

        // 2. Generar datos históricos para este usuario (últimos 3 meses)

        // 20 registros de Corazón
        MeasurementHeart::factory(20)->create(['user_id' => $user->id]);

        // 10 registros de Peso
        MeasurementWeight::factory(10)->create(['user_id' => $user->id]);

        // 15 registros de Ejercicio
        ActivityExercise::factory(15)->create(['user_id' => $user->id]);

        // 5 citas médicas
        MedicalAppointment::factory(5)->create(['user_id' => $user->id]);
    }
}
