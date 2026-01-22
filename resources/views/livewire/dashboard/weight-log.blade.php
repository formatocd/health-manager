<div class="p-6">
    {{-- Título Dinámico --}}
    <h2 class="mb-4 text-lg font-medium text-gray-900 dark:text-gray-100">
        @if($weightId) ✏️ Editar Peso @else ⚖️ Registrar Peso @endif
    </h2>

    <form wire:submit="save">
        {{-- Input de Peso --}}
        <div class="mb-4">
            <x-input-label for="weight" value="Peso (kg)" />
            <x-text-input
                id="weight"
                wire:model="weight"
                type="number"
                step="0.01"
                class="block w-full mt-1"
                placeholder="70.5"
                autofocus
            />
            <x-input-error :messages="$errors->get('weight')" class="mt-2" />
        </div>

        {{-- Input de Fecha --}}
        <div class="mb-6">
            <x-input-label for="date_weight" value="Fecha y Hora" />
            <x-text-input
                id="date_weight"
                wire:model="date"
                type="datetime-local"
                class="block w-full mt-1"
            />
        </div>

        {{-- Botones --}}
        <div class="flex justify-end gap-2">
            <button
                type="button"
                x-on:click="$dispatch('close-modal', 'log-weight')"
                class="px-4 py-2 text-sm text-gray-800 transition bg-gray-200 rounded-md hover:bg-gray-300"
            >
                Cancelar
            </button>

            <x-primary-button>
                @if($weightId) Guardar Cambios @else Registrar @endif
            </x-primary-button>
        </div>
    </form>
</div>
