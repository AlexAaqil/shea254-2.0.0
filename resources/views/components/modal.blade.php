@props([
    'name',
    'show' => false,
    'maxWidth' => '2xl',
    'focusable' => false,
])

@php
$maxWidthClass = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
][$maxWidth];
@endphp

<div
    x-data="{
        show: @js($show),
        closeOnEscape(event) {
            if (event.key === 'Escape') this.show = false;
        },
        init() {
            this.$watch('show', value => {
                document.body.classList.toggle('overflow-y-hidden', value);
                if (value && {{ $focusable ? 'true' : 'false' }}) {
                    this.$nextTick(() => {
                        let el = this.$el.querySelector('[autofocus]');
                        if (el) el.focus();
                    });
                }
            });
        }
    }"
    x-init="init()"
    x-on:keydown.window="closeOnEscape"
    x-on:open-modal.window="if ($event.detail === '{{ $name }}') show = true"
    x-on:close-modal.window="if ($event.detail === '{{ $name }}') show = false"
    x-show="show"
    class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0"
    style="display: none;"
>
    <!-- Overlay -->
    <div
        x-show="show"
        x-transition.opacity
        class="fixed inset-0 bg-gray-500/75"
        x-on:click="show = false"
    ></div>

    <!-- Modal -->
    <div
        x-show="show"
        x-transition
        class="relative mx-auto mt-30 w-full rounded-lg bg-white shadow-xl {{ $maxWidthClass }}"
        x-on:click.stop
    >
        {{ $slot }}
    </div>
</div>
