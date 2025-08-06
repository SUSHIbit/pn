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
                        <a href="{{ route('user.profile', $post->user->username) }}" style="text-decoration: none; color: inherit;">
                            {{ $post->user->display_name }}
                        </a>
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
                @auth
                    @php
                        // Check if user has liked this post - query directly
                        $hasLiked = \App\Models\Like::where('user_id', auth()->id())
                                                   ->where('post_id', $post->id)
                                                   ->exists();
                        // Get likes count - query directly
                        $likesCount = \App\Models\Like::where('post_id', $post->id)->count();
                    @endphp
                    <button class="like-btn btn {{ $hasLiked ? 'btn-primary' : 'btn-secondary' }}" 
                            style="padding: 8px 16px; font-size: 14px; {{ $hasLiked ? 'background-color: #dc2626; color: white;' : '' }}" 
                            data-post-id="{{ $post->id }}">
                        <i class="fas fa-heart"></i> 
                        <span class="like-text">{{ $hasLiked ? 'Liked' : 'Like' }}</span>
                        (<span class="like-count">{{ $likesCount }}</span>)
                    </button>
                @else
                    @php
                        $likesCount = \App\Models\Like::where('post_id', $post->id)->count();
                    @endphp
                    <span class="btn btn-secondary" style="padding: 8px 16px; font-size: 14px;">
                        <i class="fas fa-heart"></i> {{ $likesCount }}
                    </span>
                @endauth
                
                @php
                    $commentsCount = \App\Models\Comment::where('post_id', $post->id)->count();
                @endphp
                <span class="btn btn-secondary" style="padding: 8px 16px; font-size: 14px;">
                    <i class="fas fa-comment"></i> {{ $commentsCount }}
                </span>
                <button class="btn btn-secondary" style="padding: 8px 16px; font-size: 14px;" onclick="navigator.share ? navigator.share({title: '{{ $post->title }}', url: '{{ route('posts.show', $post) }}'}) : copyToClipboard('{{ route('posts.show', $post) }}')">
                    <i class="fas fa-share"></i> Share
                </button>
            </div>
        </div>
    </div>
</article>

<!-- Comments Section -->
<div class="card">
    @php
        $commentsCount = \App\Models\Comment::where('post_id', $post->id)->count();
    @endphp
    <h3 style="margin-bottom: 20px; color: var(--stone-700); display: flex; align-items: center; gap: 12px;">
        <i class="fas fa-comments" style="color: var(--stone-500);"></i>
        Comments ({{ $commentsCount }})
    </h3>

    @auth
        <!-- Add Comment Form -->
        <form method="POST" action="{{ route('comments.store', $post) }}" style="margin-bottom: 30px;">
            @csrf
            <div class="form-group">
                <textarea name="content" class="form-control @error('content') error @enderror" 
                          rows="3" placeholder="Write a comment..." required></textarea>
                @error('content')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane"></i> Post Comment
            </button>
        </form>
    @else
        <div style="text-align: center; padding: 20px; background-color: var(--stone-50); border-radius: 8px; margin-bottom: 30px;">
            <p style="color: var(--stone-600); margin-bottom: 15px;">Please login to comment on this post.</p>
            <a href="{{ route('login') }}" class="btn btn-primary">Login to Comment</a>
        </div>
    @endauth

    <!-- Comments List -->
    @if($comments->count() > 0)
        @foreach($comments as $comment)
            @include('components.comment', ['comment' => $comment])
        @endforeach

        <!-- Pagination for comments -->
        @if($comments->hasPages())
            <div style="margin-top: 20px;">
                {{ $comments->links() }}
            </div>
        @endif
    @else
        <div style="text-align: center; padding: 40px; color: var(--stone-500);">
            <i class="fas fa-comment" style="font-size: 48px; margin-bottom: 15px;"></i>
            <p>No comments yet. Be the first to comment!</p>
        </div>
    @endif
</div>

<div class="card">
    <h3 style="margin-bottom: 20px; color: var(--stone-700);">About the Author</h3>
    <div style="display: flex; align-items: center; gap: 20px;">
        <img src="{{ $post->user->avatar_url }}" alt="{{ $post->user->display_name }}" class="user-avatar large">
        <div style="flex: 1;">
            <h4 style="margin-bottom: 8px; color: var(--stone-800);">{{ $post->user->display_name }}</h4>
            <p style="color: var(--stone-500); margin-bottom: 8px;">{{ '@' . $post->user->username }}</p>
            @if($post->user->bio)
                <p style="color: var(--stone-600); margin-bottom: 12px;">{{ $post->user->bio }}</p>
            @endif
            @php
                // Get user stats manually
                $userPostsCount = \App\Models\Post::where('user_id', $post->user->id)->where('is_published', true)->count();
                $userFollowersCount = \App\Models\Follow::where('following_id', $post->user->id)->count();
                $userFollowingCount = \App\Models\Follow::where('follower_id', $post->user->id)->count();
            @endphp
            <div style="display: flex; gap: 20px; color: var(--stone-500); font-size: 14px;">
                <span><strong>{{ $userPostsCount }}</strong> Posts</span>
                <span><strong>{{ $userFollowersCount }}</strong> Followers</span>
                <span><strong>{{ $userFollowingCount }}</strong> Following</span>
            </div>
        </div>
        @auth
            @if($post->user_id !== auth()->id())
                @php
                    // Check if current user is following this user
                    $isFollowing = \App\Models\Follow::where('follower_id', auth()->id())
                                                    ->where('following_id', $post->user->id)
                                                    ->exists();
                @endphp
                <button class="follow-btn btn {{ $isFollowing ? 'btn-secondary' : 'btn-primary' }}" 
                        data-user-id="{{ $post->user->id }}">
                    <i class="fas {{ $isFollowing ? 'fa-user-check' : 'fa-user-plus' }}"></i>
                    <span class="follow-text">{{ $isFollowing ? 'Following' : 'Follow' }}</span>
                </button>
            @endif
        @endauth
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const likeBtn = document.querySelector('.like-btn');
    const followBtn = document.querySelector('.follow-btn');

    // Handle like button
    if (likeBtn) {
        likeBtn.addEventListener('click', function() {
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
    }

    // Handle follow button
    if (followBtn) {
        followBtn.addEventListener('click', function() {
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
    }
});

// Copy to clipboard function for share
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Link copied to clipboard!');
    });
}
</script>
@endpush
@endsection