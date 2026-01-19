<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Historial de Salud</title>
    <style>
        body { font-family: sans-serif; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
        h1 { color: #2d3748; margin: 0; }
        .meta { font-size: 12px; color: #718096; margin-top: 5px; }

        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th { background-color: #f7fafc; border-bottom: 2px solid #e2e8f0; text-align: left; padding: 8px; font-weight: bold; }
        td { border-bottom: 1px solid #e2e8f0; padding: 8px; vertical-align: top; }

        .badge { padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: bold; color: white; display: inline-block; }
        .bg-cita { background-color: #48bb78; }    /* Verde */
        .bg-ejercicio { background-color: #ed8936; } /* Naranja */
        .bg-peso { background-color: #4299e1; }      /* Azul */
        .bg-corazon { background-color: #f56565; }   /* Rojo */
    </style>
</head>
<body>

    <div class="header">
        <h1>Informe de Salud Personal</h1>
        <div class="meta">
            Generado para: <strong>{{ $user->name }}</strong> | Fecha: {{ $date }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="15%">Fecha</th>
                <th width="15%">Tipo</th>
                <th width="30%">Detalle Principal</th>
                <th width="40%">Notas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
                <tr>
                    <td>{{ $record->date->format('d/m/Y H:i') }}</td>
                    <td>
                        @if(class_basename($record) === 'MedicalAppointment')
                            <span class="badge bg-cita">CITA</span>
                        @elseif(class_basename($record) === 'ActivityExercise')
                            <span class="badge bg-ejercicio">EJERCICIO</span>
                        @elseif(class_basename($record) === 'MeasurementWeight')
                            <span class="badge bg-peso">PESO</span>
                        @elseif(class_basename($record) === 'MeasurementHeart')
                            <span class="badge bg-corazon">CORAZÓN</span>
                        @endif
                    </td>
                    <td>
                        @if(class_basename($record) === 'MedicalAppointment')
                            <strong>{{ $record->title }}</strong>
                        @elseif(class_basename($record) === 'ActivityExercise')
                            <strong>{{ $record->title }}</strong> <br> ({{ $record->duration_minutes }} min)
                        @elseif(class_basename($record) === 'MeasurementWeight')
                            <strong>{{ $record->weight }} kg</strong>
                        @elseif(class_basename($record) === 'MeasurementHeart')
                            {{ $record->systolic }}/{{ $record->diastolic }} mmHg <br>
                            {{ $record->bpm }} bpm
                        @endif
                    </td>
                    <td>
                        {{ $record->description ?? '-' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
