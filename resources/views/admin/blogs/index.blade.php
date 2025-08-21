<x-admin>
    <section class="Blogs">
        <div class="container blogs">
            @include('admin.partials.blogs_navbar')
            
            <div class="header">
                <h1>Blogs <span>({{ $blogs->count() }})</span></h1>
                @include('partials.js_search')
                <div class="header_btn">
                    <a href="{{ route('blogs.create') }}">New</a>
                </div>
            </div>
    
            <div class="body">
                <div class="card_wrapper">
                    @if($blogs->count() != 0)
                        @foreach($blogs as $blog)
                        <div class="card blog searchable">
                            <div class="image">
                                <a href="{{ route('blogs.edit', ['blog' => $blog->id]) }}" class="title">
                                    <img src="{{ $blog->getImageUrl() }}" alt="Blog Image">
                                </a>
                            </div>
    
                            <div class="text">
                                <div class="extra_details">
                                    <span>{{ $blog->created_at->diffForHumans() }}</span>

                                    <span>{{ $blog->blog_category ? $blog->blog_category->title : 'null' }}</span>
                                </div>

                                <div class="content">
                                    <div class="details">
                                        <span class="title">
                                            <a href="{{ route('blogs.edit', ['blog' => $blog->id]) }}">
                                                {{ $blog->title }}
                                            </a>
                                        </span>

                                        <span class="content">
                                            {!! html_entity_decode(Illuminate\Support\Str::limit($blog->content, 60, ' ...')) !!}
                                        </span>
                                    </div>

                                    <div class="actions">
                                        <div class="action">
                                            <form id="deleteForm_{{ $blog->id }}" action="{{ route('blogs.destroy', ['blog' => $blog->id]) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
        
                                                <a href="javascript:void(0);" onclick="deleteItem({{ $blog->id }}, 'blog');">
                                                    <i class="fas fa-trash-alt delete"></i>
                                                </a>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <h3>There's no availabe blogs.</h3>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <x-sweetalert></x-sweetalert>
</x-admin>
