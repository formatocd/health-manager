<?php

namespace App\Livewire\History;

use Livewire\Component;
use App\Models\MeasurementWeight;
use Carbon\Carbon;

class HealthStats extends Component
{
    public $weightLabels = []; // Fechas
    public $weightData = [];   // Kilos

    public function mount()
    {
        $userId = auth()->id();

        // Obtenemos los pesos ordenados por fecha ascendente (de antiguo a nuevo)
        $weights = MeasurementWeight::where('user_id', $userId)
            ->orderBy('date', 'asc')
            ->get();

        // Preparamos los arrays para Chart.js
        foreach ($weights as $record) {
            $this->weightLabels[] = $record->date->format('d/m/Y');
            $this->weightData[] = $record->weight;
        }
    }

    public function render()
    {
        return view('livewire.history.health-stats')->layout('layouts.app');
    }
}
