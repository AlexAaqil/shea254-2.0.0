<footer>
    <div class="container">
        <div class="content">
            <div class="branding">
                <div class="image">
                    <img src="{{ asset('assets/images/logo.jpg') }}" alt="{{ config('app.name') }} Logo">
                </div>

                <div class="info">
                    <h3>Shea.254</h3>
                    <p>Suppliers of Raw Shea, Body butter, Cocoa Butter, Black Soap, Essential & Carrier Oils</p>
                    <div class="info">
                        <p>
                            {!! config('app.address') !!}
                        </p>
                    </div>
                </div>
            </div>

            <div class="quick_links">
                <h3>Explore</h3>
                <div class="links">
                    <a href="{{ Route::has('home-page') ? route('home-page') : '#' }}" wire:navigate>Home</a>
                    <a href="{{ Route::has('shop-page') ? route('shop-page') : '#' }}" wire:navigate>Shop</a>
                    <a href="{{ Route::has('about-page') ? route('about-page') : '#' }}" wire:navigate>About</a>
                    <a href="{{ Route::has('contact-page') ? route('contact-page') : '#' }}" wire:navigate>Contact</a>
                    <a href="{{ Route::has('blogs-page') ? route('blogs-page') : '#' }}" wire:navigate>Blogs</a>
                </div>
            </div>

            <div class="connect">
                <h3>Connect With Us</h3>
                <div class="contacts">
                    <p>{{ config('app.phone_number') }}</p>
                    <p>{{ config('app.email') }}</p>
                </div>

                <div class="socials">
                    <a href="{{ config('app.instagram') }}">
                        <x-svgs.instagram />
                    </a>
                    <a href="{{ config('app.facebook') }}">
                        <x-svgs.facebook />
                    </a>
                    <a href="{{ config('app.whatsapp') }}">
                        <x-svgs.whatsapp />
                    </a>
                    <a href="{{ config('app.tiktok') }}">
                        <x-svgs.tiktok />
                    </a>
                </div>
            </div>
        </div>

        <div class="copyrights">
            <p class="text">&copy; 2025 {{ config('app.name') }}. All rights reserved.</p>
            <div class="documents">
                <a href="/privacy-policy">Privacy Policy</a>
                <a href="/terms">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>
