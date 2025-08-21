<x-app-layout>
    @include('partials.navbar')
    
    <main class="Shop" style="background: linear-gradient(135deg, #f8fafc, #ffffff); overflow: hidden;">
        @include('partials.messages')

        <div class="search" style="padding: 2em 0; text-align: center;">
            <form action="{{ route('products.search') }}" method="GET" style="display: inline-flex; align-items: center; background: white; padding: 0.5em 1em; border-radius: 24px; box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05), 0 2px 4px rgba(0, 0, 0, 0.02);">
                @csrf
                <input type="text" name="query" id="query" placeholder="Search Product" style="border: none; outline: none; flex-grow: 1; font-size: 1em; color: #64748b;">
                <button type="submit" style="background: none; border: none; cursor: pointer;">
                    <i class="fa fa-search" style="color: #1a237e; font-size: 1.2em;"></i>
                </button>
            </form>
        </div>

        <div class="container categories" style="display: flex; justify-content: center; gap: 2em; margin-bottom: 3em;">
            @foreach($product_categories as $category)
                <a href="{{ route('products.categorized', $category->slug) }}" style="color: #1a237e; text-decoration: none; font-size: 1.1em; font-weight: 600; padding: 0.5em 1em; background: white; border-radius: 24px; box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05), 0 2px 4px rgba(0, 0, 0, 0.02); transition: all 0.3s;">{{ $category->title }}</a>
            @endforeach
        </div>

        <section class="shop_products" style="padding: 2em 0;">
            <div class="container" style="max-width: 1200px; margin: 0 auto;">
                <div class="card_wrapper" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2em;">
                    @foreach($products as $product)
                        @include('partials.product')
                    @endforeach
                </div>
            </div>
        </section>
    </main>

    @include('partials.footer')
</x-app-layout>