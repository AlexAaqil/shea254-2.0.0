<x-admin>
    <div class="container categories">
        <div class="custom_form">
            <h1>Update Product Category</h1>
            <form action="{{ route('product-categories.update', ['product_category' => $product_category->id]) }}" method="post">
                @csrf
                @method('PATCH')

                <div class="input_group">
                    <label for="title">Title</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $product_category->title) }}" required />
                    <span class="inline_alert">{{ $errors->first('title') }}</span>
                </div>

                <button type="submit">Update</button>
            </form>
        </div>
    </div>
</x-admin>
