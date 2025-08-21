<aside class="admin_sidebar">
    <div class="branding">
        <a href="{{ route('home') }}">
            <img src="{{ asset('assets/images/logo.jpg') }}" alt="Logo" width=30 height=30 class="rounded">
        </a>
        <h1>{{ config('app.name') }}</h1>
    </div>

    <div class="nav_links">
        <ul>
            @php
                $navLinks = [
                    ['route' => 'admin.dashboard', 'icon' => 'fas fa-home', 'text' => 'Dashboard'],
                    ['route' => 'orders.index', 'icon' => 'fas fa-truck-loading', 'text' => 'Orders'],
                    ['route' => 'blogs.index', 'icon' => 'fas fa-blog', 'text' => 'Blogs'],
                    ['route' => 'product-reviews.index', 'icon' => 'fas fa-star', 'text' => 'Ratings'],
                    ['route' => 'comments.index', 'icon' => 'fas fa-comment', 'text' => 'Comments'],
                    ['route' => 'users.index', 'icon' => 'fas fa-users-cog', 'text' => 'Users'],
                    ['route' => 'products.index', 'icon' => 'fas fa-barcode', 'text' => 'Products'],
                    ['route' => 'locations.index', 'icon' => 'fas fa-map-marker-alt', 'text' => 'Locations'],
                ];
            @endphp

            @foreach($navLinks as $link)
                <li class="nav-link {{ Route::currentRouteName() === $link['route'] ? 'active' : '' }}">
                    <a href="{{ $link['route'] ? route($link['route']) : '#' }}">
                        <i class="{{ $link['icon'] }}"></i>
                        <span class="text">{{ $link['text'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="footer">
        <ul>
            <li class="profile">
                <img src="{{ asset('assets/images/default_profile.jpg') }}" alt="Logo" width=40 height=40 class="rounded">
                <span class="text">
                    <a href="{{ route('profile.edit') }}">
                        {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                    </a>
                </span>
            </li>
            <li class="logout">
                <form action="{{ route('logout') }}" method="post">
                    @csrf
                    <button type="submit">
                        <i class="fas fa-sign-out-alt icons"></i>
                        <span class="text">Log Out</span>
                    </button>
                </form>
            </li>
        </ul>
    </div>
</aside>
