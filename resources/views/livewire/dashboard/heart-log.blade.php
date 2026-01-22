<div class="p-6">
    {{-- Título Dinámico --}}
    <h2 class="mb-4 text-lg font-medium text-gray-900 dark:text-gray-100">
        @if($heartId)
            ✏️ Editar Datos del Corazón
        @else
            ❤️ Registrar Datos del Corazón
        @endif
    </h2>

    <form wire:submit="save">

        <div class="grid grid-cols-2 gap-4 mb-4">
            {{-- Sistólica --}}
            <div>
                <x-input-label for="systolic" value="Sistólica (Alta)" />
                <x-text-input
                    id="systolic"
                    wire:model="systolic"
                    type="number"
                    class="block w-full mt-1"
                    placeholder="120"
                    autofocus
                />
                <x-input-error :messages="$errors->get('systolic')" class="mt-2" />
            </div>

            {{-- Diastólica --}}
            <div>
                <x-input-label for="diastolic" value="Diastólica (Baja)" />
                <x-text-input
                    id="diastolic"
                    wire:model="diastolic"
                    type="number"
                    class="block w-full mt-1"
                    placeholder="80"
                />
                <x-input-error :messages="$errors->get('diastolic')" class="mt-2" />
            </div>
        </div>

        {{-- Pulsaciones --}}
        <div class="mb-4">
            <x-input-label for="bpm" value="Pulsaciones (BPM)" />
            <x-text-input
                id="bpm"
                wire:model="bpm"
                type="number"
                class="block w-full mt-1"
                placeholder="70"
            />
            <x-input-error :messages="$errors->get('bpm')" class="mt-2" />
        </div>

        {{-- Fecha --}}
        <div class="mb-6">
            <x-input-label for="date_heart" value="Fecha y Hora" />
            <x-text-input
                id="date_heart"
                wire:model="date"
                type="datetime-local"
                class="block w-full mt-1"
            />
            <x-input-error :messages="$errors->get('date')" class="mt-2" />
        </div>

        {{-- Botones --}}
        <div class="flex justify-end gap-2">
            <button
                type="button"
                x-on:click="$dispatch('close-modal', 'log-heart')"
                class="px-4 py-2 text-sm text-gray-800 transition bg-gray-200 rounded-md hover:bg-gray-300"
            >
                Cancelar
            </button>

            <x-primary-button>
                @if($heartId) Guardar Cambios @else Registrar @endif
            </x-primary-button>
        </div>
    </form>
</div>
