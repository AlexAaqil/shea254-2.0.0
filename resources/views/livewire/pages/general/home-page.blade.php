<x-slot name="head">
    {{-- SEO --}}
    <meta name="description" content="Best skin care experts in Nairobi, Kenya. We offer the best shea butter products, raw butters, african black soap, essential oils, toners & Serums, whipped butters, scrubs, carrier oils, cream and gels.">
    <meta name="keywords" content="raw butters, african black soap, essential oils, toners & Serums, whipped butters, scrubs, carrier oils, cream, gels">

    {{-- Open Graph --}}
    <meta property="og:title" content="{{ config('app.name') }}">
    <meta property="og:description" content="Best skin care experts">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('assets/images/logo.jpg') }}">

    <title>{{ config('app.name') }} | Skin Care Experts in Nairobi, Kenya</title>
</x-slot>

<div class="HomePage">
    <section class="Categories">
        <div class="container">
            @php
                $categories = collect([
                    (object) ['name' => 'Raw Butters', 'slug' => 'raw-butters'],
                    (object) ['name' => 'African Black Soap', 'slug' => 'black-soap'],
                    (object) ['name' => 'Essential Oils', 'slug' => 'essential-oil'],
                    (object) ['name' => 'Toners & Serums', 'slug' => 'toners-and-serums'],
                    (object) ['name' => 'Whipped Butters', 'slug' => 'whipped-butters'],
                    (object) ['name' => 'Scrubs', 'slug' => 'scrub'],
                    (object) ['name' => 'Carrier Oils', 'slug' => 'carrier-oil'],
                    (object) ['name' => 'Cream & Gels', 'slug' => 'creams-gels'],
                ]);
            @endphp

            @foreach($categories as $category)
                <div class="category">
                    <a href="{{ Route::has('products.categorized') ? route('products.categorized', $category->slug) : '#' }}">{{ $category->name }}</a>
                </div>
            @endforeach
        </div>
    </section>

    <section class="Hero">
        <div class="container">
            <div class="hero_wrapper">
                <div class="overlay"></div>

                <div class="text">
                    <div class="image">
                        <img src="{{ asset('/assets/images/hero-logo.png') }}" alt="Hero Logo">
                    </div>
                    <h1>Shea.254</h1>
                    <p class="sub_title">{{ config('app.slogan') }}</p>
                </div>

                <div
                    class="slideshow"
                    x-data="{
                        images: [
                            '{{ asset('/assets/images/shea254-1.jpg') }}',
                            '{{ asset('/assets/images/shea254-2.png') }}',
                            '{{ asset('/assets/images/shea254-3.png') }}',
                            '{{ asset('/assets/images/shea254-4.png') }}',
                            '{{ asset('/assets/images/shea254-5.png') }}',
                            '{{ asset('/assets/images/shea254-6.jpeg') }}',
                            '{{ asset('/assets/images/shea254-7.jpeg') }}',
                            '{{ asset('/assets/images/shea254-8.jpeg') }}',
                        ],
                        current: 0,
                        delay: 5000,
                        start() {
                            setInterval(() => {
                                this.current = (this.current + 1) % this.images.length;
                            }, this.delay);
                        }
                    }"
                    x-init="start()"
                >
                    <template x-for="(image, index) in images" :key="index">
                        <div
                            class="absolute inset-0 transition-opacity duration-1000"
                            x-show="current === index"
                            x-transition:enter="opacity-0"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="opacity-100"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                        >
                            <img
                                :src="image"
                                alt="{{ config('app.name') }} Hero Image"
                                class="w-full h-full object-cover"
                                loading="lazy"
                            />
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </section>

    <section class="About">
        <div class="container">
            <h2>Discover the Power of Natural Skincare</h2>
            <p>
                At Shea.254, we believe in the transformative power of natural ingredients. Our carefully curated
                collection of shea butters, whipped body butters, toners, and creams are designed to nourish and
                rejuvenate your skin, leaving you with a radiant, healthy glow.
            </p>
            <a href="{{ Route::has('shop-page') ? route('shop-page') : '#' }}">Explore Our Collection</a>
        </div>
    </section>

    <section class="FeaturedProducts">
        <div class="container">
            <div class="section_header">
                <h2>Most Popular</h2>
                <a href="{{ Route::has('shop-page') ? route('shop-page') : '#' }}">Shop All</a>
            </div>

            <div class="products_list custom_cards">
                @forelse($featured_products as $product)
                    @include('livewire.pages.general.products.card')
                @empty
                    <p>No products yet.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="CTA">
        <div class="container">
            <h2>Explore Our Wide Range of Products</h2>
            <p>From nourishing shea butters to rejuvenating toners and serums, our collection has something for every skin type and concern. Discover the perfect products to elevate your skincare routine.</p>
            <a href="{{ Route::has('shop-page') ? route('shop-page') : '#' }}">Explore Our Collection</a>
        </div>
    </section>
</div>
