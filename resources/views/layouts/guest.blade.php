<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        {{-- FAVICON --}}
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

        <linkpreconnect href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <!-- Scripts -->
        <script>
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-gray-900">
        <div class="flex flex-col items-center min-h-screen pt-6 bg-gray-100 sm:justify-center sm:pt-0 dark:bg-gray-900">

            {{-- BLOQUE LOGO Y TÍTULO --}}
            <div class="mb-6"> {{-- Margen inferior para separar del formulario --}}
                <a href="/" class="flex flex-col items-center gap-3"> {{-- gap-3 separa el logo del texto --}}

                    {{-- LOGO --}}
                    <x-application-logo class="w-24 h-24 text-gray-500 fill-current" />

                    {{-- TÍTULO --}}
                    <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                        {{ config('app.name', 'Health Manager') }}
                    </h1>
                </a>
            </div>

            {{-- BLOQUE FORMULARIO (La tarjeta) --}}
            <div class="w-full px-6 py-4 overflow-hidden bg-white shadow-md sm:max-w-md dark:bg-gray-800 sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
