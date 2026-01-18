Prueba
<div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
        {{ __('Calendario') }}
    </x-nav-link>

    <x-nav-link :href="route('history')" :active="request()->routeIs('history')" wire:navigate>
        {{ __('Historial') }}
    </x-nav-link>

    {{-- ✅ Asegúrate de guardar el archivo tras añadir esto --}}
    <x-nav-link :href="route('stats')" :active="request()->routeIs('stats')" wire:navigate>
        {{ __('Estadísticas') }}
    </x-nav-link>
</div>
<div class="pt-2 pb-3 space-y-1">
    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
        {{ __('Calendario') }}
    </x-responsive-nav-link>

    <x-responsive-nav-link :href="route('history')" :active="request()->routeIs('history')" wire:navigate>
        {{ __('Historial') }}
    </x-responsive-nav-link>

    {{-- ✅ Añadir también aquí --}}
    <x-responsive-nav-link :href="route('stats')" :active="request()->routeIs('stats')" wire:navigate>
        {{ __('Estadísticas') }}
    </x-responsive-nav-link>
</div>
