<div class="p-6">
    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
        🏃 Nuevo Registro de Actividad
    </h2>

    <form wire:submit="save">

        {{-- Actividad --}}
        <div class="mb-4">
            <x-input-label for="title_act" value="Tipo de Actividad" />
            <x-text-input
                id="title_act"
                wire:model="title"
                type="text"
                class="mt-1 block w-full"
                placeholder="Ej: Gimnasio, Caminata, Yoga..."
                autofocus
            />
            <x-input-error :messages="$errors->get('title')" class="mt-2" />
        </div>

        {{-- Duración (Ahora ocupa todo el ancho) --}}
        <div class="mb-4">
            <x-input-label for="duration" value="Duración (minutos)" />
            <x-text-input
                id="duration"
                wire:model="duration_minutes"
                type="number"
                class="mt-1 block w-full"
                placeholder="60"
            />
            <x-input-error :messages="$errors->get('duration_minutes')" class="mt-2" />
        </div>

        {{-- Fecha --}}
        <div class="mb-4">
            <x-input-label for="date_act" value="Fecha y Hora" />
            <x-text-input
                id="date_act"
                wire:model="date"
                type="datetime-local"
                class="mt-1 block w-full"
            />
            <x-input-error :messages="$errors->get('date')" class="mt-2" />
        </div>

        {{-- Notas --}}
        <div class="mb-6">
            <x-input-label for="desc_act" value="Notas (Opcional)" />
            <textarea
                id="desc_act"
                wire:model="description"
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                rows="2"
                placeholder="Sensaciones, series realizadas..."
            ></textarea>
        </div>

        {{-- Botones --}}
        <div class="flex justify-end gap-2">
            <button
                type="button"
                x-on:click="$dispatch('close-modal', 'log-activity')"
                class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition text-sm"
            >
                Cancelar
            </button>

            <button
                type="submit"
                class="px-4 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600 transition text-sm font-medium"
            >
                Guardar Actividad
            </button>
        </div>
    </form>
</div>
