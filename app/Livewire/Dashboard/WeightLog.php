<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\MeasurementWeight;
use Carbon\Carbon;

class WeightLog extends Component
{
    public $weight;
    public $date;

    public function mount()
    {
        // Por defecto, fecha y hora actuales
        $this->date = Carbon::now()->format('Y-m-d\TH:i');
    }

    public function save()
    {
        // 1. Validar
        $this->validate([
            'weight' => 'required|numeric|min:1|max:500', // Kilos lógicos
            'date' => 'required|date',
        ]);

        // 2. Crear registro
        MeasurementWeight::create([
            'user_id' => auth()->id(),
            'weight' => $this->weight,
            'date' => $this->date,
        ]);

        // 3. Resetear formulario
        $this->reset('weight');
        $this->date = Carbon::now()->format('Y-m-d\TH:i');

        // 4. Cerrar modal y avisar al calendario para que se refresque
        $this->dispatch('close-modal', 'log-weight'); // Cierra este modal
        $this->dispatch('refresh-calendar'); // ¡Ojo! Necesitaremos añadir esto al Calendario luego
    }

    public function render()
    {
        return view('livewire.dashboard.weight-log');
    }
}
