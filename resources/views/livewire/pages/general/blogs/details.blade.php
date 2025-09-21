<div class="UsersBlogsDetailsPage">
    <section class="BlogDetails">
        <div class="container">
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
                    <span class="date">{{ $blog->created_at->diffForHumans() }}</span>
                </div>

                <h1>{{ $blog->title }}</h1>

                <div class="blog_content">
                    {!! $blog->content !!}
                </div>

                <div class="action_btns">
                    <a href="{{ Route::has('users-blogs-page') ? route('users-blogs-page') : '#' }}" class="btn" wire:navigate><span class="arrow">&larr;</span> Back</a>
                </div>
            </div>
        </div>
    </section>
</div>
