<x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
        📜 Historial Completo
    </h2>
</x-slot>

<div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">

                {{-- BARRA DE HERRAMIENTAS (BUSCADOR Y FILTRO) --}}
                <div class="flex flex-col justify-between gap-4 mb-6 sm:flex-row">

                    {{-- Buscador --}}
                    <div class="relative w-full sm:w-1/2">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="text-gray-400 fa-solid fa-search"></i>
                        </div>
                        <input
                            wire:model.live.debounce.300ms="search"
                            type="text"
                            class="block w-full pl-10 border-gray-300 rounded-md shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Buscar citas, ejercicios..."
                        >
                    </div>

                    {{-- Filtro por Tipo --}}
                    <div class="w-full sm:w-1/4">
                        <select
                            wire:model.live="type"
                            class="block w-full border-gray-300 rounded-md shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="">Todos los registros</option>
                            <option value="appointment">🩺 Citas Médicas</option>
                            <option value="exercise">🏃 Ejercicios</option>
                            <option value="weight">⚖️ Peso</option>
                            <option value="heart">❤️ Corazón</option>
                        </select>
                    </div>
                    {{-- Botón Exportar PDF --}}
                    <div class="w-full sm:w-auto">
                        <a href="{{ route('export.history') }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 h-[42px]">
                            <i class="mr-2 fa-solid fa-file-pdf"></i> Exportar PDF
                        </a>
                    </div>
                </div>

                @if($records->isEmpty())
                    <p class="py-10 text-center text-gray-500">No hay registros todavía.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-left">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="px-6 py-3">Tipo</th>
                                    <th class="px-6 py-3">Fecha</th>
                                    <th class="px-6 py-3">Detalle Principal</th>
                                    <th class="px-6 py-3">Notas / Adjuntos</th>
                                    <th class="px-6 py-3 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($records as $record)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">

                                        {{-- 1. COLUMNA TIPO --}}
                                        <td class="px-6 py-4">
                                            @if(class_basename($record) === 'MedicalAppointment')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i class="mr-1 fa-solid fa-user-doctor"></i> Cita
                                                </span>
                                            @elseif(class_basename($record) === 'ActivityExercise')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                    <i class="mr-1 fa-solid fa-person-running"></i> Ejercicio
                                                </span>
                                            @elseif(class_basename($record) === 'MeasurementWeight')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    <i class="mr-1 fa-solid fa-scale-balanced"></i> Peso
                                                </span>
                                            @elseif(class_basename($record) === 'MeasurementHeart')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    <i class="mr-1 fa-solid fa-heart-pulse"></i> Corazón
                                                </span>
                                            @endif
                                        </td>

                                        {{-- 2. COLUMNA FECHA --}}
                                        <td class="px-6 py-4 font-medium">
                                            {{ $record->date->format('d/m/Y H:i') }}
                                        </td>

                                        {{-- 3. COLUMNA DETALLE (Varía según modelo) --}}
                                        <td class="px-6 py-4">
                                            @if(class_basename($record) === 'MedicalAppointment')
                                                <span class="font-bold">{{ $record->title }}</span>
                                            @elseif(class_basename($record) === 'ActivityExercise')
                                                <span class="font-bold">{{ $record->title }}</span> ({{ $record->duration_minutes }} min)
                                            @elseif(class_basename($record) === 'MeasurementWeight')
                                                <span class="text-lg font-bold">{{ $record->weight }} kg</span>
                                            @elseif(class_basename($record) === 'MeasurementHeart')
                                                {{ $record->systolic }}/{{ $record->diastolic }} mmHg <br>
                                                <span class="text-xs text-gray-500">{{ $record->bpm }} bpm</span>
                                            @endif
                                        </td>

                                        {{-- 4. COLUMNA NOTAS/ADJUNTOS --}}
                                        <td class="max-w-xs px-6 py-4 text-gray-500 truncate">
                                            {{-- Mostrar notas si existen --}}
                                            @if(!empty($record->description))
                                                <span title="{{ $record->description }}">{{ Str::limit($record->description, 30) }}</span>
                                            @endif

                                            {{-- Mostrar icono si tiene adjuntos (solo Citas y Ejercicios tienen la relación) --}}
                                            @if(method_exists($record, 'attachments') && $record->attachments->isNotEmpty())
                                                <span class="ml-2 text-indigo-500" title="Tiene archivos adjuntos">
                                                    <i class="fa-solid fa-paperclip"></i> {{ $record->attachments->count() }}
                                                </span>
                                            @endif
                                        </td>
                                        {{-- NUEVA COLUMNA DE ACCIONES --}}
                                        <td class="px-6 py-4 text-right whitespace-nowrap">

                                            {{-- BOTÓN ELIMINAR --}}
                                            <button
                                                wire:click="deleteRecord({{ $record->id }}, '{{ class_basename($record) }}')"
                                                wire:confirm="¿Estás seguro de borrar este registro? Si tiene archivos adjuntos, se eliminarán permanentemente."
                                                class="p-2 text-red-500 transition rounded hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20"
                                                title="Eliminar registro"
                                            >
                                                <i class="fa-solid fa-trash"></i>
                                            </button>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
