<?php

namespace App\Livewire\Dashboard;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\MeasurementHeart;
use App\Models\MeasurementWeight;
use App\Models\ActivityExercise;
use App\Models\MedicalAppointment;

class Calendar extends Component
{
    public $currentDate;

    public function mount()
    {
        $this->currentDate = Carbon::now();
    }

    public function nextMonth()
    {
        $this->currentDate->addMonth();
    }

    public function prevMonth()
    {
        $this->currentDate->subMonth();
    }

    public function goToCurrentMonth()
    {
        $this->currentDate = Carbon::now();
    }

    public function render()
    {
        // 1. Definir rango del calendario visual (incluyendo días grises)
        $startOfMonth = $this->currentDate->copy()->startOfMonth();
        $endOfMonth = $this->currentDate->copy()->endOfMonth();

        $startOfCalendar = $startOfMonth->copy()->startOfWeek(Carbon::MONDAY);
        $endOfCalendar = $endOfMonth->copy()->endOfWeek(Carbon::SUNDAY);

        // 2. Consultar datos optimizados (Agrupados por fecha Y-m-d)
        $userId = auth()->id();
        $dateRange = [$startOfCalendar, $endOfCalendar];

        // Usamos claves de fecha (string) para búsqueda rápida O(1)
        $hearts = MeasurementHeart::where('user_id', $userId)
            ->whereBetween('date', $dateRange)->get()
            ->groupBy(fn($val) => $val->date->format('Y-m-d'));

        $weights = MeasurementWeight::where('user_id', $userId)
            ->whereBetween('date', $dateRange)->get()
            ->groupBy(fn($val) => $val->date->format('Y-m-d'));

        $exercises = ActivityExercise::where('user_id', $userId)
            ->whereBetween('date', $dateRange)->get()
            ->groupBy(fn($val) => $val->date->format('Y-m-d'));

        $appointments = MedicalAppointment::where('user_id', $userId)
            ->whereBetween('date', $dateRange)->get()
            ->groupBy(fn($val) => $val->date->format('Y-m-d'));

        // 3. Construir la cuadrícula
        $days = [];
        $day = $startOfCalendar->copy();

        while ($day <= $endOfCalendar) {
            $dateKey = $day->format('Y-m-d');

            $days[] = [
                'date' => $day->copy(),
                'isCurrentMonth' => $day->month === $this->currentDate->month,
                'isToday' => $day->isToday(),
                // Banderas booleanas: ¿Hay registros este día?
                'hasHeart' => $hearts->has($dateKey),
                'hasWeight' => $weights->has($dateKey),
                'hasExercise' => $exercises->has($dateKey),
                'hasAppointment' => $appointments->has($dateKey),
            ];

            $day->addDay();
        }

        return view('livewire.dashboard.calendar', [
            'days' => $days,
            'monthName' => $this->currentDate->translatedFormat('F Y'), // Ahora saldrá en Español
        ]);
    }
}
