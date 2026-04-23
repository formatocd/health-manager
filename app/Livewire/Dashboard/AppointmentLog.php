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

    public $appointmentId = null;
    public $title;
    public $description;
    public $date;

    public $files = [];
    public $uploads = [];
    public $existingAttachments = [];

    protected $listeners = [
        'edit-appointment' => 'loadAppointment',
        'create-appointment-for-date' => 'createAppointmentForDate'
    ];

    public function createAppointmentForDate($date)
    {
        $this->reset(['title', 'description', 'files', 'uploads', 'appointmentId', 'existingAttachments']);
        $this->date = Carbon::parse($date)->format('Y-m-d') . 'T' . Carbon::now()->format('H:i');
        $this->dispatch('open-modal', 'log-appointment');
    }

    public function mount()
    {
        $this->date = Carbon::now()->format('Y-m-d\TH:i');
    }

    public function updatedUploads()
    {
        $this->files = array_merge($this->files, $this->uploads);
        $this->reset('uploads');
    }

    public function loadAppointment($id)
    {
        $appointment = MedicalAppointment::where('user_id', auth()->id())->findOrFail($id);

        $this->appointmentId = $appointment->id;
        $this->title = $appointment->title;
        $this->description = $appointment->description;
        $this->date = Carbon::parse($appointment->date)->format('Y-m-d\TH:i');

        $this->existingAttachments = $appointment->attachments;

        $this->files = [];

        $this->dispatch('open-modal', 'log-appointment');
    }

    public function deleteExistingAttachment($attachmentId)
    {
        $attachment = Attachment::where('user_id', auth()->id())->findOrFail($attachmentId);

        if (Storage::disk('local')->exists($attachment->file_path)) {
            Storage::disk('local')->delete($attachment->file_path);
        }

        $attachment->delete();

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
            $appointment = MedicalAppointment::where('user_id', auth()->id())->findOrFail($this->appointmentId);
            $appointment->update([
                'title' => $this->title,
                'date' => $this->date,
                'description' => $this->description,
            ]);
        } else {
            $appointment = MedicalAppointment::create([
                'user_id' => auth()->id(),
                'title' => $this->title,
                'date' => $this->date,
                'description' => $this->description,
            ]);
        }

        foreach ($this->files as $file) {
            $path = $file->store('attachments/appointments', 'local');
            $appointment->attachments()->create([
                'user_id' => auth()->id(),
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
            ]);
        }

        $this->reset(['title', 'description', 'files', 'uploads', 'appointmentId', 'existingAttachments']);
        $this->date = Carbon::now()->format('Y-m-d\TH:i');

        $this->dispatch('close-modal', 'log-appointment');
        $this->dispatch('refresh-calendar');
        $this->dispatch('refresh-history');
    }

    public function render()
    {
        return view('livewire.dashboard.appointment-log');
    }
}
