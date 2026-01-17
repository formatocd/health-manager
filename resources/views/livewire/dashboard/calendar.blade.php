<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">

    {{-- CABECERA --}}
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 capitalize">
            {{-- La fecha saldrá en español gracias al cambio de locale --}}
            {{ $monthName }}
        </h2>

        <div class="flex space-x-2">
            <button wire:click="prevMonth" class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                &lt;
            </button>
            <button wire:click="goToCurrentMonth" class="px-3 py-1 bg-indigo-500 text-white rounded hover:bg-indigo-600 transition text-sm">
                Hoy
            </button>
            <button wire:click="nextMonth" class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                &gt;
            </button>
        </div>
    </div>

    {{-- DÍAS SEMANA (Ahora en Español manual) --}}
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
                class="min-h-[6rem] border rounded-lg p-2 flex flex-col justify-between transition relative
                {{ $day['isCurrentMonth'] ? 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 hover:border-indigo-300 dark:hover:border-indigo-700' : 'bg-gray-50 dark:bg-gray-900 border-gray-100 dark:border-gray-800' }}
                {{ $day['isToday'] ? 'ring-2 ring-indigo-500 ring-offset-2 dark:ring-offset-gray-800' : '' }}"
            >
                {{-- Número Día --}}
                <div class="text-right">
                    <span class="text-sm font-medium {{ $day['isToday'] ? 'text-indigo-600 dark:text-indigo-400 font-bold' : ($day['isCurrentMonth'] ? 'text-gray-700 dark:text-gray-300' : 'text-gray-300 dark:text-gray-600') }}">
                        {{ $day['date']->format('j') }}
                    </span>
                </div>

                {{-- ICONOS --}}
                <div class="flex flex-wrap gap-1 mt-1 content-end">
                    @if($day['hasHeart'])
                        <i class="fa-solid fa-heart text-red-500 text-xs" title="Corazón"></i>
                    @endif
                    @if($day['hasWeight'])
                        <i class="fa-solid fa-weight-scale text-blue-500 text-xs" title="Peso"></i>
                    @endif
                    @if($day['hasExercise'])
                        <i class="fa-solid fa-person-running text-orange-500 text-xs" title="Ejercicio"></i>
                    @endif
                    @if($day['hasAppointment'])
                        <i class="fa-solid fa-user-doctor text-green-500 text-xs" title="Cita Médica"></i>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
