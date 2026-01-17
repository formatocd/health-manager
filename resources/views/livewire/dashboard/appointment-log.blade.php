<div class="p-6">
    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
        🏥 Nueva Cita Médica
    </h2>

    <form wire:submit="save">

        {{-- Título / Especialidad --}}
        <div class="mb-4">
            <x-input-label for="title" value="Especialidad o Título" />
            <x-text-input
                id="title"
                wire:model="title"
                type="text"
                class="mt-1 block w-full"
                placeholder="Ej: Revisión Anual Dentista"
                autofocus
            />
            <x-input-error :messages="$errors->get('title')" class="mt-2" />
        </div>

        {{-- Fecha --}}
        <div class="mb-4">
            <x-input-label for="date_app" value="Fecha y Hora de la Cita" />
            <x-text-input
                id="date_app"
                wire:model="date"
                type="datetime-local"
                class="mt-1 block w-full"
            />
            <x-input-error :messages="$errors->get('date')" class="mt-2" />
        </div>

        {{-- Descripción --}}
        <div class="mb-6">
            <x-input-label for="description" value="Notas (Opcional)" />
            <textarea
                id="description"
                wire:model="description"
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                rows="3"
                placeholder="Traer radiografías anteriores..."
            ></textarea>
            <x-input-error :messages="$errors->get('description')" class="mt-2" />
        </div>

        {{-- Botones --}}
        <div class="flex justify-end gap-2">
            <button
                type="button"
                x-on:click="$dispatch('close-modal', 'log-appointment')"
                class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition text-sm"
            >
                Cancelar
            </button>

            <button
                type="submit"
                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition text-sm font-medium"
            >
                Guardar Cita
            </button>
        </div>
    </form>
</div>
