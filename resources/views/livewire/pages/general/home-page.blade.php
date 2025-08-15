<div class="HomePage">
    <section class="Categories">
        <div class="container">
            <div class="categories_list" style=" align-items: center; display: flex; height: 200px;">
                @php
                    $categories = [
                        ['slug' => 'raw-butters', 'name' => 'Raw Butters'],
                        ['slug' => 'whipped-butters', 'name' => 'Whipped Butters'],
                        ['slug' => 'black-soap', 'name' => 'African Black Soap'],
                        ['slug' => 'scrub', 'name' => 'Scrubs'],
                        ['slug' => 'essential-oil', 'name' => 'Essential Oils'],
                        ['slug' => 'carrier-oil', 'name' => 'Carrier Oils'],
                        ['slug' => 'toners-and-serums', 'name' => 'Toners & Serums'],
                        ['slug' => 'creams-gels', 'name' => 'Cream & Gels'],
                    ];
                @endphp

                @foreach (array_chunk($categories, 2) as $categoryGroup)
                    <div style="categories_group display: grid; gap: 1em;">
                        @foreach ($categoryGroup as $category)
                            <div class="category" style="display: grid; gap: 1em;">
                                <a href="{{ Route::has('products.categorized') ? route('products.categorized', $category['slug']) : '#' }}">
                                    <h3>{{ $category['name'] }}</h3>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
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
                    <p class="sub_title">Skin Care Experts</p>
                </div>

                <div
                    class="slideshow"
                    x-data="{
                        images: [
                            '{{ asset('assets/images/shea254-1.jpg') }}',
                            '{{ asset('assets/images/shea254-2.png') }}',
                            '{{ asset('assets/images/shea254-3.png') }}',
                            '{{ asset('assets/images/shea254-4.png') }}',
                            '{{ asset('assets/images/shea254-5.png') }}',
                            '{{ asset('assets/images/shea254-6.jpeg') }}',
                            '{{ asset('assets/images/shea254-7.jpeg') }}',
                            '{{ asset('assets/images/shea254-8.jpeg') }}',
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
                                alt="{{ config('app.name') }} Online Shopper"
                                class="w-full h-full object-cover"
                                loading="lazy"
                            />
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </section>

    <div class="About">
        <div class="container">
            <h2>Discover the Power of Natural Skincare</h2>
            <p>At Shea.254, we believe in the transformative power of natural ingredients. Our carefully curated collection of shea butters, whipped body butters, toners, and creams are designed to nourish and rejuvenate your skin, leaving you with a radiant, healthy glow.</p>
            <div class="about_actions">
                <a href="{{ Route::has('shop-page') }}" class="btn">Explore Our Collection</a>
            </div>
        </div>
    </div>

    <div class="FeaturedProducts">
        <div class="container">
            <div class="products_list">
                @php
                    $products = collect([
                        (object)['image' => 'assets/images/shea254-1.jpg', 'title' => 'SunFix Sunscreen', 'price' => 1650, 'category' => 'cream & Gels'],
                        (object)['image' => 'assets/images/shea254-2.png', 'title' => '400g Strawberry Scented Whipped Body Butter', 'price' => 1150, 'category' => 'Whipped Butters'],
                    ]);
                @endphp

                @forelse ($products as $product)
                    <div class="product_card">
                        <div class="image">
                            <img src="{{ $product->image }}" alt="{{ $product->title }}">
                        </div>

                        <div class="content">
                            <div class="extra_details">
                                <span>{{ $product->category }}</span>
                            </div>

                            <div class="details">
                                <p class="title">{{ $product->title }}</p>
                                <p class="price">Ksh. {{ number_format($product->price) }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <p>No featured products yet</p>
                @endforelse
            </div>
        </div>
    </div>

    <section class="CTA">
        <div class="container">
            <h2>Explore Our Wide Range of Products</h2>
            <p>From nourishing shea butters to rejuvenating toners and serums, our collection has something for every skin type and concern. Discover the perfect products to elevate your skincare routine. </p>

            <div class="cta_actions">
                <a href="{{ Route::has('shop-page') }}" class="btn">Explore Our Collection</a>
            </div>
        </div>
    </section>
</div>
