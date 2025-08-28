<x-app-layout>
    <div class="custom_form py-4 max-w-4xl mx-auto">
        <div class="header">
            <a href="{{ Route::has('blogs.index') ? route('blogs.index') : '#' }}" wire:navigate>
                <x-svgs.arrow-left class="w-5 h-5" />
            </a>
            <h2>Update Blog</h2>
        </div>

        <form action="{{ route('blogs.update', $blog->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="inputs_group">
                <div class="inputs">
                    <label for="title" class="required">Title</label>
                    <input type="text" name="title" id="title" autocomplete="title" value="{{ old('title', $blog->title) }}" autofocus>
                    <x-form-input-error field="title" />
                </div>

                <div class="inputs">
                    <label for="category_id">Blog Category</label>
                    <select name="category_id" id="category_id">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $blog->category_id) == $category->id ? 'selected' : '' }}>{{ $category->title }}</option>
                        @endforeach
                    </select>
                    <x-form-input-error field="category_id" />
                </div>
            </div>

            <div class="inputs_group">
                <div class="inputs">
                    <label for="image">Image (Must be < 2MB)</label>
                    <input type="file" name="image" id="image" accept=".png, .jpg, .jpeg, .webp, .svg" />
                    <x-form-input-error field="image" />
                </div>
            </div>

            <div class="inputs">
                <label for="content" class="required">Blog Content</label>
                <textarea name="content" id="ckeditor" cols="30" rows="10">{{ old('content', $blog->content) }}</textarea>
                <x-form-input-error field="content" />
            </div>

            <div class="buttons_group">
                <button type="submit">Update Blog</button>
                <a href="{{ Route::has('blogs.index') ? route('blogs.index') : '#' }}" wire:navigate class="btn btn_danger">Cancel</a>
            </div>
        </form>
    </div>

    @push('scripts')
        <x-ckeditor />
    @endpush
</x-app-layout>

