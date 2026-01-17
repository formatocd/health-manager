<?php

namespace App\Livewire\Dashboard;

use Carbon\Carbon;
use Livewire\Component;

class Calendar extends Component
{
    public $currentDate;

    public function mount()
    {
        // Iniciamos el calendario en la fecha actual
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
        // 1. Calcular el primer y último día del mes seleccionado
        $startOfMonth = $this->currentDate->copy()->startOfMonth();
        $endOfMonth = $this->currentDate->copy()->endOfMonth();

        // 2. Calcular los días de relleno iniciales (días grises del mes anterior)
        // startOfWeek(Carbon::MONDAY) asegura que la semana empiece en Lunes
        $startOfCalendar = $startOfMonth->copy()->startOfWeek(Carbon::MONDAY);

        // 3. Calcular los días de relleno finales (días grises del mes siguiente)
        $endOfCalendar = $endOfMonth->copy()->endOfWeek(Carbon::SUNDAY);

        // 4. Generar la lista de días para el bucle
        $days = [];
        $day = $startOfCalendar->copy();

        while ($day <= $endOfCalendar) {
            $days[] = [
                'date' => $day->copy(),
                'isCurrentMonth' => $day->month === $this->currentDate->month,
                'isToday' => $day->isToday(),
            ];
            $day->addDay();
        }

        return view('livewire.dashboard.calendar', [
            'days' => $days,
            'monthName' => $this->currentDate->translatedFormat('F Y'), // Ej: Enero 2024
        ]);
    }
}
