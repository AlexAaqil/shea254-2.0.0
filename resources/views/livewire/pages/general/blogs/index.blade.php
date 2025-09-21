<div class="UsersBlogsPage">
    <section class="Hero">
        <div class="container">
            <div class="breadcrumbs">
                <a href="{{ Route::has('home-page') ? route('home-page') : '#' }}" wire:navigate>Home</a>
                <span>Blog</span>
            </div>
            <h1>Insights & Updates</h1>
            <p>Latest news, guides and insights</p>
        </div>
    </section>

    <section class="UsersBlogs">
        <div class="container">
            @if($blogs->count() > 0)
                <h2>Recent Posts</h2>

                <div class="blogs_list custom_cards">
                    @foreach($blogs as $blog)
                        <div class="blog_card card">
                            <div class="image">
                                <div class="skeleton"></div>
                                
                                @if ($blog->image_url)
                                    <img 
                                        src="{{ $blog->image_url }}" 
                                        alt="{{ $blog->title }}" 
                                        loading="lazy"
                                        onload="this.classList.add('loaded'); this.previousElementSibling.remove();"
                                    >
                                @else
                                    <div class="fallback_avatar">
                                        {{ strtoupper(substr($blog->title, 0, 1)) }}
                                    </div>
                                @endif
                            </div>

                            <div class="content">
                                <div class="extras">
                                    <span class="date">{{ $blog->created_at->format('F d, Y') }}</span>
                                    @if($blog->content)
                                        @php
                                            // Calculate the word count
                                            $wordCount = str_word_count(strip_tags($blog->content));
                                            $readingTime = ceil($wordCount / 200); // Assume 200 words per minute
                                        @endphp
                                        <span class="read-time">{{ $readingTime }} min read</span>
                                    @endif
                                </div>

                                <div class="info">
                                    <p class="title">
                                        <a href="{{ Route::has('users-blogs-details-page') ? route('users-blogs-details-page', $blog->slug) : '#' }}" wire:navigate>{{ Str::title($blog->title) }}</a>
                                    </p>

                                    <p class="blog_content">{!! Str::words($blog->content, 12) !!}</p>

                                    <a href="{{ Route::has('users-blogs-details-page') ? route('users-blogs-details-page', $blog->slug) : '#' }}" wire:navigate>Read More &rarr;</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pagination mt-6">
                    {{ $blogs->links() }}
                </div>
            @else
                <div class="no_blogs">
                    <p>No blogs yet</p>
                    <p>Check back later for new content</p>
                </div>
            @endif
        </div>
    </section>
</div>
