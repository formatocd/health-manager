<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12 relative min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Calendario --}}
            <livewire:dashboard.calendar />
        </div>

        {{-- HM-20: BOTÓN DE ACCIÓN FLOTANTE (FAB) --}}
        {{-- Usamos AlpineJS (x-data) para controlar el despliegue del menú --}}
        <div x-data="{ open: false }" class="fixed bottom-8 right-8 flex flex-col items-end space-y-3 z-50">

            {{-- MENÚ DE OPCIONES (Se despliega hacia arriba) --}}
            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-4"
                class="flex flex-col space-y-3 items-end"
                style="display: none;"
            >
                {{-- 1. Botón Cita Médica --}}
                <button
                    x-on:click="$dispatch('open-modal', 'log-appointment'); open = false"
                    class="flex items-center space-x-2 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-full shadow-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition group">
                    <span class="text-sm font-medium mr-2">Nueva Cita</span>
                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center text-green-600 dark:text-green-400 group-hover:scale-110 transition">
                        <i class="fa-solid fa-user-doctor"></i>
                    </div>
                </button>

                {{-- 2. Botón Ejercicio --}}
                <button
                    x-on:click="$dispatch('open-modal', 'log-activity'); open = false"
                    class="flex items-center space-x-2 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-full shadow-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition group">
                    <span class="text-sm font-medium mr-2">Ejercicio</span>
                    <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900 rounded-full flex items-center justify-center text-orange-600 dark:text-orange-400 group-hover:scale-110 transition">
                        <i class="fa-solid fa-person-running"></i>
                    </div>
                </button>

                {{-- 3. Botón Peso --}}
                <button
                    x-on:click="$dispatch('open-modal', 'log-weight'); open = false"
                    class="flex items-center space-x-2 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-full shadow-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition group">
                    <span class="text-sm font-medium mr-2">Registrar Peso</span>
                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center text-blue-600 dark:text-blue-400 group-hover:scale-110 transition">
                        <i class="fa-solid fa-weight-scale"></i>
                    </div>
                </button>

                {{-- 4. Botón Corazón --}}
                <button
                    x-on:click="$dispatch('open-modal', 'log-heart'); open = false"
                    class="flex items-center space-x-2 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-full shadow-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition group">
                    <span class="text-sm font-medium mr-2">Presión Arterial</span>
                    <div class="w-8 h-8 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center text-red-600 dark:text-red-400 group-hover:scale-110 transition">
                        <i class="fa-solid fa-heart"></i>
                    </div>
                </button>
            </div>

            {{-- BOTÓN PRINCIPAL (+) --}}
            <button
                @click="open = !open"
                class="w-14 h-14 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full shadow-xl flex items-center justify-center transition transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
                <i class="fa-solid fa-plus text-xl transition-transform duration-300" :class="{'rotate-45': open}"></i>
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
