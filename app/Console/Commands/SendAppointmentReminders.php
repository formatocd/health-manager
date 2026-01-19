<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MedicalAppointment;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentReminder;
use Carbon\Carbon;

class SendAppointmentReminders extends Command
{
    // Nombre del comando para ejecutarlo en terminal
    protected $signature = 'app:send-appointment-reminders';
    protected $description = 'Envía correos de recordatorio para citas de mañana';

    public function handle()
    {
        $this->info('🔍 Buscando citas para mañana...');

        // Calcular fecha de mañana
        $tomorrow = Carbon::tomorrow();

        // Buscar citas que coincidan con la fecha (sin importar la hora)
        $appointments = MedicalAppointment::whereDate('date', $tomorrow)
                                          ->with('user') // Cargar usuario para tener su email
                                          ->get();

        if ($appointments->isEmpty()) {
            $this->info('✅ No hay citas programadas para mañana.');
            return;
        }

        $count = 0;
        foreach ($appointments as $appointment) {
            // Enviar email al usuario dueño de la cita
            Mail::to($appointment->user->email)->send(new AppointmentReminder($appointment));

            $this->info("📨 Enviado recordatorio a: {$appointment->user->email} para la cita: {$appointment->title}");
            $count++;
        }

        $this->info("🚀 Proceso terminado. {$count} recordatorios enviados.");
    }
}
