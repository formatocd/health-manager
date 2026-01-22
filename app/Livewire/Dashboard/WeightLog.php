<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\MeasurementWeight;
use Carbon\Carbon;

class WeightLog extends Component
{
    public $weightId = null; // Para saber si editamos
    public $weight;
    public $date;
    public $description; // Si tienes notas en peso

    // Listener para editar
    protected $listeners = ['edit-weight-item' => 'editWeight'];

    public function mount()
    {
        $this->date = Carbon::now()->format('Y-m-d\TH:i');
    }

    // Cargar datos (Sin adjuntos es muy limpio)
    public function editWeight($id)
    {
        $record = MeasurementWeight::find($id);

        if (!$record || $record->user_id !== auth()->id()) {
            return;
        }

        $this->weightId = $record->id;
        $this->weight = $record->weight;
        // $this->description = $record->description; // Descomenta si usas notas en peso
        $this->date = Carbon::parse($record->date)->format('Y-m-d\TH:i');

        $this->dispatch('open-modal', 'log-weight');
    }

    public function save()
    {
        $this->validate([
            'weight' => 'required|numeric|min:1|max:500',
            'date' => 'required|date',
        ]);

        if ($this->weightId) {
            // Actualizar
            $record = MeasurementWeight::where('user_id', auth()->id())->find($this->weightId);
            if ($record) {
                $record->update([
                    'weight' => $this->weight,
                    'date' => $this->date,
                    // 'description' => $this->description,
                ]);
            }
        } else {
            // Crear
            MeasurementWeight::create([
                'user_id' => auth()->id(),
                'weight' => $this->weight,
                'date' => $this->date,
                // 'description' => $this->description,
            ]);
        }

        // Reset
        $this->reset(['weight', 'weightId']); // Resetear variables
        $this->date = Carbon::now()->format('Y-m-d\TH:i');

        $this->dispatch('close-modal', 'log-weight');
        $this->dispatch('refresh-calendar');
        $this->dispatch('refresh-history');
    }

    public function render()
    {
        return view('livewire.dashboard.weight-log');
    }
}
