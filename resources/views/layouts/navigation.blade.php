<div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
    {{-- Enlace al Dashboard (Calendario) con wire:navigate para carga instantánea --}}
    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
        {{ __('Calendario') }}
    </x-nav-link>

    {{-- Enlace al Historial --}}
    <x-nav-link :href="route('history')" :active="request()->routeIs('history')" wire:navigate>
        {{ __('Historial') }}
    </x-nav-link>
</div>
<div class="pt-2 pb-3 space-y-1">
    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
        {{ __('Calendario') }}
    </x-responsive-nav-link>

    <x-responsive-nav-link :href="route('history')" :active="request()->routeIs('history')" wire:navigate>
        {{ __('Historial') }}
    </x-responsive-nav-link>
</div>
