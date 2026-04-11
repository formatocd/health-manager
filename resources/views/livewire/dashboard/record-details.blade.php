<div class="p-6 bg-white dark:bg-gray-800">
    @if($record)
        {{-- CABECERA: Icono y Título --}}
        <div class="flex items-start justify-between pb-4 mb-6 border-b border-gray-100 dark:border-gray-700">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    @if($type === 'MedicalAppointment')
                        <span class="bg-green-100 text-green-800 text-xs font-bold px-2 py-0.5 rounded">CITA</span>
                    @elseif($type === 'ActivityExercise')
                        <span class="bg-orange-100 text-orange-800 text-xs font-bold px-2 py-0.5 rounded">EJERCICIO</span>
                    @elseif($type === 'MeasurementWeight')
                        <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2 py-0.5 rounded">PESO</span>
                    @elseif($type === 'MeasurementHeart')
                        <span class="bg-red-100 text-red-800 text-xs font-bold px-2 py-0.5 rounded">CORAZÓN</span>
                    @endif

                    <span class="text-sm text-gray-400">
                        {{ $record->created_at->format('d/m/Y') }} (Registro)
                    </span>
                </div>

                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                    {{-- Título dinámico según modelo --}}
                    @if($type === 'MeasurementWeight')
                        Peso Registrado
                    @elseif($type === 'MeasurementHeart')
                        Medición Cardíaca
                    @else
                        {{ $record->title }}
                    @endif
                </h2>
            </div>

            {{-- Fecha Grande --}}
            <div class="text-right">
                <div class="text-3xl font-light text-indigo-600 dark:text-indigo-400">
                    {{ $record->date->format('d') }}
                </div>
                <div class="text-sm font-bold text-gray-500 uppercase">
                    {{ $record->date->locale('es')->isoFormat('MMMM YYYY') }}
                </div>
                <div class="text-xs text-gray-400">
                    {{ $record->date->format('H:i') }}
                </div>
            </div>
        </div>

        {{-- CUERPO: Datos Específicos --}}
        <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-2">

            {{-- Datos de PESO --}}
            @if($type === 'MeasurementWeight')
                <div class="p-4 text-center rounded-lg bg-blue-50 dark:bg-blue-900/20">
                    <p class="text-sm font-bold text-blue-600 uppercase dark:text-blue-300">Peso</p>
                    <p class="text-4xl font-bold text-blue-800 dark:text-blue-100">{{ $record->weight }} <span class="text-lg">kg</span></p>
                </div>
            @endif

            {{-- Datos de CORAZÓN --}}
            @if($type === 'MeasurementHeart')
                <div class="p-4 text-center rounded-lg bg-red-50 dark:bg-red-900/20">
                    <p class="text-sm font-bold text-red-600 uppercase dark:text-red-300">Presión Arterial</p>
                    <p class="text-3xl font-bold text-red-800 dark:text-red-100">
                        {{ $record->systolic }} / {{ $record->diastolic }}
                        <span class="text-sm font-normal">mmHg</span>
                    </p>
                </div>
                <div class="p-4 text-center rounded-lg bg-red-50 dark:bg-red-900/20">
                    <p class="text-sm font-bold text-red-600 uppercase dark:text-red-300">Pulso</p>
                    <p class="text-3xl font-bold text-red-800 dark:text-red-100">
                        {{ $record->bpm }} <span class="text-sm font-normal">bpm</span>
                    </p>
                </div>
            @endif

            {{-- Datos de EJERCICIO --}}
            @if($type === 'ActivityExercise')
                <div class="flex items-center justify-center gap-3 p-4 rounded-lg bg-orange-50 dark:bg-orange-900/20">
                    <i class="text-2xl text-orange-500 fa-regular fa-clock"></i>
                    <div class="text-left">
                        <p class="text-xs font-bold text-orange-600 uppercase dark:text-orange-300">Duración</p>
                        <p class="text-2xl font-bold text-orange-800 dark:text-orange-100">{{ $record->duration_minutes }} min</p>
                    </div>
                </div>
            @endif

            {{-- NOTAS (Común a todos si tienen campo description) --}}
            @if(!empty($record->description))
                <div class="col-span-1 p-4 rounded-lg md:col-span-2 bg-gray-50 dark:bg-gray-700/50">
                    <h3 class="mb-2 text-xs font-bold text-gray-500 uppercase">Notas / Observaciones</h3>
                    <div class="text-gray-700 dark:text-gray-300 toastui-editor-contents" style="background: transparent;">{!! $record->description !!}</div>
                </div>
            @endif
        </div>

        {{-- SECCIÓN ADJUNTOS (Solo Citas y Ejercicios) --}}
        @if(method_exists($record, 'attachments') && $record->attachments->count() > 0)
            <div class="pt-4 border-t border-gray-100 dark:border-gray-700">
                <h3 class="mb-3 text-sm font-bold text-gray-900 dark:text-white">
                    <i class="mr-2 text-indigo-500 fa-solid fa-paperclip"></i> Archivos Adjuntos ({{ $record->attachments->count() }})
                </h3>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @foreach($record->attachments as $att)
                        <div class="relative flex items-center p-3 overflow-hidden transition border border-gray-200 rounded bg-gray-50 dark:bg-gray-700 dark:border-gray-600 hover:bg-indigo-50 dark:hover:bg-gray-600 group">

                            {{-- LÓGICA DE PREVISUALIZACIÓN --}}
                            <div class="flex-shrink-0 mr-4">
                                @if(Str::startsWith($att->mime_type, 'image/'))
                                    {{-- SI ES IMAGEN: Usamos la ruta segura que acabamos de crear --}}
                                    <div class="w-16 h-16 overflow-hidden border border-gray-200 rounded">
                                        <img
                                            src="{{ route('private_attachment.show', $att->id) }}"
                                            alt="Adjunto"
                                            class="object-cover w-full h-full transition-transform duration-300 cursor-pointer hover:scale-110"
                                            onclick="window.open(this.src, '_blank')"
                                            title="Click para ver en grande"
                                        >
                                    </div>
                                @else
                                    {{-- SI ES PDF U OTRO: Icono estándar --}}
                                    <div class="flex items-center justify-center w-16 h-16 bg-white border border-gray-200 rounded dark:bg-gray-800 dark:border-gray-500">
                                        <i class="text-3xl text-red-500 fa-regular fa-file-pdf"></i>
                                    </div>
                                @endif
                            </div>

                            {{-- DATOS DEL ARCHIVO --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate dark:text-gray-200" title="{{ $att->file_name }}">
                                    {{ $att->file_name }}
                                </p>
                                <p class="mt-1 text-xs text-gray-500">
                                    {{-- Convertir bytes a KB/MB para que quede bonito --}}
                                    @if(Storage::disk('local')->exists($att->file_path))
                                        {{ round(Storage::disk('local')->size($att->file_path) / 1024, 1) }} KB
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>

                            {{-- BOTÓN DESCARGA (Siempre disponible) --}}
                            <button
                                wire:click="downloadFile({{ $att->id }})"
                                class="p-2 ml-2 text-gray-400 transition bg-white border border-gray-100 rounded-full shadow-sm hover:text-indigo-600 dark:hover:text-white dark:bg-gray-800 dark:border-gray-600"
                                title="Descargar archivo"
                            >
                                <i class="fa-solid fa-cloud-arrow-down"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Footer --}}
        <div class="flex justify-end mt-8">
            <button
                x-on:click="$dispatch('close-modal', 'record-details')"
                class="px-4 py-2 text-white transition bg-gray-800 rounded hover:bg-gray-700"
            >
                Cerrar
            </button>
        </div>
    @endif
</div>
