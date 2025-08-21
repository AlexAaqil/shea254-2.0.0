<x-app-layout>
    @include('partials.navbar')
    <main class="Homepage">
        @include('partials.messages')

        <x-ad-unit type="display" />

        <section class="Hero">
            <div class="categories" style=" align-items: center; display: flex; height: 200px;">
                <div style="display: grid; gap: 1em;">
                    <div class="category">
                        <a href="{{ route('products.categorized', $product = 'raw-butters') }}">
                            <h3>Raw Butters</h3>
                        </a>
                    </div>
                    <div class="category">
                        <a href="{{ route('products.categorized', $product = 'whipped-butters') }}">
                            <h3>Whipped Butters</h3>
                        </a>
                    </div>
                </div>

                <div style="display: grid; gap: 1em;">
                    <div class="category">
                        <a href="{{ route('products.categorized', $product = 'black-soap') }}">
                            <h3>African Black Soap</h3>
                        </a>
                    </div>

                    <div class="category">
                        <a href="{{ route('products.categorized', $product = 'scrub') }}">
                            <h3>Scrubs</h3>
                        </a>
                    </div>
                </div>

                <div style="display: grid; gap: 1em;">
                    <div class="category">
                        <a href="{{ route('products.categorized', $product = 'essential-oil') }}">
                            <h3>Essential Oils</h3>
                        </a>
                    </div>

                    <div class="category">
                        <a href="{{ route('products.categorized', $product = 'carrier-oil') }}">
                            <h3>Carrier Oils</h3>
                        </a>
                    </div>

                </div>

                <div style="display: grid; gap: 1em;">
                    <div class="category">
                        <a href="{{ route('products.categorized', $product = 'toners-and-serums') }}">
                            <!-- new -->
                            <h3>Toners & Serums</h3>
                        </a>
                    </div>

                    <div class="category">
                        <a href="{{ route('products.categorized', $product = 'creams-gels') }}">
                            <h3>Cream & Gels</h3>
                        </a>
                    </div>
                </div>
            </div>

            <div class="hero_text">
                <div class="slideshow">
                    <img src="{{ asset('/assets/images/shea254-1.jpg') }}" alt="Hero Image">
                    <img src="{{ asset('/assets/images/shea254-2.png') }}" alt="Hero Image">
                    <img src="{{ asset('/assets/images/shea254-3.png') }}" alt="Hero Image">
                    <img src="{{ asset('/assets/images/shea254-4.png') }}" alt="Hero Image">
                    <img src="{{ asset('/assets/images/shea254-5.png') }}" alt="Hero Image">
                    <img src="{{ asset('/assets/images/shea254-6.jpeg') }}" alt="Hero Image">
                    <img src="{{ asset('/assets/images/shea254-7.jpeg') }}" alt="Hero Image">
                    <img src="{{ asset('/assets/images/shea254-8.jpeg') }}" alt="Hero Image">
                </div>

                <div class="brand">
                    <a href="{{ route('home') }}" style="text-decoration: none;">
                        <div class="image">
                            <img src="{{ asset('/assets/images/hero-logo.png') }}" alt="Hero Logo">
                        </div>
                    </a>
                    <h1>Shea.254</h1>
                    <h2 style=" color: white">Skin Care Experts</h2>
                </div>
            </div>
        </section>


        <!-- <section class="promo-section" style="padding: 4rem 2rem; background: linear-gradient(135deg, #ffffff, #f8fafc);">
            <div class="container" style="max-width: 1200px; margin: 0 auto;">
                <div style="display: flex; align-items: center; gap: 3rem; flex-wrap: wrap;">
                    <div class="promo-image" style="flex: 1; min-width: 300px;">
                        <img
                            src="{{ asset('/assets/images/promo.png') }}"
                            alt="Special Offer"
                            style="
                                width: 100%;
                                height: auto;
                                border-radius: 20px;
                                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
                                transition: transform 0.3s ease;
                            "
                            onmouseover="this.style.transform='scale(1.02)'"
                            onmouseout="this.style.transform='scale(1)'"
                        >
                    </div>
                    <div class="promo-content" style="flex: 1; min-width: 300px;">
                        <h2 style="
                            font-size: clamp(1.875rem, 3vw, 2.25rem);
                            color: red;
                            font-weight: 800;
                            margin-bottom: 1rem;
                            line-height: 1.2;
                        ">
                            Christmas Special Offer!
                        </h2>
                        <p style="
                            font-size: clamp(1rem, 2vw, 1.125rem);
                            color: #64748b;
                            line-height: 1.7;
                            margin-bottom: 2rem;
                        ">
                            Indulge in the magic of the season with our limited-time holiday collection. Pick up and deliveries of any pre-ordered or sold out items to be done from 3rd January 2025
                        </p>
                        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                            <a href="{{ route('shop') }}"
                               style="
                                    display: inline-block;
                                    background: #1a237e;
                                    color: white;
                                    text-decoration: none;
                                    padding: 0.875em 2em;
                                    border-radius: 24px;
                                    font-size: clamp(0.875rem, 2vw, 1rem);
                                    font-weight: 600;
                                    transition: all 0.3s ease;
                                    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                               "
                               onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 8px -1px rgba(0, 0, 0, 0.15)'"
                               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.1)'"
                            >
                                Shop Now
                            </a>
                            <!-- <span style="
                                display: inline-block;
                                padding: 0.875em 0;
                                font-size: clamp(0.875rem, 2vw, 1rem);
                                color: #1a237e;
                                font-weight: 600;
                            ">
                                Use Code: XMAS20
                            </span> -->
        </div>
        </div>
        </div>
        </div>
        </section> -->

        <x-ad-unit type="in-article" />
        <section class="" style="
    /* background: linear-gradient(135deg, #f8fafc, #ffffff); */
      text-align: center;">
            <div class="container" style="max-width: 800px; margin: 0 auto;">
                <h2 style="font-size: clamp(2rem, 5vw, 2.5rem); color: #1a237e; font-weight: 800; margin-bottom: 1em;">
                    Discover the Power of Natural Skincare</h2>
                <p style="font-size: clamp(1rem, 4.5vw, 1.2rem); color: #64748b; line-height: 1.6; margin-bottom: 2em;">
                    At Shea.254, we believe in the transformative power of natural ingredients. Our carefully curated
                    collection of shea butters, whipped body butters, toners, and creams are designed to nourish and
                    rejuvenate your skin, leaving you with a radiant, healthy glow.
                </p>
                <a href="{{ route('shop') }}"
                    style="display: inline-block; background: #1a237e; color: white; text-decoration: none; padding: 0.75em 1.5em; border-radius: 24px; font-size: clamp(0.9rem, 4vw, 1rem); transition: background 0.3s ease;">
                    Explore Our Collection
                </a>
            </div>
        </section>
        <x-ad-unit type="display" />
        <x-ad-unit type="display" />

        <section class="Products">
            <div class="container">
                <div class="header">
                    <h1>Most Popular</h1>
                    <a href="{{ route('shop') }}">Shop all</a>
                </div>

                <div class="card_wrapper">
                    @foreach($featured_products as $product)
                        @include('partials.product')
                    @endforeach
                </div>
            </div>
        </section>

        <x-ad-unit type="display" />
        <x-ad-unit type="display" />


        @if($testimonials->count() > 2)
            <section class="Testimonials">
                <div class="container">
                    <div class="header">
                        <h1>Testimonials</h1>
                    </div>

                    <div class="testimonials">
                        @foreach($testimonials as $testimonial)
                            <div class="testimonial">
                                <p>{{ $testimonial->review }}</p>
                                <p class="details">
                                    <span>
                                        {{ $testimonial->user->first_name . ' ' . $testimonial->user->last_name }}</span>
                                    <span>{{ $testimonial->created_at->diffForHumans() }}</span>
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <section class="Explore"
            style="background: linear-gradient(135deg, #1a237e, #5c6bc0); padding: 4em 2em; text-align: center;">
            <div class="container" style="max-width: 800px; margin: 0 auto;">
                <h2 style="font-size: clamp(2rem, 5vw, 2.5rem); color: white; font-weight: 800; margin-bottom: 1em;">
                    Explore Our Wide Range of Products</h2>
                <p style="font-size: clamp(1rem, 4.5vw, 1.2rem); color: white; line-height: 1.6; margin-bottom: 2em;">
                    From nourishing shea butters to rejuvenating toners and serums, our collection has something for
                    every skin type and concern. Discover the perfect products to elevate your skincare routine.
                </p>
                <a href="{{ route('shop') }}"
                    style="display: inline-block; background: white; color: #1a237e; text-decoration: none; padding: 0.75em 1.5em; border-radius: 24px; font-size: clamp(0.9rem, 4vw, 1rem); transition: background 0.3s ease, color 0.3s ease;">
                    Explore Our Collection
                </a>
            </div>
        </section>
        <x-ad-unit type="display" />
        <x-ad-unit type="display" />

        @include('partials.footer')
    </main>

    <x-slot name="javascript">
        <x-jquery>
            <script>
                $(document).ready(function () {
                    var currentSlide = 0;
                    var slides = $('.hero_text .slideshow img');

                    function showSlide(index) {
                        slides.removeClass('active');
                        slides.eq(index).addClass('active');
                    }

                    function nextSlide() {
                        currentSlide = (currentSlide + 1) % slides.length;
                        showSlide(currentSlide);
                    }

                    // Show the first slide initially
                    showSlide(currentSlide);

                    // Set interval to switch slides every 5 seconds
                    setInterval(nextSlide, 5000);
                });
            </script>
        </x-jquery>
    </x-slot>

</x-app-layout>
