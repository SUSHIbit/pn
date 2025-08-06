@extends('layouts.app')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="margin: 0; color: var(--stone-800);">My Posts</h2>
        <a href="{{ route('posts.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create New Post
        </a>
    </div>

    @if($posts->count() > 0)
        <div style="display: flex; gap: 20px; margin-bottom: 25px; border-bottom: 1px solid var(--stone-200); padding-bottom: 15px;">
            <div style="color: var(--stone-600);">
                <strong>{{ $posts->total() }}</strong> total posts
            </div>
            <div style="color: var(--stone-600);">
                <strong>{{ $posts->where('is_published', true)->count() }}</strong> published
            </div>
            <div style="color: var(--stone-600);">
                <strong>{{ $posts->where('is_published', false)->count() }}</strong> drafts
            </div>
        </div>

        @foreach($posts as $post)
            <article class="post-card" style="border: 1px solid var(--stone-200); border-radius: 12px; padding: 24px; margin-bottom: 20px; position: relative;">
                @if(!$post->is_published)
                    <div style="position: absolute; top: 15px; right: 15px; background-color: #fbbf24; color: white; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: bold;">
                        DRAFT
                    </div>
                @endif

                <div style="display: flex; gap: 20px;">
                    @if($post->image)
                        <div style="flex-shrink: 0;">
                            <img src="{{ $post->image_url }}" alt="{{ $post->title }}" 
                                 style="width: 120px; height: 80px; object-fit: cover; border-radius: 8px;">
                        </div>
                    @endif
                    
                    <div style="flex: 1;">
                        <h3 style="margin-bottom: 10px;">
                            <a href="{{ route('posts.show', $post) }}" 
                               style="text-decoration: none; color: var(--stone-800);">
                                {{ $post->title }}
                            </a>
                        </h3>

                        <p style="color: var(--stone-600); margin-bottom: 15px; line-height: 1.6;">
                            {{ $post->excerpt }}
                        </p>

                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div style="display: flex; gap: 15px; color: var(--stone-500); font-size: 14px;">
                                <span>
                                    <i class="fas fa-clock"></i> {{ $post->time_ago }}
                                </span>
                                <span>
                                    <i class="fas fa-eye"></i> 0 views
                                </span>
                            </div>
                            
                            <div style="display: flex; gap: 10px;">
                                <a href="{{ route('posts.show', $post) }}" class="btn btn-secondary" style="padding: 6px 12px; font-size: 14px;">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="{{ route('posts.edit', $post) }}" class="btn btn-secondary" style="padding: 6px 12px; font-size: 14px;">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form method="POST" action="{{ route('posts.destroy', $post) }}" 
                                      style="display: inline;" 
                                      onsubmit="return confirm('Are you sure you want to delete this post?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-secondary" style="background-color: #dc2626; color: white; padding: 6px 12px; font-size: 14px;">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
        @endforeach

        <!-- Pagination -->
        <div style="display: flex; justify-content: center; margin-top: 30px;">
            {{ $posts->links() }}
        </div>
    @else
        <div style="text-align: center; padding: 60px 20px;">
            <i class="fas fa-file-alt" style="font-size: 64px; color: var(--stone-300); margin-bottom: 20px;"></i>
            <h3 style="color: var(--stone-600); margin-bottom: 15px;">No posts yet</h3>
            <p class="text-muted">Start writing and sharing your stories with the world!</p>
            <a href="{{ route('posts.create') }}" class="btn btn-primary" style="margin-top: 20px;">
                <i class="fas fa-plus"></i> Create Your First Post
            </a>
        </div>
    @endif
</div>
@endsection