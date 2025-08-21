<x-admin>
    <section class="Comment">
        <div class="container">
            <div class="view_comment">
                <h1>
                    <span>{{ $comment->full_name }}</span>
                    <span>{{ $comment->email }}</span>
                    <span>{{ $comment->phone_number }}</span>
                </h1>
                <p>{{ $comment->message }}</p>
                <a href="{{ route('comments.index') }}">Go Back</a>
            </div>
        </div>
   </section>
</x-admin>
