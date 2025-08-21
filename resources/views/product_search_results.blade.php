<x-app-layout>
    @include('partials.navbar')
    <main class="Categorised_Products" style="background: linear-gradient(135deg, #f8fafc, #ffffff); overflow: hidden; padding: 4em 1em;">
        @include('partials.messages')
        <div class="hero" style="background: linear-gradient(135deg, #f8fafc, #ffffff); padding: 5em 2em; border-radius: 24px; box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05), 0 2px 4px rgba(0, 0, 0, 0.02); margin-bottom: 4em;">
        <div class="breadcrumb" style="font-size: clamp(0.9rem, 4vw, 1rem); color: #64748b; margin-bottom: 1.5em;">
            <a href="{{ route('shop') }}" style="color: #64748b; text-decoration: none; transition: color 0.3s ease;">Shop</a> / {{ $query }}
        </div>
        <h1 style="font-size: clamp(2.2rem, 6vw, 3rem); color: #1a237e; font-weight: 800; margin-bottom: 0.5em;">{{ $query }}</h1>
        <p style="color: #64748b; font-size: clamp(1rem, 4.5vw, 1.2rem); max-width: 800px; margin: 0 auto;"><strong>{{ $products->count() }}</strong> products matched your query.</p>
    </div>

    <div class="product_search_results container" style="max-width: 1200px; margin: 0 auto;">
        <div class="products_wrapper" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2em;">
            @foreach($products as $product)
                @include('partials.product')
            @endforeach
        </div>
    </div>
</main>
</x-app-layout>