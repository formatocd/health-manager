<?php

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public $token;
    public $invitation;

    public string $name = '';
    public string $username = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount($token)
    {
        $this->token = $token;

        // 1. Buscar la invitación
        $this->invitation = Invitation::where('token', $token)->first();

        // 2. Validaciones de seguridad (404 si no es válido)
        if (!$this->invitation) {
            abort(404, 'Invitación no encontrada.');
        }

        // 3. Validar si ya se usó
        if ($this->invitation->used_at) {
            abort(403, 'Este enlace de invitación ya ha sido utilizado.');
        }

        // 4. Validar si ha caducado por tiempo
        if ($this->invitation->expires_at->isPast()) {
            abort(403, 'Este enlace de invitación ha caducado.');
        }
    }

    public function register(): void
    {
        // Validamos el formulario normal
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:'.User::class], // Tu campo custom
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = $this->invitation->role; // Asignamos el rol de la invitación

        // --- TRANSACCIÓN DB ---
        DB::transaction(function () use ($validated) {
            // A. Crear Usuario
            $user = User::create($validated);

            // B. Marcar invitación como usada (ESTO CIERRA EL ENLACE)
            $this->invitation->update([
                'used_at' => now(),
            ]);

            event(new Registered($user));
            Auth::login($user);
        });

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    {{-- Título --}}
    <div class="mb-6 text-center">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
            Has sido invitado a Health Manager
        </h2>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Completa tus datos para activar tu cuenta.
        </p>
    </div>

    <form wire:submit="register">
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="username" :value="__('Username')" />
            <x-text-input wire:model="username" id="username" class="block mt-1 w-full" type="text" name="username" required />
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input wire:model="password" id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</div>
