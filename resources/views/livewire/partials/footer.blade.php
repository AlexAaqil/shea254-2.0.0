<footer>
    <div class="container">
        <div class="content">
            <div class="branding">
                <div class="image">
                    <x-app-logo />
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
                <h3>Quick Links</h3>
                <div class="links">
                    <a href="{{ Route::has('home-page') ? route('home-page') : '#' }}" wire:navigate>Home</a>
                    <a href="{{ Route::has('shop-page') ? route('shop-page') : '#' }}" wire:navigate>Shop</a>
                    <a href="{{ Route::has('about-page') ? route('about-page') : '#' }}" wire:navigate>About</a>
                    <a href="{{ Route::has('contact-page') ? route('contact-page') : '#' }}" wire:navigate>Contact</a>
                    <a href="{{ Route::has('blogs-page') ? route('blogs-page') : '#' }}" wire:navigate>Blogs</a>
                </div>
            </div>

            <div class="categories">
                <h3>Explore</h3>
                <div class="links">
                    <a href="{{ Route::has('products-categorized-page') ? route('products-categorized-page', 'raw-butters') : '#' }}" wire:navigate>Raw Butters</a>
                    <a href="{{ Route::has('products-categorized-page') ? route('products-categorized-page', 'whipped-butters') : '#' }}" wire:navigate>Whipped Butters</a>
                    <a href="{{ Route::has('products-categorized-page') ? route('products-categorized-page', 'black-soap') : '#' }}" wire:navigate>African Black Soap</a>
                    <a href="{{ Route::has('products-categorized-page') ? route('products-categorized-page', 'scrub') : '#' }}" wire:navigate>Scrubs</a>
                    <a href="{{ Route::has('products-categorized-page') ? route('products-categorized-page', 'essential-oil') : '#' }}" wire:navigate>Essential Oils</a>
                    <a href="{{ Route::has('products-categorized-page') ? route('products-categorized-page', 'carrier-oil') : '#' }}" wire:navigate>Carrier Oils</a>
                    <a href="{{ Route::has('products-categorized-page') ? route('products-categorized-page', 'toners-and-serums') : '#' }}" wire:navigate>Toners & Serums</a>
                    <a href="{{ Route::has('products-categorized-page') ? route('products-categorized-page', 'creams-gels') : '#' }}" wire:navigate>Cream & Gels</a>
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
