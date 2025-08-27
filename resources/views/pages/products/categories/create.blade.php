<x-app-layout>
    <div class="custom_form py-4 max-w-4xl mx-auto">
        <div class="header">
            <a href="{{ Route::has('product-categories.index') ? route('product-categories.index') : '#' }}" wire:navigate>
                <x-svgs.arrow-left class="w-5 h-5" />
            </a>
            <h2>New Product Category</h2>
        </div>

        <form action="{{ route('product-categories.store') }}" method="post">
            @csrf

            <div class="inputs">
                <label for="title" class="required">Title</label>
                <input type="text" name="title" id="title" autocomplete="title" value="{{ old('title') }}" autofocus>
                <x-form-input-error field="title" />
            </div>

            <div class="buttons_group">
                <button type="submit">Save Product Category</button>
                <a href="{{ Route::has('product-categories.index') ? route('product-categories.index') : '#' }}" wire:navigate class="btn btn_danger">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>

