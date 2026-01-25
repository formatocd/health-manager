<x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
        👥 Gestión de Usuarios
    </h2>
</x-slot>

<div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

        {{-- Mensajes Flash --}}
        @if (session('status'))
            <div class="relative px-4 py-3 mb-4 text-green-700 bg-green-100 border border-green-400 rounded">
                {{ session('status') }}
            </div>
        @endif
        @if (session('error'))
            <div class="relative px-4 py-3 mb-4 text-red-700 bg-red-100 border border-red-400 rounded">
                {{ session('error') }}
            </div>
        @endif

        <div class="p-6 overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg text-gray-900 dark:text-gray-100">Usuarios del Sistema</h3>
                <x-primary-button x-on:click="$dispatch('open-modal', 'create-user')">
                    <i class="mr-2 fa-solid fa-plus"></i> Nuevo Usuario
                </x-primary-button>
            </div>

            <table class="min-w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="px-6 py-3">Nombre</th>
                        <th class="px-6 py-3">Email</th>
                        <th class="px-6 py-3">Rol</th>
                        <th class="px-6 py-3">Registro</th>
                        <th class="px-6 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($users as $user)
                        <tr>
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $user->name }}</td>
                            <td class="px-6 py-4">{{ $user->email }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">{{ $user->created_at->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                @if($user->id !== auth()->id())
                                    <button wire:click="deleteUser({{ $user->id }})" wire:confirm="¿Eliminar usuario? Se borrarán todos sus datos." class="text-red-500 hover:text-red-700">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4">{{ $users->links() }}</div>
        </div>
    </div>

    {{-- MODAL CREAR USUARIO --}}
    <x-modal name="create-user" focusable>
        <div class="p-6">
            <h2 class="mb-4 text-lg font-medium text-gray-900 dark:text-gray-100">Invitar Nuevo Usuario</h2>

            {{-- OPCIÓN A: Formulario Clásico por Email --}}
            <form wire:submit="createUser">
                <div class="mb-4">
                    <x-input-label for="name" value="Nombre" />
                    <x-text-input id="name" wire:model="name" class="block w-full mt-1" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div class="mb-4">
                    <x-input-label for="email" value="Email" />
                    <x-text-input id="email" wire:model="email" type="email" class="block w-full mt-1" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    <p class="mt-1 text-xs text-gray-500">Se enviará una contraseña automática a este correo.</p>
                </div>

                <div class="flex justify-end gap-2">
                    <x-primary-button>Enviar Invitación por Email</x-primary-button>
                </div>
            </form>

            {{-- SEPARADOR --}}
            <div class="relative flex items-center py-5">
                <div class="flex-grow border-t border-gray-300 dark:border-gray-600"></div>
                <span class="flex-shrink-0 mx-4 text-sm text-gray-400">O genera un enlace</span>
                <div class="flex-grow border-t border-gray-300 dark:border-gray-600"></div>
            </div>

            {{-- OPCIÓN B: Generar Link --}}
            <div class="text-center">
                @if(!$generatedLink)
                    <x-secondary-button wire:click="generateInviteLink">
                        <i class="mr-2 fa-solid fa-link"></i> Generar Enlace Único (24h)
                    </x-secondary-button>
                @else
                    {{-- MOSTRAR EL LINK SI YA SE GENERÓ --}}
                    <div class="p-4 text-left border border-green-200 rounded-md bg-green-50 dark:bg-green-900">
                        <p class="mb-2 text-sm font-bold text-green-800 dark:text-green-100">
                            ¡Enlace generado! Copia y envía esto:
                        </p>
                        <div class="flex items-center gap-2">
                            <input type="text" readonly value="{{ $generatedLink }}"
                                   class="w-full text-sm text-gray-600 bg-white border-gray-300 rounded-md dark:bg-gray-800 focus:ring-0 dark:text-gray-300"
                                   onclick="this.select()">
                        </div>
                        <p class="mt-2 text-xs text-green-600 dark:text-green-300">
                            <i class="fa-regular fa-clock"></i> Caduca en 24 horas o tras el primer uso.
                        </p>
                    </div>

                    {{-- Botón para cerrar o generar otro --}}
                    <div class="flex justify-end mt-4">
                        <button type="button" x-on:click="$dispatch('close-modal', 'create-user')" class="px-4 py-2 text-gray-800 transition bg-gray-200 rounded hover:bg-gray-300">
                            Cerrar
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </x-modal>
</div>
