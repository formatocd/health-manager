<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\MedicalAppointment; // Importar modelo

class AppointmentReminder extends Mailable
{
    use Queueable, SerializesModels;

    // Variable pública para usar en la vista
    public $appointment;

    public function __construct(MedicalAppointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🔔 Recordatorio: Tienes una cita mañana',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.appointment-reminder',
        );
    }
}
