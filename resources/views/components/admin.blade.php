<x-app-layout>
    <x-slot name='extra_head'>
        {{ $extra_head ?? '' }}
    </x-slot>

    <main class="Admin">
        @include('admin.partials.sidenav')

        <div class="admin_content">
            @include('partials.messages')
            {{ $slot }}
        </div>
    </main>

    <x-slot name='javascript'>
        {{ $javascript ?? '' }}
    </x-slot>
</x-app-layout>