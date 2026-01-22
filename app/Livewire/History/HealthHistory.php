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

class HealthHistory extends Component
{
    use WithPagination;

    public $search = '';
    public $type = '';

    // HM-35: Variable para controlar items por página
    public $perPage = 20;

    protected $listeners = ['refresh-history' => '$refresh'];

    // Resetear a la página 1 si cambian los filtros
    public function updatedSearch() { $this->resetPage(); }
    public function updatedType() { $this->resetPage(); }
    public function updatedPerPage() { $this->resetPage(); }

    public function deleteRecord($id, $type)
    {
        // ... (Mismo código de borrado que ya tenías, consérvalo) ...
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
        $userId = auth()->id();
        $allRecords = collect();

        // 1. RECUPERAR TODOS LOS DATOS (Igual que antes)
        // Citas
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

        // Ejercicios
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

        // Peso
        if (empty($this->search) && (empty($this->type) || $this->type === 'weight')) {
            $allRecords = $allRecords->concat(MeasurementWeight::where('user_id', $userId)->latest('date')->get());
        }

        // Corazón
        if (empty($this->search) && (empty($this->type) || $this->type === 'heart')) {
            $allRecords = $allRecords->concat(MeasurementHeart::where('user_id', $userId)->latest('date')->get());
        }

        // 2. ORDENAR TODO
        $sortedRecords = $allRecords->sortByDesc('date');

        // 3. PAGINACIÓN MANUAL (HM-35)
        // Extraemos solo los items que tocan en esta página
        $items = $sortedRecords->forPage($this->getPage(), $this->perPage);

        // Creamos el objeto paginador manualmente
        $paginatedRecords = new LengthAwarePaginator(
            $items,
            $sortedRecords->count(), // Total real
            $this->perPage,
            $this->getPage(),
            ['path' => request()->url(), 'query' => request()->query()] // Mantener la URL limpia
        );

        return view('livewire.history.health-history', [
            'records' => $paginatedRecords
        ])->layout('layouts.app');
    }
}
