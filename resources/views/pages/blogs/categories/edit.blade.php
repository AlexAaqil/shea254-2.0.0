<x-app-layout>
    <div class="custom_form py-4 max-w-4xl mx-auto">
        <div class="header">
            <a href="{{ Route::has('blog-categories.index') ? route('blog-categories.index') : '#' }}" wire:navigate>
                <x-svgs.arrow-left class="w-5 h-5" />
            </a>
            <h2>Update Blog Category</h2>
        </div>

        <form action="{{ route('blog-categories.update', $blog_category->id) }}" method="post">
            @csrf
            @method('PATCH')

            <div class="inputs">
                <label for="title" class="required">Title</label>
                <input type="text" name="title" id="title" autocomplete="title" value="{{ old('title', $blog_category->title) }}" autofocus>
                <x-form-input-error field="title" />
            </div>

            <div class="buttons_group">
                <button type="submit">Update Blog Category</button>
                <a href="{{ Route::has('blog-categories.index') ? route('blog-categories.index') : '#' }}" wire:navigate class="btn btn_danger">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>

