<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\WithFileUploads; // 1. Trait
use App\Models\ActivityExercise;
use Carbon\Carbon;

class ActivityLog extends Component
{
    use WithFileUploads; // 2. Usar Trait

    public $title;
    public $duration_minutes;
    public $date;
    public $description;

    // 3. Variables para archivos
    public $files = [];
    public $uploads = [];

    public function mount()
    {
        $this->date = Carbon::now()->format('Y-m-d\TH:i');
    }

    // 4. Hook para acumular archivos
    public function updatedUploads()
    {
        $this->files = array_merge($this->files, $this->uploads);
        $this->uploads = [];
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:1|max:600',
            'date' => 'required|date',
            'description' => 'nullable|string|max:1000',
            'files.*' => 'nullable|file|max:10240',
        ]);

        // Crear la Actividad
        $activity = ActivityExercise::create([
            'user_id' => auth()->id(),
            'title' => $this->title,
            'duration_minutes' => $this->duration_minutes,
            'date' => $this->date,
            'description' => $this->description,
        ]);

        // 5. Guardar Archivos
        foreach ($this->files as $file) {
            $path = $file->store('attachments/exercises', 'public'); // Carpeta distinta para organizar

            $activity->attachments()->create([
                'user_id' => auth()->id(),
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
            ]);
        }

        $this->reset(['title', 'duration_minutes', 'description', 'files', 'uploads']);
        $this->date = Carbon::now()->format('Y-m-d\TH:i');

        $this->dispatch('close-modal', 'log-activity');
        $this->dispatch('refresh-calendar');
    }

    public function removeFile($index)
    {
        array_splice($this->files, $index, 1);
    }

    public function render()
    {
        return view('livewire.dashboard.activity-log');
    }
}
