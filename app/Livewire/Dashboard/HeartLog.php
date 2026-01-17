<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\MeasurementHeart;
use Carbon\Carbon;

class HeartLog extends Component
{
    public $systolic;
    public $diastolic;
    public $bpm;
    public $date;

    public function mount()
    {
        $this->date = Carbon::now()->format('Y-m-d\TH:i');
    }

    public function save()
    {
        $this->validate([
            'systolic' => 'required|integer|min:50|max:250',
            'diastolic' => 'required|integer|min:30|max:150',
            'bpm' => 'required|integer|min:30|max:220',
            'date' => 'required|date',
        ]);

        MeasurementHeart::create([
            'user_id' => auth()->id(),
            'systolic' => $this->systolic,
            'diastolic' => $this->diastolic,
            'bpm' => $this->bpm,
            'date' => $this->date,
        ]);

        // Resetear campos
        $this->reset(['systolic', 'diastolic', 'bpm']);
        $this->date = Carbon::now()->format('Y-m-d\TH:i');

        // Cerrar modal y refrescar calendario
        $this->dispatch('close-modal', 'log-heart');
        $this->dispatch('refresh-calendar');
    }

    public function render()
    {
        return view('livewire.dashboard.heart-log');
    }
}
