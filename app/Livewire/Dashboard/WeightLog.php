<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\MeasurementWeight;
use Carbon\Carbon;

class WeightLog extends Component
{
    public $weightId = null;
    public $weight;
    public $date;
    public $description;

    protected $listeners = [
        'edit-weight-item' => 'editWeight',
        'create-weight-for-date' => 'createWeightForDate'
    ];

    public function createWeightForDate($date)
    {
        $this->reset(['weight', 'weightId']);
        $this->date = Carbon::parse($date)->format('Y-m-d') . 'T' . Carbon::now()->format('H:i');
        $this->dispatch('open-modal', 'log-weight');
    }

    public function mount()
    {
        $this->date = Carbon::now()->format('Y-m-d\TH:i');
    }

    public function editWeight($id)
    {
        $record = MeasurementWeight::find($id);

        if (!$record || $record->user_id !== auth()->id()) {
            return;
        }

        $this->weightId = $record->id;
        $this->weight = $record->weight;
        $this->date = Carbon::parse($record->date)->format('Y-m-d\TH:i');

        $this->dispatch('open-modal', 'log-weight');
    }

    public function save()
    {
        $this->validate([
            'weight' => 'required|numeric|min:1|max:500',
            'date' => 'required|date',
        ]);

        if ($this->weightId) {
            $record = MeasurementWeight::where('user_id', auth()->id())->find($this->weightId);
            if ($record) {
                $record->update([
                    'weight' => $this->weight,
                    'date' => $this->date,
                ]);
            }
        } else {
            MeasurementWeight::create([
                'user_id' => auth()->id(),
                'weight' => $this->weight,
                'date' => $this->date,
            ]);
        }

        $this->reset(['weight', 'weightId']);
        $this->date = Carbon::now()->format('Y-m-d\TH:i');

        $this->dispatch('close-modal', 'log-weight');
        $this->dispatch('refresh-calendar');
        $this->dispatch('refresh-history');
    }

    public function render()
    {
        return view('livewire.dashboard.weight-log');
    }
}
