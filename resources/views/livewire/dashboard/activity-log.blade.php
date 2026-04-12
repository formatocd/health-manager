<div class="p-6">
    {{-- CORRECCIÓN AQUÍ: Usamos $activityId y cambiamos el texto a "Ejercicio" --}}
    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
        @if($activityId) 
            ✏️ Editar Ejercicio 
        @else 
            🏃 Nuevo Ejercicio 
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

        {{-- Duración --}}
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
            <x-input-label for="desc_act" value="Notas (Opcional)" class="mb-1" />
            <div wire:ignore
                x-data="{
                    content: @entangle('description'),
                    init() {
                        const editor = new toastui.Editor({
                            el: this.$refs.editorBase,
                            initialValue: this.content || '',
                            initialEditType: 'wysiwyg',
                            previewStyle: 'vertical',
                            height: '250px',
                            theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                            events: {
                                change: () => {
                                    this.content = editor.getHTML();
                                }
                            }
                        });

                        const observer = new MutationObserver((mutations) => {
                            mutations.forEach((mutation) => {
                                if (mutation.attributeName === 'class') {
                                    if(document.documentElement.classList.contains('dark')) {
                                        this.$refs.editorBase.querySelector('.toastui-editor-defaultUI').classList.add('toastui-editor-dark');
                                    } else {
                                        this.$refs.editorBase.querySelector('.toastui-editor-defaultUI').classList.remove('toastui-editor-dark');
                                    }
                                }
                            });
                        });
                        observer.observe(document.documentElement, { attributes: true });
                    }
                }"
            >
                <div x-ref="editorBase" class="bg-white dark:bg-gray-900"></div>
            </div>
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
            <x-input-label value="Fotos o Archivos (Opcional)" class="mb-2" />
            
            <div 
                x-on:dragover.prevent="isDropping = true"
                x-on:dragleave.prevent="isDropping = false"
                x-on:drop.prevent="
                    isDropping = false;
                    handleFiles($event.dataTransfer.files);
                "
                class="relative flex flex-col items-center justify-center w-full h-32 transition-colors duration-200 border-2 border-dashed rounded-lg cursor-pointer"
                :class="isDropping ? 'border-orange-500 bg-orange-50 dark:bg-orange-900/20' : 'border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800'"
            >
                <input 
                    type="file" 
                    multiple 
                    x-ref="fileInput"
                    x-on:change="handleFiles($event.target.files)"
                    class="absolute inset-0 z-10 w-full h-full opacity-0 cursor-pointer"
                >

                <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center pointer-events-none">
                    <i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-400 mb-2"></i>
                    <p class="mb-1 text-sm text-gray-500 dark:text-gray-400">
                        <span class="font-semibold">Haz clic</span> o arrastra fotos aquí
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">JPG, PNG (Máx 10MB)</p>
                </div>
            </div>

            <div x-show="isUploading" class="mt-2 text-sm text-orange-500 font-medium" style="display: none;">
                <i class="fa-solid fa-spinner fa-spin mr-1"></i> Procesando imágenes...
            </div>
            <x-input-error :messages="$errors->get('files.*')" class="mt-2" />
            
            <div x-show="uploadError" class="mt-2 text-sm font-medium text-red-600" style="display: none;">
                <i class="mr-1 fa-solid fa-circle-exclamation"></i>
                El archivo excede el límite máximo de {{ ini_get('upload_max_filesize') }}B permitido por el servidor.
            </div>

            @if(!empty($files))
                <ul class="mt-3 space-y-2">
                    @foreach($files as $index => $file)
                        <li wire:key="new-file-{{ $index }}" class="flex items-center justify-between text-sm bg-gray-50 dark:bg-gray-700 p-2 rounded border dark:border-gray-600">
                            <div class="flex items-center truncate">
                                <i class="fa-regular fa-image text-orange-500 mr-2"></i>
                                <span class="text-gray-700 dark:text-gray-300 truncate max-w-[200px]">
                                    {{ $file->getClientOriginalName() }}
                                </span>
                            </div>
                            <button type="button" wire:click="removeNewFile({{ $index }})" class="text-gray-400 hover:text-red-500 transition">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- ARCHIVOS EXISTENTES (SOLO EDICIÓN) --}}
        {{-- CORRECCIÓN AQUÍ TAMBIÉN: Usamos $activityId --}}
        @if($activityId && count($existingAttachments) > 0)
            <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-800 rounded-lg border dark:border-gray-700">
                <h3 class="text-xs font-bold text-gray-500 uppercase mb-2">Archivos Guardados:</h3>
                <ul class="space-y-2">
                    @foreach($existingAttachments as $att)
                        <li wire:key="existing-file-{{ $att->id }}" class="flex items-center justify-between text-sm bg-white dark:bg-gray-900 p-2 rounded border dark:border-gray-700">
                            <div class="flex items-center gap-2 truncate">
                                @if(Str::startsWith($att->mime_type, 'image/'))
                                    <i class="fa-regular fa-image text-indigo-500"></i>
                                @else
                                    <i class="fa-regular fa-file-pdf text-red-500"></i>
                                @endif
                                <span class="text-gray-700 dark:text-gray-300 truncate max-w-[200px]">{{ $att->file_name }}</span>
                            </div>
                            
                            <button 
                                type="button"
                                wire:click="deleteExistingAttachment({{ $att->id }})"
                                wire:confirm="¿Estás seguro? El archivo se borrará permanentemente."
                                class="text-red-500 hover:text-red-700 text-xs font-bold px-2 py-1"
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
                class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md text-sm hover:bg-gray-300 transition"
            >
                Cancelar
            </button>

            <x-primary-button class="ms-3">
                @if($activityId) 
                    {{ __('Guardar Cambios') }} 
                @else 
                    {{ __('Crear Actividad') }} 
                @endif
            </x-primary-button>
        </div>
    </form>
</div>