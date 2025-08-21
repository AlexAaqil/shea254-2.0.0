<nav x-data="{ open:false }" @click.outside="open = false" class="guest_nav relative">
    <div class="container">

        <!-- Branding -->
        <div class="branding">
            <a href="/" wire:navigate>
                <x-app-logo width="50" height="50" />
            </a>
        </div>

        <!-- Nav Links (centered on desktop, collapsible on mobile) -->
        <div class="nav_links">
            @php
                $links = [
                    ['href' => 'home-page', 'text' => 'Home'],
                    ['href' => 'shop-page', 'text' => 'Shop'],
                    ['href' => 'about-page', 'text' => 'About'],
                    ['href' => 'blog-page', 'text' => 'Blog'],
                    ['href' => 'contact-page', 'text' => 'Contact'],
                ];
            @endphp

            <div class="main_links">
                @auth
                    <a href="{{ Route::has('dashboard') ? route('dashboard') : '#' }}" wire:navigate>Dashboard</a>
                @endauth

                @foreach ($links as $link)
                    <a href="{{ Route::has($link['href']) ? route($link['href']) : '#' }}" wire:navigate>{{ $link['text'] }}</a>
                @endforeach
            </div>
        </div>

        <!-- Other Links (cart + login/logout) always visible) -->
        <div class="other_links flex items-center gap-4">
            <div class="cart relative bg-blue-100 p-2 rounded-lg">
                <a href="{{ Route::has('cart-page') ? route('cart-page') : '#' }}" wire:navigate>
                    <x-svgs.shopping-cart class="w-6 h-6" />
                    @if($cart_count >= 0)
                        <span class="absolute -top-2 -right-2 bg-blue-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                            {{ $cart_count }}
                        </span>
                    @endif
                </a>
            </div>

            @auth
                <button wire:click="logout" class="btn btn_danger">Logout</button>
            @else
                <a href="{{ Route::has('login') ? route('login') : '#' }}" class="btn_login" wire:navigate>Login</a>
            @endauth
        </div>

        <!-- Burger Menu (mobile only, toggles nav_links) -->
        <div class="burger_menu md:hidden ml-4" @click="open = !open">
            <span :class="open ? 'rotate-45 translate-y-1.5' : ''"></span>
            <span :class="open ? 'opacity-0' : ''"></span>
            <span :class="open ? '-rotate-45 -translate-y-1.5' : ''"></span>
        </div>
    </div>

    <!-- Mobile dropdown links -->
    <div class="nav_links md:hidden" :class="{ 'block' : open, 'hidden' : !open }">
        <div class="main_links flex flex-col space-y-2 mt-4 px-4">
            @auth
                <a href="{{ Route::has('dashboard') ? route('dashboard') : '#' }}" wire:navigate>Dashboard</a>
            @endauth
            @foreach ($links as $link)
                <a href="{{ Route::has($link['href']) ? route($link['href']) : '#' }}" wire:navigate>{{ $link['text'] }}</a>
            @endforeach
        </div>
    </div>
</nav>
