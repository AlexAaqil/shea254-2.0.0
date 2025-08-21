{{-- Check if the user is online --}}
@if (App::environment('production') && @fsockopen('www.google.com', 80))
    {{-- Load online scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@else
    {{-- Load offline scripts --}}
    <script src="{{ asset('/assets/js/sweetalert.js') }}"></script>
@endif
{{ $slot }}