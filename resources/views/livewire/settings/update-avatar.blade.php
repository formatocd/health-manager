<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public $photo;

    public function updateProfilePhoto()
    {
        $this->validate([
            'photo' => ['required', 'image', 'max:2048'], // 2MB Max
        ]);

        $user = Auth::user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $this->photo->store('avatars', 'public');

        $user->forceFill([
            'avatar' => $path,
        ])->save();

        $this->dispatch('profile-updated'); 
        $this->dispatch('avatar-updated'); 
        
        $this->reset('photo');
    }

    public function deleteProfilePhoto()
    {
        $user = Auth::user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            
            $user->forceFill([
                'avatar' => null,
            ])->save();
        }
        
        $this->dispatch('profile-updated');
        $this->dispatch('avatar-updated');
    }
}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Foto de Perfil') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Actualiza tu foto de perfil visible en los menús y la sección de compartir datos.") }}
        </p>
    </header>

    <div class="max-w-xl mt-6" x-data="{ photoName: null, photoPreview: null }">
        <form wire:submit="updateProfilePhoto">
            <!-- Profile Photo File Input -->
            <input type="file" id="photo" class="hidden"
                        wire:model.live="photo"
                        x-ref="photo"
                        x-on:change="
                                const file = $refs.photo.files[0];
                                if (!file) return;
                                photoName = file.name;
                                const reader = new FileReader();
                                reader.onload = (e) => {
                                    photoPreview = e.target.result;
                                };
                                reader.readAsDataURL(file);
                        " />

            <!-- Current Profile Photo -->
            <div class="mt-2" x-show="! photoPreview">
                @if(auth()->user()->avatar_url)
                    <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="object-cover w-20 h-20 rounded-full">
                @else
                    <div class="flex items-center justify-center w-20 h-20 text-3xl font-bold text-purple-700 bg-purple-100 rounded-full dark:bg-purple-900 dark:text-purple-300">
                        {{ strtoupper(substr(auth()->user()->username ?? auth()->user()->name, 0, 1)) }}
                    </div>
                @endif
            </div>

            <!-- New Profile Photo Preview -->
            <div class="mt-2" x-show="photoPreview" style="display: none;">
                <span class="block w-20 h-20 bg-center bg-no-repeat bg-cover rounded-full"
                      x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                </span>
            </div>

            <x-input-error :messages="$errors->get('photo')" class="mt-2" />

            <div class="flex items-center gap-4 mt-4">
                <x-secondary-button class="mt-2 me-2" type="button" x-on:click.prevent="$refs.photo.click()">
                    {{ __('Seleccionar una nueva foto') }}
                </x-secondary-button>

                @if (auth()->user()->avatar)
                    <x-secondary-button type="button" class="mt-2 text-red-600 border-red-200 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:border-red-800 dark:text-red-400 dark:hover:bg-red-900/40" wire:click="deleteProfilePhoto" x-on:click="photoPreview = null">
                        {{ __('Eliminar foto') }}
                    </x-secondary-button>
                @endif
            </div>
            
            <div class="flex items-center gap-4 mt-6" x-show="photoPreview" style="display: none;">
                <x-primary-button wire:loading.attr="disabled" wire:target="photo, updateProfilePhoto">{{ __('Guardar Foto') }}</x-primary-button>
                <div wire:loading wire:target="photo" class="text-sm text-gray-500 dark:text-gray-400">
                    Subiendo imagen...
                </div>
            </div>
            
            <div class="mt-2">
                <x-action-message class="me-3" on="avatar-updated">
                    {{ __('Guardado correctamente.') }}
                </x-action-message>
            </div>
        </form>
    </div>
</section>
