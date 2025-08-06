@extends('layouts.app')

@section('content')
@if(session('success'))
    <div style="background-color: #dcfce7; border: 1px solid #bbf7d0; padding: 12px; border-radius: 8px; margin-bottom: 20px; margin-left: 32px;">
        <p class="success-message" style="margin-bottom: 0;">{{ session('success') }}</p>
    </div>
@endif

<article class="card">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px;">
        <div style="flex: 1;">
            <h1 style="margin-bottom: 20px; color: var(--stone-800); font-size: 32px; line-height: 1.3;">
                {{ $post->title }}
            </h1>
            
            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 25px;">
                <img src="{{ $post->user->avatar_url }}" alt="{{ $post->user->display_name }}" class="user-avatar">
                <div>
                    <strong style="color: var(--stone-800); font-family: 'Merriweather', serif; font-size: 16px;">
                        {{ $post->user->display_name }}
                    </strong>
                    <div style="color: var(--stone-500); font-size: 14px;">
                        Published {{ $post->time_ago }}
                    </div>
                </div>
            </div>
        </div>
        
        @if($post->user_id === auth()->id())
            <div style="display: flex; gap: 10px;">
                <a href="{{ route('posts.edit', $post) }}" class="btn btn-secondary">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <form method="POST" action="{{ route('posts.destroy', $post) }}" 
                      style="display: inline;" 
                      onsubmit="return confirm('Are you sure you want to delete this post?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-secondary" style="background-color: #dc2626; color: white;">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
            </div>
        @endif
    </div>

    @if($post->image)
        <div style="margin-bottom: 30px;">
            <img src="{{ $post->image_url }}" alt="{{ $post->title }}" 
                 style="width: 100%; max-height: 500px; object-fit: cover; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
        </div>
    @endif

    <div class="reading-content" style="font-size: 18px; line-height: 1.8; color: var(--stone-700);">
        {!! nl2br(e($post->content)) !!}
    </div>

    <div style="border-top: 1px solid var(--stone-200); padding-top: 25px; margin-top: 40px;">
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 20px;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <span style="color: var(--stone-500); font-size: 14px;">
                    <i class="fas fa-clock"></i> {{ $post->formatted_publish_date }}
                </span>
                @if(!$post->is_published)
                    <span style="background-color: #fbbf24; color: white; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: bold;">
                        DRAFT
                    </span>
                @endif
            </div>
            
            <div style="display: flex; gap: 12px;">
                <button class="btn btn-secondary" style="padding: 8px 16px; font-size: 14px;">
                    <i class="fas fa-heart"></i> Like
                </button>
                <button class="btn btn-secondary" style="padding: 8px 16px; font-size: 14px;">
                    <i class="fas fa-comment"></i> Comment
                </button>
                <button class="btn btn-secondary" style="padding: 8px 16px; font-size: 14px;">
                    <i class="fas fa-share"></i> Share
                </button>
            </div>
        </div>
    </div>
</article>

<div class="card">
    <h3 style="margin-bottom: 20px; color: var(--stone-700);">About the Author</h3>
    <div style="display: flex; align-items: center; gap: 20px;">
        <img src="{{ $post->user->avatar_url }}" alt="{{ $post->user->display_name }}" class="user-avatar large">
        <div>
            <h4 style="margin-bottom: 8px; color: var(--stone-800);">{{ $post->user->display_name }}</h4>
            <p style="color: var(--stone-500); margin-bottom: 8px;">{{ '@' . $post->user->username }}</p>
            @if($post->user->bio)
                <p style="color: var(--stone-600); margin-bottom: 12px;">{{ $post->user->bio }}</p>
            @endif
            <div style="display: flex; gap: 20px; color: var(--stone-500); font-size: 14px;">
                <span><strong>{{ $post->user->posts_count }}</strong> Posts</span>
                <span><strong>0</strong> Followers</span>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h3 style="margin: 0; color: var(--stone-700);">More Posts</h3>
        <a href="{{ route('posts.index') }}" class="btn btn-secondary">
            <i class="fas fa-newspaper"></i> View All Posts
        </a>
    </div>
</div>
@endsection