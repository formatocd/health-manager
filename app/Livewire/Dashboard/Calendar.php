<?php

namespace App\Livewire\Dashboard;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\MeasurementHeart;
use App\Models\MeasurementWeight;
use App\Models\ActivityExercise;
use App\Models\MedicalAppointment;
use Livewire\Attributes\On;

class Calendar extends Component
{
    public $currentDate;
    public $selectedDate = null;
    public $dayDetails = [];

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

    public function selectDay($dateString)
    {
        $this->selectedDate = Carbon::parse($dateString);
        $userId = auth()->id();

        $startOfDay = $this->selectedDate->copy()->startOfDay();
        $endOfDay = $this->selectedDate->copy()->endOfDay();
        $range = [$startOfDay, $endOfDay];

        $this->dayDetails = [
            'hearts' => MeasurementHeart::where('user_id', $userId)->whereBetween('date', $range)->latest('date')->get(),
            'weights' => MeasurementWeight::where('user_id', $userId)->whereBetween('date', $range)->latest('date')->get(),

            // ✅ CAMBIO: Añadimos with('attachments')
            'exercises' => ActivityExercise::with('attachments')->where('user_id', $userId)->whereBetween('date', $range)->latest('date')->get(),
            'appointments' => MedicalAppointment::with('attachments')->where('user_id', $userId)->whereBetween('date', $range)->latest('date')->get(),
        ];

        $this->dispatch('open-modal', 'day-details');
    }

    #[On('refresh-calendar')]
    public function refresh(){}

    public function render()
    {
        $userId = auth()->id();

        // --- 📊 CALCULADORA DE WIDGETS (NUEVO) ---

        // 1. Widget: Próxima Cita
        $nextAppointment = MedicalAppointment::where('user_id', $userId)
            ->where('date', '>=', Carbon::now())
            ->orderBy('date', 'asc')
            ->first();

        // 2. Widget: Peso (Actual y Tendencia)
        $latestWeight = MeasurementWeight::where('user_id', $userId)->latest('date')->first();
        $previousWeight = MeasurementWeight::where('user_id', $userId)->latest('date')->skip(1)->first();

        $weightDiff = 0;
        if ($latestWeight && $previousWeight) {
            $weightDiff = $latestWeight->weight - $previousWeight->weight;
        }

        // 3. Widget: Actividad Semanal (Últimos 7 días)
        $weeklyMinutes = ActivityExercise::where('user_id', $userId)
            ->where('date', '>=', Carbon::now()->subDays(7))
            ->sum('duration_minutes');

        // --- FIN WIDGETS ---


        // ... (Aquí sigue tu lógica existente del calendario: startOfMonth, queries de $hearts, $weights, etc.) ...
        $startOfMonth = $this->currentDate->copy()->startOfMonth();
        $endOfMonth = $this->currentDate->copy()->endOfMonth();
        $startOfCalendar = $startOfMonth->copy()->startOfWeek(Carbon::MONDAY);
        $endOfCalendar = $endOfMonth->copy()->endOfWeek(Carbon::SUNDAY);
        $dateRange = [$startOfCalendar, $endOfCalendar];

        // Consultas del calendario (MANTENER IGUAL QUE ANTES)
        $hearts = MeasurementHeart::where('user_id', $userId)->whereBetween('date', $dateRange)->get()->groupBy(fn($v) => $v->date->format('Y-m-d'));
        $weights = MeasurementWeight::where('user_id', $userId)->whereBetween('date', $dateRange)->get()->groupBy(fn($v) => $v->date->format('Y-m-d'));
        $exercises = ActivityExercise::where('user_id', $userId)->whereBetween('date', $dateRange)->get()->groupBy(fn($v) => $v->date->format('Y-m-d'));
        $appointments = MedicalAppointment::where('user_id', $userId)->whereBetween('date', $dateRange)->get()->groupBy(fn($v) => $v->date->format('Y-m-d'));

        $days = [];
        $day = $startOfCalendar->copy();

        while ($day <= $endOfCalendar) {
            $dateKey = $day->format('Y-m-d');
            $days[] = [
                'date' => $day->copy(),
                'isCurrentMonth' => $day->month === $this->currentDate->month,
                'isToday' => $day->isToday(),
                'hasHeart' => $hearts->has($dateKey),
                'hasWeight' => $weights->has($dateKey),
                'hasExercise' => $exercises->has($dateKey),
                'hasAppointment' => $appointments->has($dateKey),
            ];
            $day->addDay();
        }

        $weeklyActivitiesCount = \App\Models\ActivityExercise::where('user_id', auth()->id())
            ->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        return view('livewire.dashboard.calendar', [
            'days' => $days,
            'monthName' => $this->currentDate->translatedFormat('F Y'),

            // ✅ PASAMOS LOS DATOS DE LOS WIDGETS A LA VISTA
            'nextAppointment' => $nextAppointment,
            'latestWeight' => $latestWeight,
            'weightDiff' => $weightDiff,
            'weeklyMinutes' => $weeklyMinutes,
            'weeklyActivitiesCount' => $weeklyActivitiesCount,
        ]);
    }
}
