@extends('layouts.app')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="margin: 0; color: var(--stone-800);">Latest News</h2>
        <a href="{{ route('posts.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create Post
        </a>
    </div>

    @if($posts->count() > 0)
        @foreach($posts as $post)
            <article class="post-card" style="border-bottom: 1px solid var(--stone-200); padding-bottom: 30px; margin-bottom: 30px;">
                @if($post->image)
                    <div style="margin-bottom: 20px;">
                        <img src="{{ $post->image_url }}" alt="{{ $post->title }}" 
                             style="width: 100%; max-height: 400px; object-fit: cover; border-radius: 12px;">
                    </div>
                @endif

                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px;">
                    <img src="{{ $post->user->avatar_url }}" alt="{{ $post->user->display_name }}" class="user-avatar">
                    <div>
                        <strong style="color: var(--stone-800); font-family: 'Merriweather', serif;">
                            {{ $post->user->display_name }}
                        </strong>
                        <div style="color: var(--stone-500); font-size: 14px;">
                            {{ $post->time_ago }}
                        </div>
                    </div>
                </div>

                <h3 style="margin-bottom: 15px;">
                    <a href="{{ route('posts.show', $post) }}" 
                       style="text-decoration: none; color: var(--stone-800);">
                        {{ $post->title }}
                    </a>
                </h3>

                <p style="color: var(--stone-600); line-height: 1.7; margin-bottom: 15px;">
                    {{ $post->excerpt }}
                </p>

                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <a href="{{ route('posts.show', $post) }}" class="btn btn-secondary">
                        Read More
                    </a>
                    
                    @if($post->user_id === auth()->id())
                        <div style="display: flex; gap: 10px;">
                            <a href="{{ route('posts.edit', $post) }}" class="btn btn-secondary" style="padding: 8px 16px; font-size: 14px;">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('posts.destroy', $post) }}" 
                                  style="display: inline;" 
                                  onsubmit="return confirm('Are you sure you want to delete this post?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-secondary" style="background-color: #dc2626; color: white; padding: 8px 16px; font-size: 14px;">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </article>
        @endforeach

        <!-- Pagination -->
        <div style="display: flex; justify-content: center; margin-top: 30px;">
            {{ $posts->links() }}
        </div>
    @else
        <div style="text-align: center; padding: 60px 20px;">
            <i class="fas fa-newspaper" style="font-size: 64px; color: var(--stone-300); margin-bottom: 20px;"></i>
            <h3 style="color: var(--stone-600); margin-bottom: 15px;">No posts yet</h3>
            <p class="text-muted">Be the first to share something interesting!</p>
            <a href="{{ route('posts.create') }}" class="btn btn-primary" style="margin-top: 20px;">
                <i class="fas fa-plus"></i> Create Your First Post
            </a>
        </div>
    @endif
</div>
@endsection