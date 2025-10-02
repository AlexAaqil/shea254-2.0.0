<nav x-data="{ open: false }" class="app_navbar">
    <div class="nav_container">
        <div class="branding">
            <a href="/" wire:navigate>
                <x-app-logo width="35" />
            </a>
        </div>

        <div class="burger_menu md:hidden" @click="open = !open">
            <span :class="open ? 'rotate-45 translate-y-1.5' : ''"></span>
            <span :class="open ? 'opacity-0' : ''"></span>
            <span :class="open ? '-rotate-45 -translate-y-1.5' : ''"></span>
        </div>

        <div :class="{ 'open': open }" class="nav_links" x-cloak>
            <div class="main_links">
                @php
                    use App\Enums\UserRoles;

                    $user = auth()->user();

                    $nav_items = [
                        [
                            'route' => 'dashboard',
                            'label' => 'Dashboard',
                            'navigate' => false,
                        ],
                        [
                            'route' => 'users-orders.index',
                            'label' => 'Purchases',
                            'can' => $user && (!$user->isAdmin() && !$user->isCashier()),
                            'navigate' => true,
                        ],
                        [
                            'route' => 'users-reviews.index',
                            'label' => 'Reviews',
                            'can' => $user && (!$user->isAdmin() && !$user->isCashier()),
                            'navigate' => true,
                        ],
                        [
                            'route' => 'orders.index',
                            'label' => 'Orders',
                            'can' => $user && ($user->isAdmin() || $user->isCashier()),
                            'navigate' => true,
                        ],
                        [
                            'route' => 'product-offers.index',
                            'label' => 'Offers',
                            'can' => $user && $user->isAdmin(),
                            'navigate' => true,
                        ],
                        [
                            'route' => 'products.index',
                            'label' => 'Products',
                            'can' => $user && $user->isAdmin(),
                            'navigate' => true,
                        ],
                        [
                            'route' => 'delivery-locations.index',
                            'label' => 'Locations',
                            'can' => $user && $user->isAdmin(),
                            'navigate' => true,
                        ],
                        [
                            'route' => 'users.index',
                            'label' => 'Users',
                            'can' => $user && $user->isAdmin(),
                            'navigate' => true,
                        ],
                        [
                            'route' => 'contact-messages.index',
                            'label' => 'Messages',
                            'can' => $user && ($user->isAdmin() || $user->isCashier()),
                            'navigate' => true,
                        ],
                        [
                            'route' => 'blogs.index',
                            'label' => 'Blogs',
                            'can' => $user && $user->isAdmin(),
                            'navigate' => true,
                        ],
                    ];

                    $currentRoute = Route::currentRouteName();
                @endphp

                @foreach ($nav_items as $item)
                    @if (!isset($item['can']) || $item['can'])
                        <a
                            href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}"
                            @if($item['navigate'] ?? true) wire:navigate @endif
                            class="{{ $currentRoute === $item['route'] ? 'active' : '' }}"
                        >
                            {{ $item['label'] }}
                        </a>
                    @endif
                @endforeach

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
