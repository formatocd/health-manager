<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\MeasurementHeart;
use Carbon\Carbon;

class HeartLog extends Component
{
    public $heartId = null;

    public $systolic;
    public $diastolic;
    public $bpm;
    public $date;

    protected $listeners = ['edit-heart-item' => 'editHeart'];

    public function mount()
    {
        $this->date = Carbon::now()->format('Y-m-d\TH:i');
    }

    public function editHeart($id)
    {
        $record = MeasurementHeart::find($id);

        if (!$record || $record->user_id !== auth()->id()) {
            return;
        }

        $this->heartId = $record->id;
        $this->systolic = $record->systolic;
        $this->diastolic = $record->diastolic;
        $this->bpm = $record->bpm;
        $this->date = Carbon::parse($record->date)->format('Y-m-d\TH:i');

        $this->dispatch('open-modal', 'log-heart');
    }

    public function save()
    {
        $this->validate([
            'systolic' => 'required|integer|min:50|max:250',
            'diastolic' => 'required|integer|min:30|max:150',
            'bpm' => 'required|integer|min:30|max:220',
            'date' => 'required|date',
        ]);

        $data = [
            'systolic' => $this->systolic,
            'diastolic' => $this->diastolic,
            'bpm' => $this->bpm,
            'date' => $this->date
        ];

        if ($this->heartId) {
            $record = MeasurementHeart::where('user_id', auth()->id())->find($this->heartId);
            if ($record) {
                $record->update($data);
            }
        } else {
            MeasurementHeart::create(array_merge($data, ['user_id' => auth()->id()]));
        }

        $this->reset(['systolic', 'diastolic', 'bpm', 'heartId']);
        $this->date = Carbon::now()->format('Y-m-d\TH:i');

        $this->dispatch('close-modal', 'log-heart');
        $this->dispatch('refresh-calendar');
        $this->dispatch('refresh-history');
    }

    public function render()
    {
        return view('livewire.dashboard.heart-log');
    }
}
