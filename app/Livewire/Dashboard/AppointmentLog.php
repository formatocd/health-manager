<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\MedicalAppointment;
use App\Models\Attachment;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AppointmentLog extends Component
{
    use WithFileUploads;

    public $appointmentId = null; // NULL = Modo Crear | CON ID = Modo Editar
    public $title;
    public $description;
    public $date;

    // Gestión de Archivos
    public $files = [];             // Nuevos archivos a subir
    public $uploads = [];           // Temporal de Livewire
    public $existingAttachments = []; // Archivos ya guardados en BD

    // Escuchamos el evento desde la tabla de historial
    protected $listeners = ['edit-appointment' => 'loadAppointment'];

    public function mount()
    {
        $this->date = Carbon::now()->format('Y-m-d\TH:i');
    }

    public function updatedUploads()
    {
        $this->files = array_merge($this->files, $this->uploads);
        $this->uploads = [];
    }

    // CARGAR DATOS PARA EDITAR
    public function loadAppointment($id)
    {
        $appointment = MedicalAppointment::where('user_id', auth()->id())->findOrFail($id);

        $this->appointmentId = $appointment->id;
        $this->title = $appointment->title;
        $this->description = $appointment->description;
        $this->date = Carbon::parse($appointment->date)->format('Y-m-d\TH:i');

        // Cargar adjuntos antiguos
        $this->existingAttachments = $appointment->attachments;

        // Resetear archivos nuevos pendientes
        $this->files = [];

        // Abrir el modal
        $this->dispatch('open-modal', 'log-appointment');
    }

    // BORRAR UN ADJUNTO EXISTENTE
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
            'date' => 'required|date',
            'description' => 'nullable|string|max:1000',
            'files.*' => 'nullable|file|max:10240',
        ]);

        if ($this->appointmentId) {
            // --- ACTUALIZAR ---
            $appointment = MedicalAppointment::where('user_id', auth()->id())->findOrFail($this->appointmentId);
            $appointment->update([
                'title' => $this->title,
                'date' => $this->date,
                'description' => $this->description,
            ]);
        } else {
            // --- CREAR ---
            $appointment = MedicalAppointment::create([
                'user_id' => auth()->id(),
                'title' => $this->title,
                'date' => $this->date,
                'description' => $this->description,
            ]);
        }

        // Guardar archivos NUEVOS (los viejos no se tocan)
        foreach ($this->files as $file) {
            $path = $file->store('attachments/appointments', 'local');
            $appointment->attachments()->create([
                'user_id' => auth()->id(),
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
            ]);
        }

        // Reset total
        $this->reset(['title', 'description', 'files', 'uploads', 'appointmentId', 'existingAttachments']);
        $this->date = Carbon::now()->format('Y-m-d\TH:i');

        $this->dispatch('close-modal', 'log-appointment');
        $this->dispatch('refresh-calendar'); // Refresca el calendario
        $this->dispatch('refresh-history');  // Refrescará el historial (si añadimos el listener allí)
    }

    public function render()
    {
        return view('livewire.dashboard.appointment-log');
    }
}
