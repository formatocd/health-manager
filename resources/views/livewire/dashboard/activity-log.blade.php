<div class="p-6">
    <h2 class="mb-4 text-lg font-medium text-gray-900 dark:text-gray-100">
        @if($appointmentId)
            ✏️ Editar Cita Médica
        @else
            🩺 Nueva Cita Médica
        @endif
    </h2>

    <form wire:submit="save">

        {{-- Actividad --}}
        <div class="mb-4">
            <x-input-label for="title_act" value="Tipo de Actividad" />
            <x-text-input
                id="title_act"
                wire:model="title"
                type="text"
                class="block w-full mt-1"
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
                class="block w-full mt-1"
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
                class="block w-full mt-1"
            />
            <x-input-error :messages="$errors->get('date')" class="mt-2" />
        </div>

        {{-- Notas --}}
        <div class="mb-6">
            <x-input-label for="desc_act" value="Notas (Opcional)" />
            <textarea
                id="desc_act"
                wire:model="description"
                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600"
                rows="2"
                placeholder="Sensaciones, series realizadas..."
            ></textarea>
        </div>

        {{-- ZONA DRAG & DROP --}}
        <div class="mb-6">
            <x-input-label value="Fotos o Archivos (Opcional)" class="mb-2" />

            <div
                x-data="{ isDropping: false }"
                x-on:dragover.prevent="isDropping = true"
                x-on:dragleave.prevent="isDropping = false"
                x-on:drop.prevent="
                    isDropping = false;
                    $refs.fileInput.files = $event.dataTransfer.files;
                    $refs.fileInput.dispatchEvent(new Event('change', { bubbles: true }));
                "
                class="relative flex flex-col items-center justify-center w-full h-32 transition-colors duration-200 border-2 border-dashed rounded-lg cursor-pointer"
                :class="isDropping ? 'border-orange-500 bg-orange-50 dark:bg-orange-900/20' : 'border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800'"
            >
                <input
                    type="file"
                    wire:model="uploads"
                    multiple
                    x-ref="fileInput"
                    class="absolute inset-0 z-10 w-full h-full opacity-0 cursor-pointer"
                >

                <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center pointer-events-none">
                    <i class="mb-2 text-3xl text-gray-400 fa-solid fa-cloud-arrow-up"></i>
                    <p class="mb-1 text-sm text-gray-500 dark:text-gray-400">
                        <span class="font-semibold">Haz clic</span> o arrastra fotos aquí
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">JPG, PNG (Máx 10MB)</p>
                </div>
            </div>

            <div wire:loading wire:target="uploads" class="mt-2 text-sm font-medium text-orange-500">
                <i class="mr-1 fa-solid fa-spinner fa-spin"></i> Procesando imágenes...
            </div>
            <x-input-error :messages="$errors->get('files.*')" class="mt-2" />

            @if(!empty($files))
                <ul class="mt-3 space-y-2">
                    @foreach($files as $index => $file)
                        <li class="flex items-center justify-between p-2 text-sm border rounded bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
                            <div class="flex items-center truncate">
                                <i class="mr-2 text-orange-500 fa-regular fa-image"></i>
                                <span class="text-gray-700 dark:text-gray-300 truncate max-w-[200px]">
                                    {{ $file->getClientOriginalName() }}
                                </span>
                            </div>
                            <button type="button" wire:click="removeFile({{ $index }})" class="text-gray-400 transition hover:text-red-500">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- ARCHIVOS EXISTENTES (SOLO EDICIÓN) --}}
        @if($appointmentId && count($existingAttachments) > 0)
            <div class="p-4 mb-4 bg-gray-100 border rounded-lg dark:bg-gray-800 dark:border-gray-700">
                <h3 class="mb-2 text-xs font-bold text-gray-500 uppercase">Archivos Guardados:</h3>
                <ul class="space-y-2">
                    @foreach($existingAttachments as $att)
                        <li class="flex items-center justify-between p-2 text-sm bg-white border rounded dark:bg-gray-900 dark:border-gray-700">
                            <div class="flex items-center gap-2 truncate">
                                @if(Str::startsWith($att->mime_type, 'image/'))
                                    <i class="text-indigo-500 fa-regular fa-image"></i>
                                @else
                                    <i class="text-red-500 fa-regular fa-file-pdf"></i>
                                @endif
                                <span class="text-gray-700 dark:text-gray-300 truncate max-w-[200px]">{{ $att->file_name }}</span>
                            </div>

                            <button
                                type="button"
                                wire:click="deleteExistingAttachment({{ $att->id }})"
                                wire:confirm="¿Estás seguro? El archivo se borrará permanentemente."
                                class="px-2 py-1 text-xs font-bold text-red-500 hover:text-red-700"
                            >
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Botones --}}
        <div class="flex justify-end gap-2">
            <button
                type="button"
                x-on:click="$dispatch('close-modal', 'log-activity')"
                class="px-4 py-2 text-sm text-gray-800 transition bg-gray-200 rounded-md hover:bg-gray-300"
            >
                Cancelar
            </button>

            <x-primary-button class="ms-3">
                @if($appointmentId)
                    {{ __('Guardar Cambios') }}
                @else
                    {{ __('Crear actividad') }}
                @endif
            </x-primary-button>
        </div>
    </form>
</div>
