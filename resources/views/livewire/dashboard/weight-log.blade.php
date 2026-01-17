<div class="p-6">
    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
        ⚖️ Nuevo Registro de Peso
    </h2>

    <form wire:submit="save">
        {{-- Campo: Peso --}}
        <div class="mb-4">
            <x-input-label for="weight" value="Peso (kg)" />
            <x-text-input
                id="weight"
                wire:model="weight"
                type="number"
                step="0.01"
                class="mt-1 block w-full"
                placeholder="Ej: 75.5"
                autofocus
            />
            <x-input-error :messages="$errors->get('weight')" class="mt-2" />
        </div>

        {{-- Campo: Fecha --}}
        <div class="mb-6">
            <x-input-label for="date" value="Fecha y Hora" />
            <x-text-input
                id="date"
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
                x-on:click="$dispatch('close-modal', 'log-weight')"
                class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition text-sm"
            >
                Cancelar
            </button>

            <button
                type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition text-sm font-medium"
            >
                Guardar Peso
            </button>
        </div>
    </form>
</div>
