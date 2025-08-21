<x-app-layout>
@include('partials.navbar')

<main class="blog-main">
    <section class="blog-hero">
        <div class="hero-content">
            <div class="breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span class="separator">/</span>
                <a href="{{ route('users.blogs') }}">Blog</a>
            </div>
            <h1>Insights & Updates</h1>
            <p class="hero-subtitle">Latest news, guides and insights</p>
        </div>
    </section>

    {{-- Add error handling --}}
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <section class="blog-grid">
        <div class="container">
            @if($blogs->count() > 0)
                <div class="blog-header">
                    <h2>Recent Posts</h2>
                </div>

                <div class="posts-grid">
                    @foreach($blogs as $blog)
                    <article class="blog-card">
                        <div class="card-image">
                            <a href="{{ route('blogs.show', ['slug' => $blog->slug]) }}">
                                <img src="{{ $blog->getImageUrl() }}" alt="{{ $blog->title }}">
                            </a>
                        </div>
                        <div class="card-content">
                            <div class="card-meta">
                                <span class="date">{{ $blog->created_at->format('F d, Y') }}</span>
                                {{-- Calculate the reading time based on word count --}}
                                @if($blog->content)
                                    @php
                                        // Calculate the word count
                                        $wordCount = str_word_count(strip_tags($blog->content));
                                        $readingTime = ceil($wordCount / 200); // Assume 200 words per minute
                                    @endphp
                                    <span class="read-time">{{ $readingTime }} min read</span>
                                @endif
                            </div>
                            <h3>
                                <a href="{{ route('blogs.show', ['slug' => $blog->slug]) }}">
                                    {{ $blog->title }}
                                </a>
                            </h3>
                            {{-- Add null check for content --}}
                            @if($blog->content)
                                <p>{!! Str::limit(strip_tags($blog->content), 150, ' ...') !!}</p>
                            @endif
                            <div class="card-footer">
                                <a href="{{ route('blogs.show', ['slug' => $blog->slug]) }}" class="read-more">
                                    Read More
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                    @endforeach
                </div>

                @if($blogs->hasPages())
                    <div class="pagination">
                        @if ($blogs->previousPageUrl())
                            <a href="{{ $blogs->previousPageUrl() }}" class="page-btn prev">Previous</a>
                        @else
                            <span class="page-btn prev disabled">Previous</span>
                        @endif

                        <div class="page-numbers">
                            @for ($i = 1; $i <= $blogs->lastPage(); $i++)
                                <a href="{{ $blogs->url($i) }}" 
                                   class="page-number {{ $blogs->currentPage() == $i ? 'active' : '' }}">
                                    {{ $i }}
                                </a>
                            @endfor
                        </div>

                        @if ($blogs->nextPageUrl())
                            <a href="{{ $blogs->nextPageUrl() }}" class="page-btn next">Next</a>
                        @else
                            <span class="page-btn next disabled">Next</span>
                        @endif
                    </div>
                @endif
            @else
                <div class="no-posts">
                    <h2>No Posts Available</h2>
                    <p>Check back later for new content</p>
                </div>
            @endif
        </div>
    </section>
</main>

@include('partials.footer')
</x-app-layout>
