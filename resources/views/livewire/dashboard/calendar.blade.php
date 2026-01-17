<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">

    {{-- CABECERA: Navegación entre meses --}}
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 capitalize">
            {{ $monthName }}
        </h2>

        <div class="flex space-x-2">
            <button wire:click="prevMonth" class="px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded hover:bg-gray-300 dark:hover:bg-gray-600">
                &lt; Anterior
            </button>
            <button wire:click="goToCurrentMonth" class="px-3 py-1 bg-indigo-500 text-white rounded hover:bg-indigo-600">
                Hoy
            </button>
            <button wire:click="nextMonth" class="px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded hover:bg-gray-300 dark:hover:bg-gray-600">
                Siguiente &gt;
            </button>
        </div>
    </div>

    {{-- DÍAS DE LA SEMANA --}}
    <div class="grid grid-cols-7 gap-2 mb-2 text-center text-sm font-semibold text-gray-500 dark:text-gray-400">
        <div>LUN</div>
        <div>MAR</div>
        <div>MIE</div>
        <div>JUE</div>
        <div>VIE</div>
        <div>SÁB</div>
        <div>DOM</div>
    </div>

    {{-- CUADRÍCULA DEL CALENDARIO --}}
    <div class="grid grid-cols-7 gap-2">
        @foreach($days as $day)
            <div
                class="h-24 border rounded-lg p-2 flex flex-col relative transition hover:bg-gray-50 dark:hover:bg-gray-700
                {{ $day['isCurrentMonth'] ? 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700' : 'bg-gray-50 dark:bg-gray-900 border-gray-100 dark:border-gray-800 text-gray-400' }}
                {{ $day['isToday'] ? 'ring-2 ring-indigo-500' : '' }}"
            >
                {{-- Número del día --}}
                <span class="text-sm font-bold {{ $day['isToday'] ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-700 dark:text-gray-300' }}">
                    {{ $day['date']->format('d') }}
                </span>

                {{-- AQUÍ IRÁN LOS ICONOS MÁS ADELANTE --}}
                <div class="flex gap-1 mt-1 flex-wrap">
                    {{-- Placeholder para iconos --}}
                </div>
            </div>
        @endforeach
    </div>
</div>
