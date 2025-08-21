<x-admin>

    <x-slot name='extra_head'>
        <script src="https://cdn.tiny.cloud/1/44zgxk9rangxvjd8no219bfrmnon247y18bl2fylec4zpa55/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    </x-slot>
    
    <div class="container Blogs">
        <div class="custom_form">
            <h1>Update Blog</h1>
            <form action="{{ route('blogs.update', ['blog' => $blog->id]) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="row_input_group">
                    <div class="input_group">
                        <label for="category_id">Blog Category<span>*</span></label>
                        <select name="category_id" id="category_id">
                            <option value="">Select Blog Category</option>
                            @foreach($blog_categories as $category)
                                <option value="{{ $category->id }}" {{ (old('category_id', $blog->category_id) == $category->id) ? 'selected' : '' }}>
                                    {{ $category->title }}
                                </option>
                            @endforeach
                        </select>
                        <span class="inline_alert">{{ $errors->first('category_id') }}</span>
                    </div>

                    <div class="blog_image_input">
                        <div class="input_group">
                            <label for="image">Thumbnail</label>
                            <input type="file" name="image" id="image" />
                            <span class="inline_alert">{{ $errors->first('image') }}</span>
                        </div>
    
                        <div class="blog_image">
                            <img src="{{ $blog->getImageUrl() }}" alt="Blog Image">
                        </div>
                    </div>
                </div>

                <div class="input_group">
                    <label for="title">Title<span class="text-danger">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title', $blog->title) }}" placeholder="Title" autofocus />
                    <span class="inline_alert">{{ $errors->first('title') }}</span>
                </div>

                <div class="input_group">
                    <label for="content">Content<span class="text-danger">*</span></label>
                    <textarea name="content" id="content" class="tinymiced" rows="7" placeholder="Enter the Blog's Content">{{ old('content', $blog->content) }}</textarea>
                    @error('content')
                        <span class="inline_alert">{{ $errors->first('content') }}</span>
                    @enderror
                </div>

                <button type="submit">Update</button>
            </form>
        </div>
    </div>

    <x-slot name='javascript'>
        <script src="{{ asset('assets/js/tinymce.js') }}"></script>
    </x-slot>
</x-admin>