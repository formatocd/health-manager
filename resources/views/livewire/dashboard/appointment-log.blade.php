<div class="p-6">
    <h2 class="mb-4 text-lg font-medium text-gray-900 dark:text-gray-100">
        @if($appointmentId)
            ✏️ Editar Cita Médica
        @else
            🩺 Nueva Cita Médica
        @endif
    </h2>

    <form wire:submit="save">

        {{-- Título / Especialidad --}}
        <div class="mb-4">
            <x-input-label for="title" value="Especialidad o Título" />
            <x-text-input
                id="title"
                wire:model="title"
                type="text"
                class="block w-full mt-1"
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
                class="block w-full mt-1"
            />
            <x-input-error :messages="$errors->get('date')" class="mt-2" />
        </div>

        {{-- Descripción --}}
        <div class="mb-6">
            <x-input-label for="description" value="Notas (Opcional)" />
            <textarea
                id="description"
                wire:model="description"
                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600"
                rows="3"
                placeholder="Traer radiografías anteriores..."
            ></textarea>
            <x-input-error :messages="$errors->get('description')" class="mt-2" />
        </div>

        {{-- ZONA DRAG & DROP --}}
        <div class="mb-6" x-data="{ 
            isDropping: false, 
            isUploading: false, 
            uploadError: false,
            handleFiles(files) {
                const maxBytes = {{ (int) str_replace('M', '', ini_get('upload_max_filesize')) * 1024 * 1024 }};
                if(Array.from(files).some(f => f.size > maxBytes)) {
                    this.uploadError = true;
                    this.isDropping = false;
                    this.$refs.fileInput.value = '';
                    return;
                }
                this.uploadError = false;
                this.isUploading = true;
                $wire.uploadMultiple('uploads', files, 
                    () => { this.isUploading = false; this.$refs.fileInput.value = ''; },
                    () => { this.isUploading = false; this.uploadError = true; this.$refs.fileInput.value = ''; }
                );
            }
        }">
            <x-input-label value="Adjuntar Archivos (Informes, Recetas...)" class="mb-2" />

            <div
                x-on:dragover.prevent="isDropping = true"
                x-on:dragleave.prevent="isDropping = false"
                x-on:drop.prevent="
                    isDropping = false;
                    handleFiles($event.dataTransfer.files);
                "
                class="relative flex flex-col items-center justify-center w-full h-32 transition-colors duration-200 border-2 border-dashed rounded-lg cursor-pointer"
                :class="isDropping ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800'"
            >
                {{-- Input gestionado manualmente por Alpine --}}
                <input
                    type="file"
                    multiple
                    x-ref="fileInput"
                    x-on:change="handleFiles($event.target.files)"
                    class="absolute inset-0 z-10 w-full h-full opacity-0 cursor-pointer"
                >

                <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center pointer-events-none">
                    <i class="mb-2 text-3xl text-gray-400 fa-solid fa-cloud-arrow-up"></i>
                    <p class="mb-1 text-sm text-gray-500 dark:text-gray-400">
                        <span class="font-semibold">Haz clic</span> o arrastra archivos aquí
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">PDF, JPG, PNG (Máx 10MB)</p>
                </div>
            </div>

            {{-- Mensaje de carga (Alpine) --}}
            <div x-show="isUploading" class="mt-2 text-sm font-medium text-blue-500" style="display: none;">
                <i class="mr-1 fa-solid fa-spinner fa-spin"></i> Procesando archivos...
            </div>
            
            <x-input-error :messages="$errors->get('files.*')" class="mt-2" />
            
            {{-- Mensaje de error de límite (Alpine) --}}
            <div x-show="uploadError" class="mt-2 text-sm font-medium text-red-600" style="display: none;">
                <i class="mr-1 fa-solid fa-circle-exclamation"></i>
                El archivo excede el límite máximo de {{ ini_get('upload_max_filesize') }}B permitido por el servidor (o no tiene un formato válido).
            </div>

            {{-- LISTA DE ARCHIVOS ACUMULADOS (miramos files) --}}
            @if(!empty($files))
                <ul class="mt-3 space-y-2">
                    @foreach($files as $index => $file)
                        <li wire:key="new-file-{{ $index }}" class="flex items-center justify-between p-2 text-sm border rounded bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
                            <div class="flex items-center truncate">
                                @if(Str::startsWith($file->getMimeType(), 'image/'))
                                    <i class="mr-2 text-indigo-500 fa-regular fa-image"></i>
                                @else
                                    <i class="mr-2 text-red-500 fa-regular fa-file-pdf"></i>
                                @endif

                                <span class="text-gray-700 dark:text-gray-300 truncate max-w-[200px]">
                                    {{ $file->getClientOriginalName() }}
                                </span>
                            </div>

                            <button type="button" wire:click="removeNewFile({{ $index }})" class="text-gray-400 transition hover:text-red-500">
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
                        <li wire:key="existing-file-{{ $att->id }}" class="flex items-center justify-between p-2 text-sm bg-white border rounded dark:bg-gray-900 dark:border-gray-700">
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
                x-on:click="$dispatch('close-modal', 'log-appointment')"
                class="px-4 py-2 text-sm text-gray-800 transition bg-gray-200 rounded-md hover:bg-gray-300"
            >
                Cancelar
            </button>

            <x-primary-button class="ms-3">
                @if($appointmentId)
                    {{ __('Guardar Cambios') }}
                @else
                    {{ __('Crear Cita') }}
                @endif
            </x-primary-button>
        </div>
    </form>
</div>
