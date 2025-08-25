<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="icon" href="{{ asset('assets/images/app-logo.ico') }}" type="image/x-icon">

        @livewireStyles

        @vite(['resources/css/guest-layout.css', 'resources/js/app.js'])

        @isset($head)
            {{ $head }}
        @else
            <title>{{ config('app.name') }} | Skin Care Experts</title>
        @endisset
    </head>
    <body class="antialiased">
        <livewire:partials.flash-messages />

        <main class="guest_layout">
            <livewire:partials.navbar />

            <div class="guest_layout_container">
                {{ $slot }}
            </div>

            <livewire:partials.footer />
        </main>

        @livewireScripts

        @stack('scripts')
    </body>
</html>
