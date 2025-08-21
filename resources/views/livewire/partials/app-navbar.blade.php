<nav x-data="{ open: false }" class="app_navbar">
    <div class="nav_container">
        <!-- Branding -->
        <div class="branding">
            <a href="/" wire:navigate>
                <x-app-logo width="35" />
            </a>
        </div>

        <!-- Burger (Mobile) -->
        <div class="burger_menu md:hidden" @click="open = !open">
            <span :class="open ? 'rotate-45 translate-y-1.5' : ''"></span>
            <span :class="open ? 'opacity-0' : ''"></span>
            <span :class="open ? '-rotate-45 -translate-y-1.5' : ''"></span>
        </div>

        <!-- Nav Links -->
        <div :class="{ 'open': open }" class="nav_links" x-cloak>
            <div class="main_links">
                <a href="{{ Route::has('dashboard') ? route('dashboard') : '#' }}">Dashboard</a>
                <a href="{{ Route::has('users.index') ? route('users.index') : '#' }}" wire:navigate>Users</a>
                <a href="{{ Route::has('sales.index') ? route('sales.index') : '#' }}" wire:navigate>Sales</a>
                <a href="{{ Route::has('products.index') ? route('products.index') : '#' }}" wire:navigate>Products</a>
                <a href="{{ Route::has('delivery-regions.index') ? route('delivery-regions.index') : '#' }}" wire:navigate>Locations</a>
                <a href="{{ Route::has('blogs.index') ? route('blogs.index') : '#' }}" wire:navigate>Blogs</a>
                {{-- <a href="{{ Route::has('ratings.index') ? route('ratings.index') : '#' }}" wire:navigate>Ratings</a> --}}
                <a href="{{ Route::has('contact-messages.index') ? route('contact-messages.index') : '#' }}" wire:navigate>Messages</a>

                @auth
                    <!-- Mobile view -->
                    <div class="mobile_only md:hidden">
                        <a href="{{ Route::has('profile.edit') ? route('profile.edit') : '#' }}" wire:navigate>Profile</a>
                        <button wire:click="logout" class="text-left w-full">Logout</button>
                    </div>
                @endauth

                @guest
                    <a href="{{ route('login') }}" wire:navigate>Login</a>
                @endguest
            </div>
        </div>

        <!-- Desktop Dropdown (Authenticated Only) -->
        @auth
            <div class="user_dropdown hidden md:block" x-data="{ open: false }" @click.outside="open = false">
                <button @click="open = !open" class="user_dropdown_toggle">
                    <span>{{ $name }}</span>
                    <svg class="caret" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="open" x-cloak x-transition class="dropdown_menu">
                    <a href="{{ Route::has('profile.edit') ? route('profile.edit') : '#' }}" wire:navigate>Profile</a>
                    <button wire:click="logout" class="btn_danger">Logout</button>
                </div>
            </div>
        @endauth
    </div>
</nav>
