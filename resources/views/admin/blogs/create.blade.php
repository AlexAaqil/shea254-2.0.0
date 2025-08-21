<x-admin>

    <x-slot name='extra_head'>
        <script src="https://cdn.tiny.cloud/1/44zgxk9rangxvjd8no219bfrmnon247y18bl2fylec4zpa55/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    </x-slot>

    <div class="container">
        <div class="custom_form">
            <h1>Add Blog</h1>
            <form action="{{ route('blogs.store') }}" method="post" enctype="multipart/form-data">
                @csrf

                <div class="row_input_group">
                    <div class="input_group">
                        <label for="category_id">Category</label>
                        <select name="category_id" id="category_id">
                            <option value="">Select Blog Category</option>
                            @foreach($blog_categories as $category)
                                <option value="{{ $category->id }}">
                                    {{ $category->title }}
                                </option>
                            @endforeach
                        </select>  
                        <span class="inline_alert">{{ $errors->first('category_id') }}</span>                      
                    </div>

                    <div class="input_group">
                        <label for="image">Thumbnail</label>
                        <input type="file" name="image" id="image" />
                        <span class="inline_alert">{{ $errors->first('image') }}</span>
                    </div>
                </div>

                <div class="input_group">
                    <label for="title">Title<span class="text-danger">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" placeholder="Title" autofocus />
                    <span class="inline_alert">{{ $errors->first('title') }}</span>
                </div>

                <div class="input_group">
                    <label for="content">Content<span class="text-danger">*</span></label>
                    <textarea name="content" id="content" class="tinymiced" rows="7" placeholder="Enter the Blog's Content">{{ old('content') }}</textarea>
                    @error('content')
                        <span class="inline_alert">{{ $errors->first('content') }}</span>
                    @enderror
                </div>

                <button type="submit">Save</button>
            </form>
        </div>
    </div>

    <x-slot name='javascript'>
        <script src="{{ asset('assets/js/tinymce.js') }}"></script>
    </x-slot>
</x-admin>
