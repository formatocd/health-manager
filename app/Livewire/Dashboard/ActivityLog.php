<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\ActivityExercise;
use App\Models\Attachment;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ActivityLog extends Component
{
    use WithFileUploads;

    // Identificador para Edición (Null = Crear)
    public $activityId = null;

    // Campos del formulario
    public $title;
    public $duration_minutes; // Campo exclusivo de Ejercicio
    public $date;
    public $description;

    // Gestión de Archivos
    public $files = [];             // Nuevos archivos a subir
    public $uploads = [];           // Temporal de Livewire
    public $existingAttachments = []; // Archivos ya guardados en BD

    // Listener para recibir la orden de editar desde el Historial
    protected $listeners = ['edit-activity' => 'loadActivity'];

    public function mount()
    {
        $this->date = Carbon::now()->format('Y-m-d\TH:i');
    }

    public function updatedUploads()
    {
        $this->files = array_merge($this->files, $this->uploads);
        $this->uploads = [];
    }

    // --- CARGAR DATOS PARA EDITAR ---
    public function loadActivity($id)
    {
        // Buscamos el ejercicio asegurando que sea del usuario
        $activity = ActivityExercise::where('user_id', auth()->id())->findOrFail($id);

        $this->activityId = $activity->id;
        $this->title = $activity->title;
        $this->duration_minutes = $activity->duration_minutes; // Cargamos la duración
        $this->description = $activity->description;
        $this->date = Carbon::parse($activity->date)->format('Y-m-d\TH:i');

        // Cargar adjuntos antiguos
        $this->existingAttachments = $activity->attachments;

        // Limpiar archivos nuevos anteriores
        $this->files = [];

        // Abrir el modal
        $this->dispatch('open-modal', 'log-activity');
    }

    // --- BORRAR ADJUNTO EXISTENTE ---
    public function deleteExistingAttachment($attachmentId)
    {
        $attachment = Attachment::where('user_id', auth()->id())->findOrFail($attachmentId);

        // 1. Borrar físico
        if (Storage::disk('local')->exists($attachment->file_path)) {
            Storage::disk('local')->delete($attachment->file_path);
        }

        // 2. Borrar lógico
        $attachment->delete();

        // 3. Refrescar la lista visualmente
        $this->existingAttachments = $this->existingAttachments->fresh();
    }

    public function removeNewFile($index)
    {
        array_splice($this->files, $index, 1);
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

        if ($this->activityId) {
            // --- ACTUALIZAR EJERCICIO ---
            $activity = ActivityExercise::where('user_id', auth()->id())->findOrFail($this->activityId);
            $activity->update([
                'title' => $this->title,
                'duration_minutes' => $this->duration_minutes,
                'date' => $this->date,
                'description' => $this->description,
            ]);
        } else {
            // --- CREAR EJERCICIO ---
            $activity = ActivityExercise::create([
                'user_id' => auth()->id(),
                'title' => $this->title,
                'duration_minutes' => $this->duration_minutes,
                'date' => $this->date,
                'description' => $this->description,
            ]);
        }

        // Guardar archivos NUEVOS
        foreach ($this->files as $file) {
            $path = $file->store('attachments/exercises', 'local'); // Nota la carpeta 'exercises'

            $activity->attachments()->create([
                'user_id' => auth()->id(),
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
            ]);
        }

        // Reset total
        $this->reset(['title', 'duration_minutes', 'description', 'files', 'uploads', 'activityId', 'existingAttachments']);
        $this->date = Carbon::now()->format('Y-m-d\TH:i');

        $this->dispatch('close-modal', 'log-activity');
        $this->dispatch('refresh-calendar');
        $this->dispatch('refresh-history');
    }

    public function render()
    {
        return view('livewire.dashboard.activity-log');
    }
}
