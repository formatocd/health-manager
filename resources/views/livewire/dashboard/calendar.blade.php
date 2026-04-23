<div>
    <div class="p-6 overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">

        {{-- WIDGETS DE RESUMEN (HM-27) --}}
    <div class="grid grid-cols-1 gap-4 mb-8 md:grid-cols-3">

        {{-- 1. Tarjeta: Próxima Cita --}}
        <div class="p-4 overflow-hidden bg-white border-l-4 border-green-500 shadow-sm dark:bg-gray-800 sm:rounded-lg">
            <div class="flex items-center">
                <div class="p-3 mr-4 text-green-500 bg-green-100 rounded-full dark:bg-green-900/20">
                    <i class="text-xl fa-solid fa-user-doctor"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase dark:text-gray-400">Próxima Cita</p>
                    @if($nextAppointment)
                        <p class="w-40 text-lg font-bold text-gray-800 truncate dark:text-gray-100 sm:w-auto" title="{{ $nextAppointment->title }}">
                            {{ $nextAppointment->title }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ $nextAppointment->date->format('d M, H:i') }}
                            ({{ $nextAppointment->date->diffForHumans() }})
                        </p>
                    @else
                        <p class="text-sm italic text-gray-400">No hay citas pendientes</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- 2. Tarjeta: Último Peso --}}
        <div class="p-4 overflow-hidden bg-white border-l-4 border-blue-500 shadow-sm dark:bg-gray-800 sm:rounded-lg">
            <div class="flex items-center">
                <div class="p-3 mr-4 text-blue-500 bg-blue-100 rounded-full dark:bg-blue-900/20">
                    <i class="text-xl fa-solid fa-scale-balanced"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase dark:text-gray-400">Peso Actual</p>
                    @if($latestWeight)
                        <div class="flex items-end gap-2">
                            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                                {{ $latestWeight->weight }} <span class="text-sm font-normal">kg</span>
                            </p>

                            {{-- Indicador de tendencia (sube o baja) --}}
                            @if($weightDiff > 0)
                                <span class="mb-1 text-xs font-bold text-red-500">
                                    <i class="fa-solid fa-arrow-up"></i> {{ number_format(abs($weightDiff), 1) }}
                                </span>
                            @elseif($weightDiff < 0)
                                <span class="mb-1 text-xs font-bold text-green-500">
                                    <i class="fa-solid fa-arrow-down"></i> {{ number_format(abs($weightDiff), 1) }}
                                </span>
                            @else
                                <span class="mb-1 text-xs text-gray-400">
                                    <i class="fa-solid fa-minus"></i> 0.0
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500">Registrado {{ $latestWeight->date->diffForHumans() }}</p>
                    @else
                        <p class="text-sm italic text-gray-400">Sin registros</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- 3. Tarjeta: Actividad Semanal --}}
        <div class="p-4 overflow-hidden bg-white border-l-4 border-orange-500 shadow-sm dark:bg-gray-800 sm:rounded-lg">
            <div class="flex items-center">
                <div class="p-3 mr-4 text-orange-500 bg-orange-100 rounded-full dark:bg-orange-900/20">
                    <i class="text-xl fa-solid fa-fire"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase dark:text-gray-400">Actividad</p>
                    {{-- CAMBIO: Mostramos el contador de actividades --}}
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                        {{ $weeklyActivitiesCount }}
                    </p>
                    <p class="text-xs text-gray-500">
                        actividades esta semana
                    </p>
                </div>
            </div>
        </div>
    </div>

        {{-- CABECERA --}}
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800 capitalize dark:text-gray-100">
                {{ $monthName }}
            </h2>

            <div class="flex space-x-2">
                <button wire:click="prevMonth" class="px-3 py-1 text-gray-700 transition bg-gray-100 rounded dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 dark:text-gray-300">
                    &lt;
                </button>
                <button wire:click="goToCurrentMonth" class="px-3 py-1 text-sm text-white transition bg-indigo-500 rounded hover:bg-indigo-600">
                    Hoy
                </button>
                <button wire:click="nextMonth" class="px-3 py-1 text-gray-700 transition bg-gray-100 rounded dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 dark:text-gray-300">
                    &gt;
                </button>
            </div>
        </div>

        {{-- DÍAS SEMANA --}}
        <div class="grid grid-cols-7 gap-2 mb-2 text-xs font-bold tracking-wide text-center text-gray-500 uppercase dark:text-gray-400">
            <div>Lun</div>
            <div>Mar</div>
            <div>Mié</div>
            <div>Jue</div>
            <div>Vie</div>
            <div>Sáb</div>
            <div>Dom</div>
        </div>

        {{-- CUADRÍCULA --}}
        <div class="grid grid-cols-7 gap-2">
            @foreach($days as $day)
                <div
                    wire:click="selectDay('{{ $day['date']->format('Y-m-d') }}')"
                    class="min-h-[6rem] border rounded-lg p-2 flex flex-col justify-between transition relative cursor-pointer
                    {{ $day['isCurrentMonth'] ? 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 hover:border-indigo-300 dark:hover:border-indigo-500' : 'bg-gray-50 dark:bg-gray-900 border-gray-100 dark:border-gray-800' }}
                    {{ $day['isToday'] ? 'ring-2 ring-indigo-500 ring-offset-2 dark:ring-offset-gray-800' : '' }}"
                >
                    {{-- NÚMERO DEL DÍA --}}
                    <div class="text-right">
                        <span class="text-sm font-medium {{ $day['isToday'] ? 'text-indigo-600 dark:text-indigo-400 font-bold' : ($day['isCurrentMonth'] ? 'text-gray-700 dark:text-gray-300' : 'text-gray-400 dark:text-gray-600') }}">
                            {{ $day['date']->format('j') }}
                        </span>
                    </div>

                    {{-- ICONOS --}}
                    <div class="flex flex-wrap content-end gap-1 mt-1">
                        @if($day['hasHeart'])
                            <i class="fa-solid fa-heart text-red-500 text-[24px]" title="Corazón"></i>
                        @endif
                        @if($day['hasWeight'])
                            <i class="fa-solid fa-weight-scale text-blue-500 text-[24px]" title="Peso"></i>
                        @endif
                        @if($day['hasExercise'])
                            <i class="fa-solid fa-person-running text-orange-500 text-[24px]" title="Ejercicio"></i>
                        @endif
                        @if($day['hasAppointment'])
                            <i class="fa-solid fa-user-doctor text-green-500 text-[24px]" title="Cita Médica"></i>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- MODAL (HM-19) --}}
    {{-- CORRECCIÓN: Eliminamos :show="$showModal" --}}
    <x-modal name="day-details" focusable>
        <div class="p-6 bg-white dark:bg-gray-800">

            {{-- CONTENIDO --}}
            @php
                $isReadOnly = session('viewing_user_id') && session('viewing_user_id') != auth()->id();
            @endphp
            
            {{-- Cabecera --}}
            <div class="flex items-center justify-between pb-2 mb-4 border-b dark:border-gray-700">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Resumen del {{ $selectedDate ? \Carbon\Carbon::parse($selectedDate)->translatedFormat('d \d\e F') : '' }}
                </h2>
                
                {{-- Botón X --}}
                <button x-on:click="$dispatch('close-modal', 'day-details')" class="text-gray-400 transition hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fa-solid fa-xmark fa-lg"></i>
                </button>
            </div>

            @if($selectedDate)
                <div class="space-y-6">
                    @if(collect($dayDetails)->flatten()->isEmpty())
                        <div class="py-6 text-center text-gray-500 dark:text-gray-400">
                            <i class="mb-2 text-2xl opacity-50 fa-regular fa-calendar"></i>
                            <p>No hay registros para este día.</p>
                        </div>
                    @else
                        {{-- 1. Corazón --}}
                        @if($dayDetails['hearts']->isNotEmpty())
                            <div>
                                <h3 class="flex items-center gap-2 mb-2 text-sm font-bold text-red-500 uppercase">
                                    <i class="fa-solid fa-heart"></i> Corazón
                                </h3>
                                <div class="p-2 space-y-1 rounded bg-red-50 dark:bg-red-900/20">
                                    @foreach($dayDetails['hearts'] as $h)
                                        <div class="flex items-center justify-between text-sm text-gray-700 dark:text-gray-300">
                                            <div>
                                                <span class="mr-2">{{ $h->date->format('H:i') }}</span>
                                                <span class="font-bold">{{ $h->systolic }}/{{ $h->diastolic }} <span class="text-xs font-normal">mmHg</span></span>
                                            </div>
                                            @if(!$isReadOnly)
                                                <button x-on:click="$dispatch('close-modal', 'day-details')" wire:click.stop="$dispatch('edit-heart-item', { id: {{ $h->id }} })" class="text-gray-400 transition hover:text-blue-600" title="Editar">
                                                    <i class="fa-solid fa-pen"></i>
                                                </button>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- 2. Peso --}}
                        @if($dayDetails['weights']->isNotEmpty())
                            <div>
                                <h3 class="flex items-center gap-2 mb-2 text-sm font-bold text-blue-500 uppercase">
                                    <i class="fa-solid fa-weight-scale"></i> Peso
                                </h3>
                                <div class="p-2 text-sm text-gray-700 rounded bg-blue-50 dark:bg-blue-900/20 dark:text-gray-300">
                                    @foreach($dayDetails['weights'] as $w)
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <span class="mr-2">{{ $w->date->format('H:i') }}</span>
                                                <span class="font-bold">{{ $w->weight }} kg</span>
                                            </div>
                                            @if(!$isReadOnly)
                                                <button x-on:click="$dispatch('close-modal', 'day-details')" wire:click.stop="$dispatch('edit-weight-item', { id: {{ $w->id }} })" class="text-gray-400 transition hover:text-blue-600" title="Editar">
                                                    <i class="fa-solid fa-pen"></i>
                                                </button>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- 3. Ejercicio --}}
                        @if($dayDetails['exercises']->isNotEmpty())
                            <div>
                                <h3 class="flex items-center gap-2 mb-2 text-sm font-bold text-orange-500 uppercase">
                                    <i class="fa-solid fa-person-running"></i> Ejercicio
                                </h3>
                                <div class="space-y-2">
                                    @foreach($dayDetails['exercises'] as $e)
                                        <div class="p-3 text-sm text-gray-700 rounded bg-orange-50 dark:bg-orange-900/20 dark:text-gray-300">
                                            <div class="flex items-start justify-between mb-1">
                                                <div class="font-semibold">
                                                    <span>{{ $e->title }}</span>
                                                    <span class="ml-1 text-xs font-normal text-gray-500 dark:text-gray-400">({{ $e->duration_minutes }} min)</span>
                                                </div>
                                                @if(!$isReadOnly)
                                                    <button x-on:click="$dispatch('close-modal', 'day-details')" wire:click.stop="$dispatch('edit-activity-item', { id: {{ $e->id }} })" class="text-gray-400 transition hover:text-blue-600" title="Editar">
                                                        <i class="fa-solid fa-pen"></i>
                                                    </button>
                                                @endif
                                            </div>
                                            @if($e->description)
                                                <div class="mb-2 toastui-editor-contents" style="background: transparent;">{!! $e->description !!}</div>
                                            @endif

                                            {{-- VISUALIZACIÓN DE ADJUNTOS --}}
                                            @if($e->attachments->isNotEmpty())
                                                <div class="flex flex-wrap gap-2 pt-2 mt-2 border-t border-orange-200 dark:border-orange-800/30">
                                                    @foreach($e->attachments as $file)
                                                        <a href="{{ route('attachment.show', $file->id) }}" target="_blank" class="flex items-center gap-1 px-2 py-1 text-xs transition bg-white border border-gray-200 rounded dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 group">
                                                            @if(Str::startsWith($file->mime_type, 'image/'))
                                                                <i class="text-orange-500 transition-transform fa-regular fa-image group-hover:scale-110"></i>
                                                            @else
                                                                <i class="text-red-500 transition-transform fa-regular fa-file-pdf group-hover:scale-110"></i>
                                                            @endif
                                                            <span class="truncate max-w-[100px]">{{ $file->file_name }}</span>
                                                            <i class="fa-solid fa-arrow-up-right-from-square text-[24px] text-gray-400"></i>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- 4. Citas --}}
                        @if($dayDetails['appointments']->isNotEmpty())
                            <div>
                                <h3 class="flex items-center gap-2 mb-2 text-sm font-bold text-green-500 uppercase">
                                    <i class="fa-solid fa-user-doctor"></i> Citas
                                </h3>
                                <div class="space-y-2">
                                    @foreach($dayDetails['appointments'] as $a)
                                        <div class="p-3 text-sm text-gray-700 rounded bg-green-50 dark:bg-green-900/20 dark:text-gray-300">
                                            <div class="flex items-start justify-between mb-1">
                                                <div class="font-semibold">
                                                    <span>{{ $a->title }}</span>
                                                    <span class="ml-1 text-xs font-normal text-gray-500 dark:text-gray-400">{{ $a->date->format('H:i') }}</span>
                                                </div>
                                                @if(!$isReadOnly)
                                                    <button x-on:click="$dispatch('close-modal', 'day-details')" wire:click.stop="$dispatch('edit-appointment', { id: {{ $a->id }} })" class="text-gray-400 transition hover:text-blue-600" title="Editar">
                                                        <i class="fa-solid fa-pen"></i>
                                                    </button>
                                                @endif
                                            </div>
                                            @if($a->description)
                                                <div class="mb-2 toastui-editor-contents" style="background: transparent;">{!! $a->description !!}</div>
                                            @endif

                                            {{-- VISUALIZACIÓN DE ADJUNTOS --}}
                                            @if($a->attachments->isNotEmpty())
                                                <div class="flex flex-wrap gap-2 pt-2 mt-2 border-t border-green-200 dark:border-green-800/30">
                                                    @foreach($a->attachments as $file)
                                                        <a href="{{ route('attachment.show', $file->id) }}" target="_blank" class="flex items-center gap-1 px-2 py-1 text-xs transition bg-white border border-gray-200 rounded dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 group">
                                                            @if(Str::startsWith($file->mime_type, 'image/'))
                                                                <i class="text-green-600 transition-transform fa-regular fa-image group-hover:scale-110"></i>
                                                            @else
                                                                <i class="text-red-500 transition-transform fa-regular fa-file-pdf group-hover:scale-110"></i>
                                                            @endif
                                                            <span class="truncate max-w-[100px]">{{ $file->file_name }}</span>
                                                            <i class="fa-solid fa-arrow-up-right-from-square text-[24px] text-gray-400"></i>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            @endif

            {{-- Botonera Inferior --}}
            <div class="flex items-end justify-between mt-6">
                {{-- Opciones Añadir --}}
                <div x-data="{ addMenuOpen: false }" class="flex items-center gap-3">
                    @if(!$isReadOnly && $selectedDate)
                        {{-- BOTÓN PRINCIPAL (+) --}}
                        <button
                            @click="addMenuOpen = !addMenuOpen"
                            class="flex items-center justify-center w-10 h-10 text-white transition transform bg-indigo-600 rounded-full shadow-lg hover:bg-indigo-700 hover:scale-105 focus:outline-none"
                        >
                            <i class="text-lg transition-transform duration-300 fa-solid fa-plus" :class="{'rotate-45': addMenuOpen}"></i>
                        </button>

                        {{-- Opciones desplegables horizontales --}}
                        <div
                            x-show="addMenuOpen"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 -translate-x-4"
                            x-transition:enter-end="opacity-100 translate-x-0"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 translate-x-0"
                            x-transition:leave-end="opacity-0 -translate-x-4"
                            class="flex items-center gap-2"
                            style="display: none;"
                        >
                            {{-- 1. Cita --}}
                            <button
                                x-on:click="$dispatch('close-modal', 'day-details'); $dispatch('create-appointment-for-date', { date: '{{ $selectedDate }}' }); addMenuOpen = false"
                                class="flex items-center justify-center w-8 h-8 text-green-600 transition bg-green-100 rounded-full shadow hover:bg-green-200 dark:bg-green-900 dark:text-green-400 hover:scale-110" title="Nueva Cita">
                                <i class="fa-solid fa-user-doctor"></i>
                            </button>

                            {{-- 2. Ejercicio --}}
                            <button
                                x-on:click="$dispatch('close-modal', 'day-details'); $dispatch('create-activity-for-date', { date: '{{ $selectedDate }}' }); addMenuOpen = false"
                                class="flex items-center justify-center w-8 h-8 text-orange-600 transition bg-orange-100 rounded-full shadow hover:bg-orange-200 dark:bg-orange-900 dark:text-orange-400 hover:scale-110" title="Ejercicio">
                                <i class="fa-solid fa-person-running"></i>
                            </button>

                            {{-- 3. Peso --}}
                            <button
                                x-on:click="$dispatch('close-modal', 'day-details'); $dispatch('create-weight-for-date', { date: '{{ $selectedDate }}' }); addMenuOpen = false"
                                class="flex items-center justify-center w-8 h-8 text-blue-600 transition bg-blue-100 rounded-full shadow hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-400 hover:scale-110" title="Registrar Peso">
                                <i class="fa-solid fa-weight-scale"></i>
                            </button>

                            {{-- 4. Corazón --}}
                            <button
                                x-on:click="$dispatch('close-modal', 'day-details'); $dispatch('create-heart-for-date', { date: '{{ $selectedDate }}' }); addMenuOpen = false"
                                class="flex items-center justify-center w-8 h-8 text-red-600 transition bg-red-100 rounded-full shadow hover:bg-red-200 dark:bg-red-900 dark:text-red-400 hover:scale-110" title="Presión Arterial">
                                <i class="fa-solid fa-heart"></i>
                            </button>
                        </div>
                    @else
                        <div></div>
                    @endif
                </div>

                {{-- Botón Cerrar --}}
                <button x-on:click="$dispatch('close-modal', 'day-details')" class="px-4 py-2 text-sm font-medium text-gray-800 transition bg-gray-200 rounded hover:bg-gray-300">
                    Cerrar
                </button>
            </div>
        </div>
    </x-modal>
</div>
