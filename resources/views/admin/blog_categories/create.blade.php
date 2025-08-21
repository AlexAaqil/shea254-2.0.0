<x-admin>
    <div class="container categories">
        <div class="custom_form">
            <h1>Add Blog Category</h1>
            <form action="{{ route('blog-categories.store') }}" method="post">
                @csrf

                <div class="input_group">
                    <label for="title">Title</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required autofocus />
                    <span class="inline_alert">{{ $errors->first('title') }}</span>
                </div>

                <button type="submit">Save</button>
            </form>
        </div>
    </div>
</x-admin>
