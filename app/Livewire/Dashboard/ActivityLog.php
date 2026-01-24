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

    public $activityId = null;
    public $title;
    public $duration_minutes;
    public $date;
    public $description;

    public $files = [];
    public $uploads = [];
    public $existingAttachments = [];

    protected $listeners = ['edit-activity-item' => 'editActivity'];

    public function mount()
    {
        $this->date = Carbon::now()->format('Y-m-d\TH:i');
    }

    public function updatedUploads()
    {
        $this->files = array_merge($this->files, $this->uploads);
        $this->uploads = [];
    }

    public function editActivity($id)
    {
        $activity = ActivityExercise::find($id);

        if (!$activity) {
             return;
        }

        $this->activityId = $activity->id;
        $this->title = $activity->title;
        $this->duration_minutes = $activity->duration_minutes;
        $this->description = $activity->description;
        $this->date = Carbon::parse($activity->date)->format('Y-m-d\TH:i');
        $this->existingAttachments = $activity->attachments;
        $this->files = [];

        $this->dispatch('open-modal', 'log-activity');
    }

    public function deleteExistingAttachment($attachmentId)
    {
        $attachment = Attachment::where('user_id', auth()->id())->find($attachmentId);

        if ($attachment) {
            if (Storage::disk('local')->exists($attachment->file_path)) {
                Storage::disk('local')->delete($attachment->file_path);
            }
            $attachment->delete();
            $this->existingAttachments = $this->existingAttachments->fresh();
        }
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
            $activity = ActivityExercise::where('user_id', auth()->id())->find($this->activityId);

            if ($activity) {
                $activity->update([
                    'title' => $this->title,
                    'duration_minutes' => $this->duration_minutes,
                    'date' => $this->date,
                    'description' => $this->description,
                ]);
            }
        } else {
            $activity = ActivityExercise::create([
                'user_id' => auth()->id(),
                'title' => $this->title,
                'duration_minutes' => $this->duration_minutes,
                'date' => $this->date,
                'description' => $this->description,
            ]);
        }

        if (isset($activity) && $activity) {
            foreach ($this->files as $file) {
                $path = $file->store('attachments/exercises', 'local');
                $activity->attachments()->create([
                    'user_id' => auth()->id(),
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                ]);
            }
        }

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
