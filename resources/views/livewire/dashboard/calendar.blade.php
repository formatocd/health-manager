<div>
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">

        {{-- CABECERA --}}
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 capitalize">
                {{ $monthName }}
            </h2>

            <div class="flex space-x-2">
                <button wire:click="prevMonth" class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition text-gray-700 dark:text-gray-300">
                    &lt;
                </button>
                <button wire:click="goToCurrentMonth" class="px-3 py-1 bg-indigo-500 text-white rounded hover:bg-indigo-600 transition text-sm">
                    Hoy
                </button>
                <button wire:click="nextMonth" class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition text-gray-700 dark:text-gray-300">
                    &gt;
                </button>
            </div>
        </div>

        {{-- DÍAS SEMANA --}}
        <div class="grid grid-cols-7 gap-2 mb-2 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
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
                    <div class="flex flex-wrap gap-1 mt-1 content-end">
                        @if($day['hasHeart'])
                            <i class="fa-solid fa-heart text-red-500 text-[10px]" title="Corazón"></i>
                        @endif
                        @if($day['hasWeight'])
                            <i class="fa-solid fa-weight-scale text-blue-500 text-[10px]" title="Peso"></i>
                        @endif
                        @if($day['hasExercise'])
                            <i class="fa-solid fa-person-running text-orange-500 text-[10px]" title="Ejercicio"></i>
                        @endif
                        @if($day['hasAppointment'])
                            <i class="fa-solid fa-user-doctor text-green-500 text-[10px]" title="Cita Médica"></i>
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

            {{-- Cabecera --}}
            <div class="flex justify-between items-center mb-4 pb-2 border-b dark:border-gray-700">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Resumen del {{ $selectedDate ? \Carbon\Carbon::parse($selectedDate)->translatedFormat('d \d\e F') : '' }}
                </h2>
                {{-- Botón X --}}
                <button x-on:click="$dispatch('close-modal', 'day-details')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                    <i class="fa-solid fa-xmark fa-lg"></i>
                </button>
            </div>

            {{-- CONTENIDO --}}
            @if($selectedDate)
                <div class="space-y-6">
                    @if(collect($dayDetails)->flatten()->isEmpty())
                        <div class="text-center py-6 text-gray-500 dark:text-gray-400">
                            <i class="fa-regular fa-calendar text-2xl mb-2 opacity-50"></i>
                            <p>No hay registros para este día.</p>
                        </div>
                    @else
                        {{-- 1. Corazón --}}
                        @if($dayDetails['hearts']->isNotEmpty())
                            <div>
                                <h3 class="text-sm font-bold text-red-500 uppercase mb-2 flex items-center gap-2">
                                    <i class="fa-solid fa-heart"></i> Corazón
                                </h3>
                                <div class="bg-red-50 dark:bg-red-900/20 rounded p-2 space-y-1">
                                    @foreach($dayDetails['hearts'] as $h)
                                        <div class="flex justify-between text-sm text-gray-700 dark:text-gray-300">
                                            <span>{{ $h->date->format('H:i') }}</span>
                                            <span class="font-bold">{{ $h->systolic }}/{{ $h->diastolic }} <span class="text-xs font-normal">mmHg</span></span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- 2. Peso --}}
                        @if($dayDetails['weights']->isNotEmpty())
                            <div>
                                <h3 class="text-sm font-bold text-blue-500 uppercase mb-2 flex items-center gap-2">
                                    <i class="fa-solid fa-weight-scale"></i> Peso
                                </h3>
                                <div class="bg-blue-50 dark:bg-blue-900/20 rounded p-2 text-sm text-gray-700 dark:text-gray-300">
                                    @foreach($dayDetails['weights'] as $w)
                                        <div class="flex justify-between">
                                            <span>{{ $w->date->format('H:i') }}</span>
                                            <span class="font-bold">{{ $w->weight }} kg</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- 3. Ejercicio --}}
                        @if($dayDetails['exercises']->isNotEmpty())
                            <div>
                                <h3 class="text-sm font-bold text-orange-500 uppercase mb-2 flex items-center gap-2">
                                    <i class="fa-solid fa-person-running"></i> Ejercicio
                                </h3>
                                <div class="space-y-1">
                                    @foreach($dayDetails['exercises'] as $e)
                                        <div class="bg-orange-50 dark:bg-orange-900/20 rounded p-2 text-sm text-gray-700 dark:text-gray-300">
                                            <div class="flex justify-between font-semibold">
                                                <span>{{ $e->title }}</span>
                                                <span>{{ $e->duration_minutes }} min</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- 4. Citas --}}
                        @if($dayDetails['appointments']->isNotEmpty())
                            <div>
                                <h3 class="text-sm font-bold text-green-500 uppercase mb-2 flex items-center gap-2">
                                    <i class="fa-solid fa-user-doctor"></i> Citas
                                </h3>
                                <div class="space-y-1">
                                    @foreach($dayDetails['appointments'] as $a)
                                        <div class="bg-green-50 dark:bg-green-900/20 rounded p-2 text-sm text-gray-700 dark:text-gray-300">
                                            <div class="flex justify-between font-semibold">
                                                <span>{{ $a->title }}</span>
                                                <span>{{ $a->date->format('H:i') }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            @endif

            {{-- Botón Cerrar --}}
            <div class="mt-6 flex justify-end">
                <button x-on:click="$dispatch('close-modal', 'day-details')" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 text-sm font-medium transition">
                    Cerrar
                </button>
            </div>
        </div>
    </x-modal>
</div>
