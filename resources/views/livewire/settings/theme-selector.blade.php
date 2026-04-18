<?php

use Livewire\Volt\Component;

new class extends Component
{
    // No server-side logic needed, everything is handled client-side via Alpine.js
}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Apariencia') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Configura el modo visual de la aplicación según tu preferencia.") }}
        </p>
    </header>

    <div class="max-w-xl mt-6" x-data="{ theme: localStorage.theme || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light') }">
        <x-input-label for="theme_selector" :value="__('Tema de la aplicación')" />
        
        <select id="theme_selector" 
                x-model="theme"
                @change="
                    localStorage.theme = theme; 
                    if (theme === 'dark') { 
                        document.documentElement.classList.add('dark'); 
                    } else { 
                        document.documentElement.classList.remove('dark'); 
                    }
                "
                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600">
            <option value="light">{{ __('Modo Claro') }}</option>
            <option value="dark">{{ __('Modo Oscuro') }}</option>
        </select>
    </div>
</section>
