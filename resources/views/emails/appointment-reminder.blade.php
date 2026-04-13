<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; color: #333; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 8px; }
        .btn { display: inline-block; background-color: #48bb78; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 20px; }
        .header { font-size: 18px; font-weight: bold; color: #2d3748; margin-bottom: 20px; border-bottom: 2px solid #48bb78; padding-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            📅 Recordatorio de Cita
        </div>

        <p>Hola, <strong>{{ $appointment->user->name }}</strong>.</p>

        <p>Te recordamos que tienes una cita médica programada para mañana:</p>

        <div style="background-color: #f7fafc; padding: 15px; border-left: 4px solid #48bb78; margin: 20px 0;">
            <p style="margin: 0; font-size: 16px;"><strong>{{ $appointment->title }}</strong></p>
            <p style="margin: 5px 0 0 0; color: #718096;">
                ⏰ Hora: {{ $appointment->date->format('H:i') }}
            </p>
        </div>

        @if($appointment->description)
            <div><strong>Notas:</strong> {!! $appointment->description !!}</div>
        @endif

        <p>¡Que tengas un buen día!</p>

        <a href="{{ route('dashboard') }}" class="btn">Ver en el Calendario</a>
    </div>
</body>
</html>
