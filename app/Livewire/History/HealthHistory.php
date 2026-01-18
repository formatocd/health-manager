<?php

namespace App\Livewire\History;

use Livewire\Component;
use Livewire\WithPagination; // Para que no salga una lista infinita
use App\Models\MedicalAppointment;
use App\Models\ActivityExercise;
use App\Models\MeasurementWeight;
use App\Models\MeasurementHeart;

class HealthHistory extends Component
{
    use WithPagination;

    public function render()
    {
        $userId = auth()->id();

        // 1. Obtenemos las colecciones (Limitamos a últimos 50 de cada uno por ahora para no saturar)
        // Nota: En el siguiente ticket (Filtros) haremos esto más elegante con uniones de SQL.
        // Para la HM-21 básica, vamos a traerlos y mezclarlos en memoria (collection).

        $appointments = MedicalAppointment::where('user_id', $userId)->latest('date')->get();
        $exercises = ActivityExercise::where('user_id', $userId)->latest('date')->get();
        $weights = MeasurementWeight::where('user_id', $userId)->latest('date')->get();
        $hearts = MeasurementHeart::where('user_id', $userId)->latest('date')->get();

        // 2. Mezclamos todo en una sola colección y ordenamos
        $allRecords = $appointments->concat($exercises)
                                   ->concat($weights)
                                   ->concat($hearts)
                                   ->sortByDesc('date');

        // 3. Paginación manual (un truco para colecciones mezcladas)
        // O para esta primera versión, mostramos una tabla simple sin paginar si no tienes miles de datos.
        // Vamos a enviarlo todo a la vista primero.

        return view('livewire.history.health-history', [
            'records' => $allRecords
        ])->layout('layouts.app'); // Asegúrate de usar tu layout principal
    }
}
