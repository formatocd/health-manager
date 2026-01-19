<?php

namespace App\Livewire\History;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage; // IMPORTANTE: Para borrar archivos
// Modelos
use App\Models\MedicalAppointment;
use App\Models\ActivityExercise;
use App\Models\MeasurementWeight;
use App\Models\MeasurementHeart;

class HealthHistory extends Component
{
    use WithPagination;

    public $search = '';
    public $type = '';

    // ✅ MÉTODO DE BORRADO UNIVERSAL
    public function deleteRecord($id, $type)
    {
        // 1. Identificar qué modelo estamos intentando borrar
        $modelClass = match($type) {
            'MedicalAppointment' => MedicalAppointment::class,
            'ActivityExercise' => ActivityExercise::class,
            'MeasurementWeight' => MeasurementWeight::class,
            'MeasurementHeart' => MeasurementHeart::class,
            default => null,
        };

        if (!$modelClass) return;

        // 2. Buscar el registro (y asegurar que pertenece al usuario logueado)
        $record = $modelClass::where('id', $id)->where('user_id', auth()->id())->first();

        if ($record) {
            // 3. Limpieza de Adjuntos (Solo si el modelo tiene adjuntos)
            // Comprobamos si existe la relación 'attachments' en este objeto
            if (method_exists($record, 'attachments')) {
                foreach ($record->attachments as $attachment) {
                    // Borrar el archivo físico del disco 'local'
                    if (Storage::disk('local')->exists($attachment->file_path)) {
                        Storage::disk('local')->delete($attachment->file_path);
                    }
                    // Borrar el registro de la tabla attachments
                    $attachment->delete();
                }
            }

            // 4. Borrar el registro principal
            $record->delete();

            // 5. Mensaje de éxito (opcional, Livewire refrescará la tabla solo)
            // session()->flash('message', 'Registro eliminado correctamente.');
        }
    }

    public function render()
    {
        $userId = auth()->id();
        $records = collect();

        // --- LÓGICA DE FILTRADO (Igual que tenías, la resumo aquí) ---

        // Citas
        if (empty($this->type) || $this->type === 'appointment') {
            $query = MedicalAppointment::where('user_id', $userId);
            if (!empty($this->search)) {
                $query->where(function (Builder $q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            }
            $records = $records->concat($query->latest('date')->get());
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
            $records = $records->concat($query->latest('date')->get());
        }

        // Peso (Sin búsqueda de texto)
        if (empty($this->search) && (empty($this->type) || $this->type === 'weight')) {
            $records = $records->concat(MeasurementWeight::where('user_id', $userId)->latest('date')->get());
        }

        // Corazón (Sin búsqueda de texto)
        if (empty($this->search) && (empty($this->type) || $this->type === 'heart')) {
            $records = $records->concat(MeasurementHeart::where('user_id', $userId)->latest('date')->get());
        }

        // Ordenar
        $sortedRecords = $records->sortByDesc('date');

        return view('livewire.history.health-history', [
            'records' => $sortedRecords
        ])->layout('layouts.app');
    }
}
