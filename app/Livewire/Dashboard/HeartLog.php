<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\MeasurementHeart;
use Carbon\Carbon;

class HeartLog extends Component
{
    // ✅ ESTA ES LA VARIABLE QUE FALTABA O ESTABA MAL DEFINIDA
    public $heartId = null;

    public $systolic;
    public $diastolic;
    public $bpm;
    public $date;

    // Listener para recibir la orden de editar
    protected $listeners = ['edit-heart-item' => 'editHeart'];

    public function mount()
    {
        $this->date = Carbon::now()->format('Y-m-d\TH:i');
    }

    public function editHeart($id)
    {
        $record = MeasurementHeart::find($id);

        // Seguridad: si no existe o no es mío, no hacemos nada
        if (!$record || $record->user_id !== auth()->id()) {
            return;
        }

        // Cargamos los datos en el formulario
        $this->heartId = $record->id;
        $this->systolic = $record->systolic;
        $this->diastolic = $record->diastolic;
        $this->bpm = $record->bpm;
        $this->date = Carbon::parse($record->date)->format('Y-m-d\TH:i');

        // Abrimos el modal
        $this->dispatch('open-modal', 'log-heart');
    }

    public function save()
    {
        $this->validate([
            'systolic' => 'required|integer|min:50|max:250',
            'diastolic' => 'required|integer|min:30|max:150',
            'bpm' => 'required|integer|min:30|max:220',
            'date' => 'required|date',
        ]);

        $data = [
            'systolic' => $this->systolic,
            'diastolic' => $this->diastolic,
            'bpm' => $this->bpm,
            'date' => $this->date
        ];

        if ($this->heartId) {
            // Actualizar registro existente
            $record = MeasurementHeart::where('user_id', auth()->id())->find($this->heartId);
            if ($record) {
                $record->update($data);
            }
        } else {
            // Crear nuevo registro
            MeasurementHeart::create(array_merge($data, ['user_id' => auth()->id()]));
        }

        // Resetear formulario
        $this->reset(['systolic', 'diastolic', 'bpm', 'heartId']);
        $this->date = Carbon::now()->format('Y-m-d\TH:i');

        // Cerrar modal y refrescar tablas
        $this->dispatch('close-modal', 'log-heart');
        $this->dispatch('refresh-calendar');
        $this->dispatch('refresh-history');
    }

    public function render()
    {
        return view('livewire.dashboard.heart-log');
    }
}
