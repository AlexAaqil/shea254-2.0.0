<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="shortcut icon" href="{{ asset('assets/images/logo.jpg') }}" type="image/x-icon">

        @vite(['resources/css/guest-layout.css', 'resources/js/app.js'])

        @livewireStyles

        @isset($head)
            {{ $head }}
        @else
            <title>{{ config('app.name') }}</title>
        @endisset
        @stack('head')
    </head>
    <body class="font-sans antialiased bg-white text-gray-900">
        <livewire:partials.navbar />

        <livewire:partials.flash-messages />

        <div class="guest_layout">
            {{ $slot ?? '' }}
            @yield('content')
        </div>

        <livewire:partials.footer />

        @livewireScripts

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
