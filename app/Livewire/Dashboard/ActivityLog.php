<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\ActivityExercise;
use Carbon\Carbon;

class ActivityLog extends Component
{
    public $title;
    public $duration_minutes;
    // Eliminada: public $calories_burned;
    public $date;
    public $description;

    public function mount()
    {
        $this->date = Carbon::now()->format('Y-m-d\TH:i');
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:1|max:600',
            'date' => 'required|date',
            'description' => 'nullable|string|max:1000',
        ]);

        ActivityExercise::create([
            'user_id' => auth()->id(),
            'title' => $this->title,
            'duration_minutes' => $this->duration_minutes,
            'date' => $this->date,
            'description' => $this->description,
        ]);

        $this->reset(['title', 'duration_minutes', 'description']);
        $this->date = Carbon::now()->format('Y-m-d\TH:i');

        $this->dispatch('close-modal', 'log-activity');
        $this->dispatch('refresh-calendar');
    }

    public function render()
    {
        return view('livewire.dashboard.activity-log');
    }
}
