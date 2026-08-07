<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="icon" href="{{ asset('assets/images/shea254-app-logo.ico') }}" type="image/x-icon">

        @if(config('meta-pixel.enabled'))
            <!-- Meta Pixel Code -->
            <script>
                !function(f,b,e,v,n,t,s)
                {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
                n.callMethod.apply(n,arguments):n.queue.push(arguments)};
                if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
                n.queue=[];t=b.createElement(e);t.async=!0;
                t.src=v;s=b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t,s)}(window, document,'script',
                'https://connect.facebook.net/en_US/fbevents.js');
                fbq('init', '{{ config('meta-pixel.pixel_id') }}');
                fbq('track', 'PageView');
            </script>
            <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ config('meta-pixel.pixel_id') }}&ev=PageView&noscript=1"/></noscript>
            <!-- End Meta Pixel Code -->
        @endif

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

        @if(config('meta-pixel.capi_enabled'))
            @php
                try {
                    app(\App\Services\MetaConversionsApiService::class)->trackPageView();
                } catch (\Exception $e) {
                    \Illuminate\Facades\Log::error('Failed to send CAPI PageView event');
                }
            @endphp
        @endif

        @livewireScripts

        @stack('scripts')
    </body>
</html>
