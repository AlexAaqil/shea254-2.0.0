<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- CSRF --}}
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- Vite Assets --}}
        @vite(['resources/css/guest-layout.css', 'resources/js/app.js'])

        {{-- Livewire --}}
        @livewireStyles

        {{-- Dynamic Slot/Stacked Head --}}
        @isset($head)
            {{ $head }}
        @else
            <title>{{ config('app.name') }}</title>
        @endisset
        @stack('head')
    </head>
    <body class="font-sans antialiased">
        <livewire:partials.navbar />

        <livewire:partials.flash-messages />

        <div class="guest_layout">
            {{-- Page Content --}}
            {{ $slot ?? '' }}
            @yield('content')
        </div>

        <livewire:partials.footer />

        {{-- Livewire --}}
        @livewireScripts

        {{-- Dynamic Scripts --}}
        @isset($scripts)
            {{ $scripts }}
        @endisset
        @stack('scripts')

        @isset($afterScripts)
            {{ $afterScripts }}
        @endisset
        @stack('after-scripts')
    </body>
</html>
