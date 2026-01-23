<div>
    {{-- REQUISITO 3: Si nadie ha compartido datos conmigo, ocultamos el selector entero --}}
    @if($accessibleUsers->isNotEmpty())
        <div class="relative flex items-center">
            <x-dropdown align="right" width="48">

                {{-- TRIGGER (Botón Visible) --}}
                <x-slot name="trigger">
                    {{-- REQUISITO 1: Estilo siempre "activo" (Morado/Indigo) --}}
                    <button class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-white transition duration-150 ease-in-out bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700">
                        <div class="flex items-center gap-2">

                            {{-- REQUISITO 2: Iconos Diferentes --}}
                            @if($currentUser->id !== auth()->id())
                                <i class="fa-solid fa-eye"></i> {{-- Icono Ojo (Invitado) --}}
                            @else
                                <i class="fa-solid fa-user"></i> {{-- Icono Usuario (Propio) --}}
                            @endif

                            {{-- REQUISITO 2: Textos Diferentes --}}
                            <span class="font-semibold">
                                @if($currentUser->id === auth()->id())
                                    Mi Perfil
                                @else
                                    Perfil de {{ $currentUser->username ?? $currentUser->name }}
                                @endif
                            </span>
                        </div>

                        <div class="ms-1">
                            <svg class="w-4 h-4 text-indigo-200 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </button>
                </x-slot>

                {{-- CONTENIDO DEL MENÚ DESPLEGABLE --}}
                <x-slot name="content">
                    <div class="px-4 py-2 text-xs text-gray-400">
                        Seleccionar contexto:
                    </div>

                    {{-- Opción: Mi Perfil --}}
                    <x-dropdown-link wire:click="switchTo({{ auth()->id() }})" class="cursor-pointer border-l-4 {{ $currentUser->id === auth()->id() ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-transparent' }}">
                        <div class="font-bold">
                            <i class="mr-1 text-gray-400 fa-solid fa-user"></i> Mi Perfil
                        </div>
                    </x-dropdown-link>

                    <div class="my-1 border-t border-gray-100 dark:border-gray-600"></div>

                    {{-- Opciones: Usuarios que me han invitado --}}
                    @foreach($accessibleUsers as $user)
                        <x-dropdown-link wire:click="switchTo({{ $user->id }})" class="cursor-pointer border-l-4 {{ $currentUser->id === $user->id ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-transparent' }}">
                            <div class="font-bold text-indigo-600 dark:text-indigo-400">
                                {{ $user->username ?? $user->name }}
                            </div>
                            <div class="text-xs text-gray-500">Perfil de invitado</div>
                        </x-dropdown-link>
                    @endforeach
                </x-slot>
            </x-dropdown>
        </div>
    @endif
</div>
