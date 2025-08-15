<nav x-data="{ open:false }" @click.outside="open = false" class="guest_nav relative">
    <div class="container">
        <div class="branding">
            <a href="/" wire:navigate>
                <x-app-logo width="50" height="50" />
            </a>
        </div>

        <div class="burger_menu" @click="open = !open">
            <span :class="open ? 'rotate-45 translate-y-1.5' : ''"></span>
            <span :class="open ? 'opacity-0' : ''"></span>
            <span :class="open ? '-rotate-45 -translate-y-1.5' : ''"></span>
        </div>

        <div class="nav_links" :class="{ 'open' : open }">
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
                @endif
                @foreach ($links as $link)
                    <a href="{{ Route::has($link['href']) ? route($link['href']) : '#' }}" wire:navigate>{{ $link['text'] }}</a>
                @endforeach
            </div>

            <div class="other_links">
                <div class="cart relative bg-blue-100 p-2 rounded-lg">
                    <a href="{{ Route::has('cart-page') ? route('cart-page') : '#' }}" wire:navigate>
                        <x-svgs.shopping-cart class="w-6 h-6" />

                        @if($cart_count >= 0)
                            <span class="absolute p-2 -top-2 -right-2 bg-blue-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                {{ $cart_count }}
                            </span>
                        @endif
                    </a>
                </div>

                @auth
                    <button wire:click="logout" class="btn btn_danger">Logout</button>
                @else
                    <a href="{{ Route::has('login') ? route('login') : '#' }}" class="btn" wire:navigate>Login</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
