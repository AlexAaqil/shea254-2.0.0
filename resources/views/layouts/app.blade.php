<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="{{ asset('assets/images/logo.jpg') }}" type="image/x-icon">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('assets/icons/icons.css') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            AOS.init({
                duration: 800,
                easing: 'ease-out',
                once: true
            });
        });
    </script>

    @isset($extra_head)
        {{ $extra_head }}
    @endisset

    @vite(['resources/css/guest-layout.css', 'resources/js/app.js'])

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Meta Pixel Code -->
    <script>
        !function (f, b, e, v, n, t, s) {
            if (f.fbq) return; n = f.fbq = function () {
                n.callMethod ?
                n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if (!f._fbq) f._fbq = n; n.push = n; n.loaded = !0; n.version = '2.0';
            n.queue = []; t = b.createElement(e); t.async = !0;
            t.src = v; s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s)
        }(window, document, 'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '472959975149711');
        fbq('track', 'PageView');
    </script>
    <noscript>
        <img height="1" width="1" style="display:none"
            src="https://www.facebook.com/tr?id=472959975149711&ev=PageView&noscript=1" />
    </noscript>
    <!-- End Meta Pixel Code -->

    <!-- Meta Pixel Event Tracking -->
    @if(Session::has('pixel_data'))
        <script>
            fbq('track', '{{ request()->route()->getName() === "cart.destroy" ? "RemoveFromCart" : "AddToCart" }}',
                @json(Session::get('pixel_data'))
            );
        </script>
    @endif

    @if(Session::has('purchase_pixel_data'))
        <script>
            fbq('track', 'Purchase', @json(Session::get('purchase_pixel_data')));
        </script>
    @endif
    <!-- End Meta Pixel Event Tracking -->
</head>

<body>
    {{ $slot }}

    @isset($javascript)
        {{ $javascript }}
        <script src="{{ asset('assets/js/alert.js') }}"></script>
        <script src="{{ asset('assets/js/custom.js') }}"></script>
    @else
        <script src="{{ asset('assets/js/jquery.js') }}"></script>
        <script src="{{ asset('assets/js/alert.js') }}"></script>
        <script src="{{ asset('assets/js/custom.js') }}"></script>
    @endisset
</body>

</html>
