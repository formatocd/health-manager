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
            'exercises' => ActivityExercise::where('user_id', $userId)->whereBetween('date', $range)->latest('date')->get(),
            'appointments' => MedicalAppointment::where('user_id', $userId)->whereBetween('date', $range)->latest('date')->get(),
        ];

        $this->dispatch('open-modal', 'day-details');
    }

    #[On('refresh-calendar')]
    public function refresh()
    {

    }

    public function render()
    {
        $startOfMonth = $this->currentDate->copy()->startOfMonth();
        $endOfMonth = $this->currentDate->copy()->endOfMonth();
        $startOfCalendar = $startOfMonth->copy()->startOfWeek(Carbon::MONDAY);
        $endOfCalendar = $endOfMonth->copy()->endOfWeek(Carbon::SUNDAY);

        $userId = auth()->id();
        $dateRange = [$startOfCalendar, $endOfCalendar];

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

        return view('livewire.dashboard.calendar', [
            'days' => $days,
            'monthName' => $this->currentDate->translatedFormat('F Y'),
        ]);
    }
}
