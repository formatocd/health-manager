<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            <i class="mr-2 text-indigo-500 fa-solid fa-calendar-days"></i> {{ __('Calendar') }}
        </h2>
    </x-slot>

    <div class="relative min-h-screen py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            {{-- Calendario --}}
            <livewire:dashboard.calendar />
        </div>

        {{-- HM-20: BOTÓN DE ACCIÓN FLOTANTE (FAB) --}}
        {{-- Usamos AlpineJS (x-data) para controlar el despliegue del menú --}}
        <div x-data="{ open: false }" class="fixed z-50 flex flex-col items-end space-y-3 bottom-8 right-8">

            @php
                $isReadOnly = session('viewing_user_id') && session('viewing_user_id') != auth()->id();
            @endphp
            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-4"
                class="flex flex-col items-end space-y-3"
                style="display: none;"
            >
                @if(!$isReadOnly)
                {{-- 1. Botón Cita Médica --}}
                <button
                    x-on:click="$dispatch('open-modal', 'log-appointment'); open = false"
                    class="flex items-center px-4 py-2 space-x-2 text-gray-700 transition bg-white rounded-full shadow-lg dark:bg-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 group">
                    <span class="mr-2 text-sm font-medium">Nueva Cita</span>
                    <div class="flex items-center justify-center w-8 h-8 text-green-600 transition bg-green-100 rounded-full dark:bg-green-900 dark:text-green-400 group-hover:scale-110">
                        <i class="fa-solid fa-user-doctor"></i>
                    </div>
                </button>

                {{-- 2. Botón Ejercicio --}}
                <button
                    x-on:click="$dispatch('open-modal', 'log-activity'); open = false"
                    class="flex items-center px-4 py-2 space-x-2 text-gray-700 transition bg-white rounded-full shadow-lg dark:bg-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 group">
                    <span class="mr-2 text-sm font-medium">Ejercicio</span>
                    <div class="flex items-center justify-center w-8 h-8 text-orange-600 transition bg-orange-100 rounded-full dark:bg-orange-900 dark:text-orange-400 group-hover:scale-110">
                        <i class="fa-solid fa-person-running"></i>
                    </div>
                </button>

                {{-- 3. Botón Peso --}}
                <button
                    x-on:click="$dispatch('open-modal', 'log-weight'); open = false"
                    class="flex items-center px-4 py-2 space-x-2 text-gray-700 transition bg-white rounded-full shadow-lg dark:bg-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 group">
                    <span class="mr-2 text-sm font-medium">Registrar Peso</span>
                    <div class="flex items-center justify-center w-8 h-8 text-blue-600 transition bg-blue-100 rounded-full dark:bg-blue-900 dark:text-blue-400 group-hover:scale-110">
                        <i class="fa-solid fa-weight-scale"></i>
                    </div>
                </button>

                {{-- 4. Botón Corazón --}}
                <button
                    x-on:click="$dispatch('open-modal', 'log-heart'); open = false"
                    class="flex items-center px-4 py-2 space-x-2 text-gray-700 transition bg-white rounded-full shadow-lg dark:bg-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 group">
                    <span class="mr-2 text-sm font-medium">Presión Arterial</span>
                    <div class="flex items-center justify-center w-8 h-8 text-red-600 transition bg-red-100 rounded-full dark:bg-red-900 dark:text-red-400 group-hover:scale-110">
                        <i class="fa-solid fa-heart"></i>
                    </div>
                </button>
                @else
                    <div class="flex items-center gap-3 p-4 mb-8 text-blue-700 border border-blue-200 rounded-lg bg-blue-50 dark:bg-blue-900/20 dark:text-blue-200 dark:border-blue-800">
                        <i class="text-xl fa-solid fa-eye"></i>
                        <div>
                            <p class="font-bold">Modo Espectador</p>
                            <p class="text-sm">Estás viendo los datos de otro usuario. No puedes crear ni editar registros.</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- BOTÓN PRINCIPAL (+) --}}
            <button
                @click="open = !open"
                class="flex items-center justify-center text-white transition transform bg-indigo-600 rounded-full shadow-xl w-14 h-14 hover:bg-indigo-700 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
                <i class="text-xl transition-transform duration-300 fa-solid fa-plus" :class="{'rotate-45': open}"></i>
            </button>
        </div>
    </div>
    <x-modal name="log-weight" focusable>
        <livewire:dashboard.weight-log />
    </x-modal>
    <x-modal name="log-heart" focusable>
        <livewire:dashboard.heart-log />
    </x-modal>
    <x-modal name="log-appointment" focusable>
        <livewire:dashboard.appointment-log />
    </x-modal>
    <x-modal name="log-activity" focusable>
        <livewire:dashboard.activity-log />
    </x-modal>
</x-app-layout>
