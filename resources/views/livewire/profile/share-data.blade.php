<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            🤝 Compartir mis Datos (Lista Blanca)
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Autoriza a otros usuarios a consultar tu historial y calendario.
            <br><strong>Nota:</strong> Solo podrán VER tus datos, nunca editarlos ni borrarlos.
        </p>
    </header>

    {{-- Formulario --}}
    <form wire:submit="addViewer" class="mt-6 space-y-4">
        <div>
            <x-input-label for="share_email" value="Email del usuario a invitar" />
            <div class="flex gap-2 mt-1">
                <x-text-input
                    id="share_email"
                    wire:model="email"
                    type="email"
                    class="block w-full max-w-md"
                    placeholder="usuario@ejemplo.com"
                />
                <x-primary-button>
                    Conceder Acceso
                </x-primary-button>
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />

            @if (session('status'))
                <p class="mt-2 text-sm font-medium text-green-600 dark:text-green-400">
                    <i class="mr-1 fa-solid fa-check"></i> {{ session('status') }}
                </p>
            @endif
        </div>
    </form>

    {{-- Lista de Accesos --}}
    @if($viewers->isNotEmpty())
        <div class="pt-6 mt-8 border-t dark:border-gray-700">
            <h3 class="mb-4 text-sm font-bold text-gray-700 uppercase dark:text-gray-300">Usuarios autorizados:</h3>

            <div class="grid gap-4 sm:grid-cols-2">
                @foreach($viewers as $viewer)
                    <div class="flex items-center justify-between p-4 border rounded-lg bg-gray-50 dark:bg-gray-700/50 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-10 h-10 font-bold text-indigo-700 bg-indigo-100 rounded-full dark:bg-indigo-900 dark:text-indigo-300">
                                {{ substr($viewer->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $viewer->name }}</p>
                                <p class="text-xs text-gray-500">{{ $viewer->email }}</p>
                            </div>
                        </div>

                        <button
                            wire:click="removeViewer({{ $viewer->id }})"
                            wire:confirm="¿Seguro que quieres quitar el acceso a {{ $viewer->name }}?"
                            class="px-2 text-gray-400 transition hover:text-red-500"
                            title="Revocar acceso"
                        >
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</section>
