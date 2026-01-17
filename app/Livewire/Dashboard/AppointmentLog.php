<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\MedicalAppointment;
use Carbon\Carbon;

class AppointmentLog extends Component
{
    public $title;
    public $description;
    public $date;

    public function mount()
    {
        $this->date = Carbon::now()->format('Y-m-d\TH:i');
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'description' => 'nullable|string|max:1000',
        ]);

        MedicalAppointment::create([
            'user_id' => auth()->id(),
            'title' => $this->title,
            'date' => $this->date,
            'description' => $this->description,
        ]);

        // Reset
        $this->reset(['title', 'description']);
        $this->date = Carbon::now()->format('Y-m-d\TH:i');

        // Cerrar y refrescar
        $this->dispatch('close-modal', 'log-appointment');
        $this->dispatch('refresh-calendar');
    }

    public function render()
    {
        return view('livewire.dashboard.appointment-log');
    }
}
