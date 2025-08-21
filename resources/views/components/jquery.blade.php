{{-- Check if the user is online --}}
@if (App::environment('production') && @fsockopen('www.google.com', 80))
    {{-- Load online scripts --}}
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
@else
    {{-- Load offline scripts --}}
    <script src="{{ asset('/assets/js/jquery.js') }}"></script>
@endif
{{ $slot }}