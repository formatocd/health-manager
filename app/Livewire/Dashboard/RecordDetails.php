<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use App\Models\MedicalAppointment;
use App\Models\ActivityExercise;
use App\Models\MeasurementWeight;
use App\Models\MeasurementHeart;
use App\Models\Attachment;

class RecordDetails extends Component
{
    public $record = null;
    public $type = '';
    public $isOpen = false;

    protected $listeners = ['view-record' => 'loadRecord'];

    public function loadRecord($id, $type)
    {
        // 1. Identificar modelo
        $modelClass = match($type) {
            'MedicalAppointment' => MedicalAppointment::class,
            'ActivityExercise' => ActivityExercise::class,
            'MeasurementWeight' => MeasurementWeight::class,
            'MeasurementHeart' => MeasurementHeart::class,
            default => null,
        };

        if (!$modelClass) return;

        // 2. Cargar registro
        $this->record = $modelClass::where('user_id', auth()->id())->find($id);
        $this->type = $type;

        if ($this->record) {
            $this->dispatch('open-modal', 'record-details');
        }
    }

    // Método seguro para descargar archivos privados
    public function downloadFile($attachmentId)
    {
        $attachment = Attachment::where('user_id', auth()->id())->find($attachmentId);

        if ($attachment && Storage::disk('local')->exists($attachment->file_path)) {
            return Storage::disk('local')->download($attachment->file_path, $attachment->file_name);
        }
    }

    public function render()
    {
        return view('livewire.dashboard.record-details');
    }
}
