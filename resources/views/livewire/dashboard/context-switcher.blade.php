<div class="relative flex items-center">
    <x-dropdown align="right" width="48">

        {{-- TRIGGER: Lo que se ve siempre --}}
        <x-slot name="trigger">
            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md transition ease-in-out duration-150
                {{ $currentUser->id !== auth()->id() ? 'text-white bg-indigo-600 hover:bg-indigo-700' : 'text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300' }}
            ">
                <div class="flex items-center gap-2">
                    @if($currentUser->id !== auth()->id())
                        <i class="fa-solid fa-eye"></i> {{-- Icono de "Ojo" si estoy espiando --}}
                    @endif

                    {{-- Usamos el username si existe, sino el nombre --}}
                    <span>{{ $currentUser->username ?? $currentUser->name }}</span>
                </div>

                <div class="ms-1">
                    <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </div>
            </button>
        </x-slot>

        {{-- CONTENIDO DEL MENÚ --}}
        <x-slot name="content">
            {{-- Opción 1: Yo mismo --}}
            <div class="px-4 py-2 text-xs text-gray-400">
                Ver datos de:
            </div>

            <x-dropdown-link wire:click="switchTo({{ auth()->id() }})" class="cursor-pointer border-l-4 {{ $currentUser->id === auth()->id() ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-transparent' }}">
                <div class="font-bold">Mi Perfil</div>
                <div class="text-xs text-gray-500">Gestión completa</div>
            </x-dropdown-link>

            {{-- Separador si hay otros usuarios --}}
            @if($accessibleUsers->isNotEmpty())
                <div class="my-1 border-t border-gray-100 dark:border-gray-600"></div>

                @foreach($accessibleUsers as $user)
                    <x-dropdown-link wire:click="switchTo({{ $user->id }})" class="cursor-pointer border-l-4 {{ $currentUser->id === $user->id ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-transparent' }}">
                        <div class="font-bold">{{ $user->username ?? $user->name }}</div>
                        <div class="text-xs text-gray-500">Solo lectura</div>
                    </x-dropdown-link>
                @endforeach
            @endif

            @if($accessibleUsers->isEmpty())
                <div class="px-4 py-2 text-xs italic text-gray-400">
                    Nadie ha compartido datos contigo aún.
                </div>
            @endif
        </x-slot>
    </x-dropdown>
</div>
