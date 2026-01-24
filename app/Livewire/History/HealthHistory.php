<?php

namespace App\Livewire\History;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator; // Necesario para paginar colecciones
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use App\Models\MedicalAppointment;
use App\Models\ActivityExercise;
use App\Models\MeasurementWeight;
use App\Models\MeasurementHeart;
use App\Traits\HasContext;

class HealthHistory extends Component
{
    use WithPagination;
    use HasContext;

    public $search = '';
    public $type = '';

    public $perPage = 20;

    protected $listeners = ['refresh-history' => '$refresh'];

    public function updatedSearch() { $this->resetPage(); }
    public function updatedType() { $this->resetPage(); }
    public function updatedPerPage() { $this->resetPage(); }

    public function deleteRecord($id, $type)
    {
        if($this->isReadOnly) {
            return;
        }

        $modelClass = match($type) {
            'MedicalAppointment' => MedicalAppointment::class,
            'ActivityExercise' => ActivityExercise::class,
            'MeasurementWeight' => MeasurementWeight::class,
            'MeasurementHeart' => MeasurementHeart::class,
            default => null,
        };
        if (!$modelClass) return;

        $record = $modelClass::where('id', $id)->where('user_id', auth()->id())->first();

        if ($record) {
            if (method_exists($record, 'attachments')) {
                foreach ($record->attachments as $attachment) {
                    if (Storage::disk('local')->exists($attachment->file_path)) {
                        Storage::disk('local')->delete($attachment->file_path);
                    }
                    $attachment->delete();
                }
            }
            $record->delete();
        }
    }

    public function render()
    {
        $userId = $this->getTargetUserId();
        $allRecords = collect();

        if (empty($this->type) || $this->type === 'appointment') {
            $query = MedicalAppointment::where('user_id', $userId);
            if (!empty($this->search)) {
                $query->where(function (Builder $q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            }
            $allRecords = $allRecords->concat($query->latest('date')->get());
        }

        if (empty($this->type) || $this->type === 'exercise') {
            $query = ActivityExercise::where('user_id', $userId);
            if (!empty($this->search)) {
                $query->where(function (Builder $q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            }
            $allRecords = $allRecords->concat($query->latest('date')->get());
        }

        if (empty($this->search) && (empty($this->type) || $this->type === 'weight')) {
            $allRecords = $allRecords->concat(MeasurementWeight::where('user_id', $userId)->latest('date')->get());
        }

        if (empty($this->search) && (empty($this->type) || $this->type === 'heart')) {
            $allRecords = $allRecords->concat(MeasurementHeart::where('user_id', $userId)->latest('date')->get());
        }

        $sortedRecords = $allRecords->sortByDesc('date');

        $items = $sortedRecords->forPage($this->getPage(), $this->perPage);

        $paginatedRecords = new LengthAwarePaginator(
            $items,
            $sortedRecords->count(),
            $this->perPage,
            $this->getPage(),
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('livewire.history.health-history', [
            'records' => $paginatedRecords
        ])->layout('layouts.app');
    }
}
