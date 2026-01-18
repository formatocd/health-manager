<?php

namespace App\Livewire\History;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MedicalAppointment;
use App\Models\ActivityExercise;
use App\Models\MeasurementWeight;
use App\Models\MeasurementHeart;
use Illuminate\Database\Eloquent\Builder;

class HealthHistory extends Component
{
    use WithPagination;

    // Propiedades para los filtros
    public $search = '';
    public $type = ''; // '' = Todos, 'appointment', 'exercise', 'weight', 'heart'

    public function render()
    {
        $userId = auth()->id();
        $records = collect();

        // 1. Lógica para CITAS (Si el filtro es 'Todos' o 'Cita')
        if (empty($this->type) || $this->type === 'appointment') {
            $query = MedicalAppointment::where('user_id', $userId);

            // Si hay búsqueda, filtramos por título o descripción
            if (!empty($this->search)) {
                $query->where(function (Builder $q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            }
            $records = $records->concat($query->latest('date')->get());
        }

        // 2. Lógica para EJERCICIOS
        if (empty($this->type) || $this->type === 'exercise') {
            $query = ActivityExercise::where('user_id', $userId);

            if (!empty($this->search)) {
                $query->where(function (Builder $q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            }
            $records = $records->concat($query->latest('date')->get());
        }

        // 3. Lógica para PESO (Solo tiene sentido buscar si filtramos por fecha, pero por ahora lo ocultamos si hay búsqueda de texto, o lo mostramos siempre)
        // Decisión de diseño: Si buscas texto "Dentista", no quieres ver pesos.
        if (empty($this->search) && (empty($this->type) || $this->type === 'weight')) {
            $records = $records->concat(MeasurementWeight::where('user_id', $userId)->latest('date')->get());
        }

        // 4. Lógica para CORAZÓN
        if (empty($this->search) && (empty($this->type) || $this->type === 'heart')) {
            $records = $records->concat(MeasurementHeart::where('user_id', $userId)->latest('date')->get());
        }

        // Ordenamos el resultado final mezclado
        $sortedRecords = $records->sortByDesc('date');

        return view('livewire.history.health-history', [
            'records' => $sortedRecords
        ])->layout('layouts.app');
    }
}
