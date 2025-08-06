@extends('layouts.app')

@section('content')
<div class="welcome-card card">
    <h2>Welcome to Social News</h2>
    <p>Hello, {{ $user->display_name }}! Your personalized news feed awaits. Share your thoughts, discover new perspectives, and connect with fellow readers.</p>
    
    <div class="action-buttons">
        <a href="{{ route('posts.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create Your First Post
        </a>
        <a href="{{ route('posts.index') }}" class="btn btn-secondary">
            <i class="fas fa-newspaper"></i> Browse All Posts
        </a>
        <a href="{{ route('settings') }}" class="btn btn-secondary">
            <i class="fas fa-user-edit"></i> Complete Profile
        </a>
    </div>
</div>

<div class="card">
    <h3 style="margin-bottom: 20px; color: var(--stone-700); display: flex; align-items: center; gap: 12px;">
        <i class="fas fa-user-circle" style="color: var(--stone-500);"></i>
        Your Profile
    </h3>
    <div style="display: flex; align-items: center; gap: 24px;">
        <img src="{{ $user->avatar_url }}" alt="Profile" class="user-avatar profile">
        <div>
            <h4 style="margin-bottom: 8px; color: var(--stone-800); font-size: 20px;">{{ $user->display_name }}</h4>
            <p style="margin-bottom: 8px; color: var(--stone-500); font-size: 16px;">{{ '@' . $user->username }}</p>
            @if($user->bio)
                <p style="color: var(--stone-600); margin-bottom: 15px; font-size: 16px; line-height: 1.6;">{{ $user->bio }}</p>
            @else
                <p style="color: var(--stone-400); margin-bottom: 15px; font-style: italic; font-size: 16px;">Add your bio in settings to tell others about yourself</p>
            @endif
            <div style="display: flex; gap: 20px; color: var(--stone-500);">
                <span><strong style="color: var(--stone-700);">{{ $user->posts()->count() }}</strong> Posts</span>
                <span><strong style="color: var(--stone-700);">0</strong> Following</span>
                <span><strong style="color: var(--stone-700);">0</strong> Followers</span>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="margin: 0; color: var(--stone-700); display: flex; align-items: center; gap: 12px;">
            <i class="fas fa-newspaper" style="color: var(--stone-500);"></i>
            Your Recent Posts
        </h3>
    </div>
    
    @php
        $recentPosts = $user->posts()->orderBy('created_at', 'desc')->take(3)->get();
    @endphp
    
    @if($recentPosts->count() > 0)
        <div style="margin-bottom: 15px;">
            <a href="{{ route('posts.my') }}" class="btn btn-secondary" style="padding: 8px 16px; font-size: 14px;">
                <i class="fas fa-list"></i> View All My Posts
            </a>
        </div>
        
        @foreach($recentPosts as $post)
            <div style="border-bottom: 1px solid var(--stone-200); padding-bottom: 20px; margin-bottom: 20px;">
                <div style="display: flex; gap: 15px;">
                    @if($post->image)
                        <img src="{{ asset('uploads/posts/' . $post->image) }}" alt="{{ $post->title }}" 
                             style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; flex-shrink: 0;">
                    @endif
                    <div style="flex: 1;">
                        <h4 style="margin-bottom: 8px;">
                            <a href="{{ route('posts.show', $post) }}" style="text-decoration: none; color: var(--stone-800);">
                                {{ $post->title }}
                            </a>
                        </h4>
                        <p style="color: var(--stone-600); margin-bottom: 8px; font-size: 14px; line-height: 1.5;">
                            @if(strlen($post->content) > 100)
                                {{ substr($post->content, 0, 100) }}...
                            @else
                                {{ $post->content }}
                            @endif
                        </p>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <small style="color: var(--stone-500);">
                                {{ $post->created_at->diffForHumans() }}
                                @if(!$post->is_published)
                                    <span style="background-color: #fbbf24; color: white; padding: 2px 6px; border-radius: 4px; font-size: 10px; margin-left: 8px;">
                                        DRAFT
                                    </span>
                                @endif
                            </small>
                            <div style="display: flex; gap: 8px;">
                                <a href="{{ route('posts.edit', $post) }}" style="color: var(--stone-500); font-size: 12px;">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div style="text-align: center; padding: 40px 20px;">
            <i class="fas fa-file-alt" style="font-size: 48px; color: var(--stone-300); margin-bottom: 16px;"></i>
            <p class="text-muted" style="font-size: 18px; margin-bottom: 24px;">No posts yet. Be the first to share something interesting!</p>
            <a href="{{ route('posts.create') }}" class="btn btn-primary">
                <i class="fas fa-pen"></i> Write Your First Post
            </a>
        </div>
    @endif
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="margin: 0; color: var(--stone-700); display: flex; align-items: center; gap: 12px;">
            <i class="fas fa-globe" style="color: var(--stone-500);"></i>
            Latest from Community
        </h3>
        <a href="{{ route('posts.index') }}" class="btn btn-secondary" style="padding: 8px 16px; font-size: 14px;">
            <i class="fas fa-arrow-right"></i> See All
        </a>
    </div>
    
    @php
        $latestPosts = App\Models\Post::with('user')
            ->where('is_published', true)
            ->where('user_id', '!=', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    @endphp

    @if($latestPosts->count() > 0)
        @foreach($latestPosts as $post)
            <div style="border-bottom: 1px solid var(--stone-200); padding-bottom: 15px; margin-bottom: 15px;">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 10px;">
                    <img src="{{ $post->user->avatar_url }}" alt="{{ $post->user->display_name }}" 
                         style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                    <div>
                        <strong style="color: var(--stone-800); font-size: 14px;">{{ $post->user->display_name }}</strong>
                        <small style="color: var(--stone-500); margin-left: 8px;">{{ $post->created_at->diffForHumans() }}</small>
                    </div>
                </div>
                <h4 style="margin-bottom: 5px; font-size: 16px;">
                    <a href="{{ route('posts.show', $post) }}" style="text-decoration: none; color: var(--stone-800);">
                        {{ $post->title }}
                    </a>
                </h4>
                <p style="color: var(--stone-600); font-size: 14px; line-height: 1.5;">
                    @if(strlen($post->content) > 120)
                        {{ substr($post->content, 0, 120) }}...
                    @else
                        {{ $post->content }}
                    @endif
                </p>
            </div>
        @endforeach
    @else
        <p style="color: var(--stone-500); text-align: center; padding: 20px;">No community posts yet.</p>
    @endif
</div>
@endsection