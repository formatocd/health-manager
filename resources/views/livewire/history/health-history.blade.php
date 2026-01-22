<x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
        📜 Historial Completo
    </h2>
</x-slot>

<div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">

                {{-- BARRA DE HERRAMIENTAS --}}
                <div class="flex flex-col items-end justify-between gap-4 mb-6 sm:flex-row sm:items-center">

                    {{-- Buscador --}}
                    <div class="relative w-full sm:w-1/3">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="text-gray-400 fa-solid fa-search"></i>
                        </div>
                        <input
                            wire:model.live.debounce.300ms="search"
                            type="text"
                            class="block w-full pl-10 border-gray-300 rounded-md shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Buscar..."
                        >
                    </div>

                    {{-- Filtro Tipo --}}
                    <div class="w-full sm:w-1/4">
                        <select wire:model.live="type" class="block w-full border-gray-300 rounded-md shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Todos los registros</option>
                            <option value="appointment">🩺 Citas Médicas</option>
                            <option value="exercise">🏃 Ejercicios</option>
                            <option value="weight">⚖️ Peso</option>
                            <option value="heart">❤️ Corazón</option>
                        </select>
                    </div>

                    {{-- Selector de Paginación --}}
                    <div class="w-full sm:w-auto">
                        <select wire:model.live="perPage" class="block w-full border-gray-300 rounded-md shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500" title="Resultados por página">
                            <option value="20">20 por pág.</option>
                            <option value="40">40 por pág.</option>
                            <option value="60">60 por pág.</option>
                            <option value="80">80 por pág.</option>
                            <option value="100">100 por pág.</option>
                        </select>
                    </div>

                    {{-- PDF Export --}}
                    <div class="w-full sm:w-auto">
                        <a href="{{ route('export.history') }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white transition h-[42px]">
                            <i class="mr-2 fa-solid fa-file-pdf"></i> PDF
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
                                    @php
                                        // Definimos la variable $editEvent correctamente para usarla en el botón
                                        $editEvent = match(class_basename($record)) {
                                            'MedicalAppointment' => "edit-appointment",
                                            'ActivityExercise' => "edit-activity-item",
                                            'MeasurementWeight' => "edit-weight-item",
                                            'MeasurementHeart' => "edit-heart-item",
                                            default => null
                                        };
                                    @endphp

                                    <tr
                                        wire:key="record-{{ $loop->index }}"
                                        {{-- Clic en la fila abre el VISOR --}}
                                        wire:click="$dispatch('view-record', { id: {{ $record->id }}, type: '{{ class_basename($record) }}' })"
                                        class="transition-colors bg-white border-b cursor-pointer dark:bg-gray-800 dark:border-gray-700 hover:bg-blue-50 dark:hover:bg-gray-700 group"
                                        title="Haz clic para ver detalles y adjuntos"
                                    >

                                        {{-- 1. Tipo --}}
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

                                        {{-- 2. Fecha --}}
                                        <td class="px-6 py-4 font-medium">
                                            {{ $record->date->format('d/m/Y H:i') }}
                                        </td>

                                        {{-- 3. Detalle --}}
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

                                        {{-- 4. Notas/Adjuntos --}}
                                        <td class="max-w-xs px-6 py-4 text-gray-500 truncate">
                                            @if(!empty($record->description))
                                                <span title="{{ $record->description }}">{{ Str::limit($record->description, 30) }}</span>
                                            @endif
                                            @if(method_exists($record, 'attachments') && $record->attachments->isNotEmpty())
                                                <span class="ml-2 text-indigo-500 bg-indigo-50 px-2 py-0.5 rounded text-xs font-bold border border-indigo-200">
                                                    <i class="fa-solid fa-paperclip"></i> {{ $record->attachments->count() }}
                                                </span>
                                            @endif
                                        </td>

                                        {{-- 5. Acciones --}}
                                        <td class="px-6 py-4 text-right whitespace-nowrap">

                                            {{-- BOTÓN EDITAR (Lápiz) --}}
                                            @if($editEvent)
                                                <button
                                                    {{-- .stop evita que se abra el visor al hacer click aquí --}}
                                                    wire:click.stop="$dispatch('{{ $editEvent }}', { id: {{ $record->id }} })"
                                                    class="mr-3 text-gray-400 transition hover:text-blue-600"
                                                    title="Modificar registro"
                                                >
                                                    <i class="fa-solid fa-pen"></i>
                                                </button>
                                            @endif

                                            {{-- BOTÓN ELIMINAR --}}
                                            <button
                                                wire:click.stop="deleteRecord({{ $record->id }}, '{{ class_basename($record) }}')"
                                                wire:confirm="¿Borrar este registro permanentemente?"
                                                class="text-gray-400 transition hover:text-red-600"
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

                    {{-- Enlaces de Paginación --}}
                    <div class="mt-4">
                        {{ $records->links() }}
                    </div>

                @endif
            </div>
        </div>
    </div>

    {{-- MODALES --}}
    <x-modal name="log-appointment" focusable> <livewire:dashboard.appointment-log /> </x-modal>
    <x-modal name="log-activity" focusable> <livewire:dashboard.activity-log /> </x-modal>
    <x-modal name="log-weight" focusable> <livewire:dashboard.weight-log /> </x-modal>
    <x-modal name="log-heart" focusable> <livewire:dashboard.heart-log /> </x-modal>
    <x-modal name="record-details" focusable> <livewire:dashboard.record-details /> </x-modal>

</div>
