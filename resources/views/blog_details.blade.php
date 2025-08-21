<x-app-layout>
    @include('partials.navbar')
    <main class="Blog" style="background: linear-gradient(135deg, #f8fafc, #ffffff); padding: 4em 1em;">
    <section class="Blogs">
        <div class="blog blog_details" style="background: white; border-radius: 24px; box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05), 0 2px 4px rgba(0, 0, 0, 0.02); overflow: hidden;">
            <div class="image" style="height: 300px; overflow: hidden;">
                <img src="{{ $blog->getImageUrl() }}" alt="Blog Image" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            <div class="container" style="max-width: 800px; margin: 0 auto; padding: 2em;">
                <div class="text">
                    <span class="date" style="font-size: clamp(0.9rem, 4vw, 1rem); color: #64748b;">
                        {{ $blog->created_at->diffForHumans() }}
                    </span>
                    <h1 style="font-size: clamp(1.8rem, 5vw, 2.2rem); color: #1a237e; font-weight: 800; margin: 0.5em 0;">{{ $blog->title }}</h1>
                    <span class="content" style="font-size: clamp(0.9rem, 4vw, 1rem); color: #64748b; line-height: 1.6;">
                        {!! html_entity_decode($blog->content) !!}
                    </span>
                    <div class="btn" style="text-align: right; margin-top: 2em;">
                        <a href="{{ route('users.blogs') }}" style="display: inline-block; background: #1a237e; color: white; text-decoration: none; padding: 0.75em 1.5em; border-radius: 24px; font-size: clamp(0.9rem, 4vw, 1rem); transition: background 0.3s ease;">
                            <i class="fas fa-arrow-left" style="margin-right: 0.5rem;"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

@include('partials.footer')
</x-app-layout>