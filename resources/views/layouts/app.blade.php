<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="icon" href="{{ asset('assets/images/shea254-app-logo.ico') }}" type="image/x-icon">

        @livewireStyles

        @vite(['resources/css/app-layout.css', 'resources/js/app.js'])

        @isset($head)
            {{ $head }}
        @else
            <title>{{ config('app.name') }} | Skin Care Experts</title>
        @endisset
    </head>
    <body class="antialiased">
        <livewire:partials.flash-messages />

        <main class="app_layout">
            <livewire:partials.app-navbar />

            <div class="app_layout_container">
                {{ $slot }}
            </div>
        </main>

        @livewireScripts

        @stack('scripts')
    </body>
</html>
