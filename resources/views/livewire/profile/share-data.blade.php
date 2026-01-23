<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            🤝 Compartir mis Datos
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Busca a otros usuarios por su <strong>Nick</strong>. Su correo permanecerá privado.
        </p>
    </header>

    {{-- Formulario --}}
    <form wire:submit.prevent="addViewer" class="relative mt-6 space-y-4">
        <div>
            <x-input-label for="search_user" value="Buscar por Nick" />

            <div class="relative flex gap-2 mt-1">
                <div class="relative w-full">
                    {{-- Input Buscador --}}
                    <div class="flex">
                        <span class="inline-flex items-center px-3 text-sm text-gray-500 border border-r-0 border-gray-300 rounded-l-md bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
                            @
                        </span>
                        <x-text-input
                            wire:model.live.debounce.300ms="search"
                            type="text"
                            class="block w-full rounded-l-none"
                            placeholder="batman88"
                        />
                    </div>

                    {{-- Lista Resultados --}}
                    @if(!empty($search) && !empty($searchResults))
                        <div class="absolute z-10 w-full overflow-y-auto bg-white border border-gray-200 shadow-lg dark:bg-gray-700 dark:border-gray-600 rounded-b-md max-h-48">
                            <ul>
                                @foreach($searchResults as $result)
                                    <li
                                        wire:click="selectUser('{{ $result->username }}')"
                                        class="px-4 py-2 transition border-b cursor-pointer hover:bg-indigo-50 dark:hover:bg-gray-600 dark:border-gray-600 last:border-0"
                                    >
                                        {{-- SOLO MOSTRAMOS NICK Y NOMBRE, NO EMAIL --}}
                                        <div class="text-sm font-bold text-gray-800 dark:text-gray-200">
                                            {{ $result->username }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $result->name }}
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @elseif(!empty($search) && empty($searchResults))
                         <div class="absolute z-10 w-full p-2 text-sm text-center text-gray-500 bg-white border border-gray-200 shadow-lg dark:bg-gray-700 dark:border-gray-600 rounded-b-md">
                            No encontrado.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Confirmación de Selección --}}
            @if($username)
                <div class="flex items-center justify-between p-2 mt-3 border border-indigo-200 rounded bg-indigo-50 dark:bg-indigo-900/20 dark:border-indigo-800">
                    <div class="text-sm text-indigo-800 dark:text-indigo-300">
                        <span class="font-bold">Usuario:</span> {{ $username }}
                    </div>
                    <button type="submit" class="px-3 py-1 text-xs font-bold text-white transition bg-indigo-600 rounded hover:bg-indigo-700">
                        CONFIRMAR
                    </button>
                </div>
            @endif

            @if (session('status'))
                <p class="mt-2 text-sm font-medium text-green-600 dark:text-green-400">
                    <i class="mr-1 fa-solid fa-check"></i> {{ session('status') }}
                </p>
            @endif
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
        </div>
    </form>

    {{-- Lista de Autorizados --}}
    @if($viewers->isNotEmpty())
        <div class="pt-6 mt-8 border-t dark:border-gray-700">
            <h3 class="mb-4 text-sm font-bold text-gray-700 uppercase dark:text-gray-300">Usuarios autorizados:</h3>
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach($viewers as $viewer)
                    <div class="flex items-center justify-between p-4 border rounded-lg bg-gray-50 dark:bg-gray-700/50 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-10 h-10 font-bold text-purple-700 bg-purple-100 rounded-full dark:bg-purple-900 dark:text-purple-300">
                                {{ substr($viewer->username, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ $viewer->username }}
                                </p>
                                <p class="text-xs text-gray-500">{{ $viewer->name }}</p>
                                {{-- EMAIL OCULTO --}}
                            </div>
                        </div>
                        <button
                            wire:click="removeViewer({{ $viewer->id }})"
                            wire:confirm="¿Revocar acceso?"
                            class="px-2 text-gray-400 transition hover:text-red-500"
                        >
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</section>
