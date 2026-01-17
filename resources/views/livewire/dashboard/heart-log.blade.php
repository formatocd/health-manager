<div class="p-6">
    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
        ❤️ Registro de Salud Cardiaca
    </h2>

    <form wire:submit="save">

        {{-- Fila: Presión Arterial --}}
        <div class="grid grid-cols-2 gap-4 mb-4">
            {{-- Sistólica --}}
            <div>
                <x-input-label for="systolic" value="Sistólica (Alta)" />
                <x-text-input
                    id="systolic"
                    wire:model="systolic"
                    type="number"
                    class="mt-1 block w-full"
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
                    class="mt-1 block w-full"
                    placeholder="80"
                />
                <x-input-error :messages="$errors->get('diastolic')" class="mt-2" />
            </div>
        </div>

        {{-- Fila: BPM --}}
        <div class="mb-4">
            <x-input-label for="bpm" value="Pulsaciones (BPM)" />
            <div class="relative">
                <x-text-input
                    id="bpm"
                    wire:model="bpm"
                    type="number"
                    class="mt-1 block w-full pr-10"
                    placeholder="70"
                />
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                    <i class="fa-solid fa-heart-pulse"></i>
                </div>
            </div>
            <x-input-error :messages="$errors->get('bpm')" class="mt-2" />
        </div>

        {{-- Fecha --}}
        <div class="mb-6">
            <x-input-label for="date_heart" value="Fecha y Hora" />
            <x-text-input
                id="date_heart"
                wire:model="date"
                type="datetime-local"
                class="mt-1 block w-full"
            />
            <x-input-error :messages="$errors->get('date')" class="mt-2" />
        </div>

        {{-- Botones --}}
        <div class="flex justify-end gap-2">
            <button
                type="button"
                x-on:click="$dispatch('close-modal', 'log-heart')"
                class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition text-sm"
            >
                Cancelar
            </button>

            <button
                type="submit"
                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition text-sm font-medium"
            >
                Guardar
            </button>
        </div>
    </form>
</div>
