<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\MedicalAppointment;
use Carbon\Carbon;

class AppointmentLog extends Component
{
    use WithFileUploads;

    public $title;
    public $description;
    public $date;

    // VARIABLES SEPARADAS
    public $files = [];   // Aquí se ACUMULAN los archivos
    public $uploads = []; // Aquí entran los NUEVOS (temporal)

    public function mount()
    {
        $this->date = Carbon::now()->format('Y-m-d\TH:i');
    }

    // ✅ HOOK MÁGICO: Se ejecuta automáticamente cuando $uploads cambia
    public function updatedUploads()
    {
        // 1. Fusionamos los nuevos archivos con los que ya teníamos
        $this->files = array_merge($this->files, $this->uploads);

        // 2. Limpiamos el input temporal para dejarlo listo para el siguiente "drop"
        $this->uploads = [];
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'description' => 'nullable|string|max:1000',
            'files.*' => 'nullable|file|max:10240', // Validamos la lista acumulada
        ]);

        $appointment = MedicalAppointment::create([
            'user_id' => auth()->id(),
            'title' => $this->title,
            'date' => $this->date,
            'description' => $this->description,
        ]);

        // Guardamos los archivos de la lista acumulada
        foreach ($this->files as $file) {
            $path = $file->store('attachments/appointments', 'local');

            $appointment->attachments()->create([
                'user_id' => auth()->id(),
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
            ]);
        }

        $this->reset(['title', 'description', 'files', 'uploads']);
        $this->date = Carbon::now()->format('Y-m-d\TH:i');

        $this->dispatch('close-modal', 'log-appointment');
        $this->dispatch('refresh-calendar');
    }

    public function removeFile($index)
    {
        array_splice($this->files, $index, 1);
    }

    public function render()
    {
        return view('livewire.dashboard.appointment-log');
    }
}
