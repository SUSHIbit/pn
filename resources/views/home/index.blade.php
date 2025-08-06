@extends('layouts.app')

@section('content')
<div class="welcome-card card">
    <h2>Welcome to Social News</h2>
    <p>Hello, {{ $user->display_name }}! Your personalized news feed awaits. Share your thoughts, discover new perspectives, and connect with fellow readers.</p>
    
    <div class="action-buttons">
        <a href="{{ route('posts.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create Your First Post
        </a>
        <a href="{{ route('posts.feed') }}" class="btn btn-secondary">
            <i class="fas fa-newspaper"></i> View Feed
        </a>
        <a href="{{ route('users.discover') }}" class="btn btn-secondary">
            <i class="fas fa-users"></i> Discover People
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
                <span><strong style="color: var(--stone-700);">{{ $stats['posts_count'] }}</strong> Posts</span>
                <span><strong style="color: var(--stone-700);">{{ $user->following_count }}</strong> Following</span>
                <span><strong style="color: var(--stone-700);">{{ $user->followers_count }}</strong> Followers</span>
            </div>
        </div>
    </div>
</div>

@if($feedPosts->count() > 0)
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="margin: 0; color: var(--stone-700); display: flex; align-items: center; gap: 12px;">
            <i class="fas fa-feed" style="color: var(--stone-500);"></i>
            {{ $user->following_count > 0 ? 'Your Feed' : 'Latest Posts' }}
        </h3>
        <a href="{{ route('posts.feed') }}" class="btn btn-secondary" style="padding: 8px 16px; font-size: 14px;">
            <i class="fas fa-arrow-right"></i> View All
        </a>
    </div>
    
    @foreach($feedPosts as $post)
        @include('components.post-card', ['post' => $post, 'showAuthor' => true])
    @endforeach
</div>
@endif

@if($suggestedUsers->count() > 0)
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="margin: 0; color: var(--stone-700); display: flex; align-items: center; gap: 12px;">
            <i class="fas fa-user-plus" style="color: var(--stone-500);"></i>
            Suggested for You
        </h3>
        <a href="{{ route('users.discover') }}" class="btn btn-secondary" style="padding: 8px 16px; font-size: 14px;">
            <i class="fas fa-compass"></i> Discover More
        </a>
    </div>
    
    @foreach($suggestedUsers as $suggestedUser)
        @include('components.user-card', ['user' => $suggestedUser])
    @endforeach
</div>
@endif

@if($user->posts_count > 0)
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="margin: 0; color: var(--stone-700); display: flex; align-items: center; gap: 12px;">
            <i class="fas fa-file-alt" style="color: var(--stone-500);"></i>
            Your Recent Posts
        </h3>
        <a href="{{ route('posts.my') }}" class="btn btn-secondary" style="padding: 8px 16px; font-size: 14px;">
            <i class="fas fa-list"></i> View All
        </a>
    </div>
    
    @php
        $recentPosts = $user->posts()->with(['likes', 'comments'])->withCount(['likes', 'comments'])->orderBy('created_at', 'desc')->take(3)->get();
    @endphp
    
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
                        {{ $post->excerpt }}
                    </p>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; gap: 15px; color: var(--stone-500); font-size: 12px;">
                            <span><i class="fas fa-heart"></i> {{ $post->likes_count }}</span>
                            <span><i class="fas fa-comment"></i> {{ $post->comments_count }}</span>
                            <span><i class="fas fa-clock"></i> {{ $post->time_ago }}</span>
                            @if(!$post->is_published)
                                <span style="background-color: #fbbf24; color: white; padding: 2px 6px; border-radius: 4px; font-size: 10px;">
                                    DRAFT
                                </span>
                            @endif
                        </div>
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
</div>
@else
<div class="card">
    <div style="text-align: center; padding: 40px 20px;">
        <i class="fas fa-file-alt" style="font-size: 48px; color: var(--stone-300); margin-bottom: 16px;"></i>
        <p class="text-muted" style="font-size: 18px; margin-bottom: 24px;">No posts yet. Be the first to share something interesting!</p>
        <a href="{{ route('posts.create') }}" class="btn btn-primary">
            <i class="fas fa-pen"></i> Write Your First Post
        </a>
    </div>
</div>
@endif

@push('scripts')
<script>
// Like functionality
document.addEventListener('DOMContentLoaded', function() {
    const likeButtons = document.querySelectorAll('.like-btn');
    const followButtons = document.querySelectorAll('.follow-btn');

    // Handle like button clicks
    likeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.dataset.postId;
            const likeText = this.querySelector('.like-text');
            const likeCount = this.querySelector('.like-count');

            fetch(`/posts/${postId}/like`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                likeText.textContent = data.liked ? 'Liked' : 'Like';
                likeCount.textContent = data.likes_count;
                
                if (data.liked) {
                    this.style.backgroundColor = '#dc2626';
                    this.style.color = 'white';
                } else {
                    this.style.backgroundColor = '';
                    this.style.color = '';
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });

    // Handle follow button clicks
    followButtons.forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const followText = this.querySelector('.follow-text');
            const icon = this.querySelector('i');

            fetch(`/users/${userId}/follow`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                followText.textContent = data.following ? 'Following' : 'Follow';
                
                if (data.following) {
                    this.className = 'follow-btn btn btn-secondary';
                    icon.className = 'fas fa-user-check';
                } else {
                    this.className = 'follow-btn btn btn-primary';
                    icon.className = 'fas fa-user-plus';
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
});
</script>
@endpush
@endsection