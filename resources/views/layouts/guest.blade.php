<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="icon" href="{{ asset('assets/images/shea254-app-logo.ico') }}" type="image/x-icon">

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

            <a href="https://wa.me/254711894267?text=Hi%20Shea254%20Team!%20I%20need%20help!" target="_blank" rel="noopener noreferrer" aria-label="Chat with us on WhatsApp" class="whatsapp_customer_service_btn">
                <x-svgs.whatsapp />
            </a>
        </main>

        @livewireScripts

        @stack('scripts')
    </body>
</html>
