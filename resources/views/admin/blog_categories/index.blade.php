<x-admin>
    <section class="Blogs">
        <div class="container categories">
            @include('admin.partials.blogs_navbar')
    
            <div class="header">
                <h1>Blog Categories <span>({{ $categories->count() }})</span></h1>
                @include('partials.js_search')
                <div class="header_btn">
                    <a href="{{ route('blog-categories.create') }}">New</a>
                </div>
            </div>
    
            <div class="body">
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Slug</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($categories->count() != 0)
                            @foreach($categories as $value)
                            <tr class="searchable">
                                <td>{{ $value->title }}</td>
                                <td>{{ $value->slug }}</td>
                                <td class="actions">
                                        <div class="action">
                                        <a href="{{ route('blog-categories.edit', ['blog_category'=>$value->id]) }}">
                                            <i class="fas fa-pencil-alt update"></i>
                                        </a>
                                    </div>
                                    <div class="action">
                                        <form id="deleteForm_{{ $value->id }}" action="{{ route('blog-categories.destroy', ['blog_category' => $value->id]) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
        
                                            <button type="button" onclick="deleteItem({{ $value->id }}, 'blog category');">
                                                <i class="fas fa-trash-alt delete"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="3">No available blog categories</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <x-sweetalert></x-sweetalert>
</x-admin>
