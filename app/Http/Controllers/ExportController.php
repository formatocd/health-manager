<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; // Importante: Usar esta fachada
use App\Models\MedicalAppointment;
use App\Models\ActivityExercise;
use App\Models\MeasurementWeight;
use App\Models\MeasurementHeart;

class ExportController extends Controller
{
    public function downloadHistory()
    {
        $userId = auth()->id();

        // 1. Recuperamos TODOS los datos (sin paginar)
        $appointments = MedicalAppointment::where('user_id', $userId)->get();
        $exercises = ActivityExercise::where('user_id', $userId)->get();
        $weights = MeasurementWeight::where('user_id', $userId)->get();
        $hearts = MeasurementHeart::where('user_id', $userId)->get();

        // 2. Mezclamos y ordenamos por fecha descendente
        $records = $appointments->concat($exercises)
                                ->concat($weights)
                                ->concat($hearts)
                                ->sortByDesc('date');

        // 3. Generamos el PDF usando una vista Blade específica
        $pdf = Pdf::loadView('pdf.history-report', [
            'records' => $records,
            'user' => auth()->user(),
            'date' => now()->format('d/m/Y')
        ]);

        // 4. Descargamos el archivo
        return $pdf->download('historial-salud-' . now()->format('Y-m-d') . '.pdf');
    }
}
