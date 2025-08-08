@extends('layouts.app')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="margin: 0; color: var(--stone-800); font-size: 36px; margin-bottom: 10px;">
                #{{ $hashtagModel->name }}
            </h1>
            <p style="color: var(--stone-600); margin: 0;">
                {{ $hashtagModel->posts_count }} post{{ $hashtagModel->posts_count !== 1 ? 's' : '' }} using this hashtag
            </p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('search') }}" class="btn btn-secondary">
                <i class="fas fa-search"></i> Back to Search
            </a>
            <button id="follow-hashtag-btn" class="btn btn-primary" data-hashtag="{{ $hashtagModel->name }}">
                <i class="fas fa-bell"></i> Follow Hashtag
            </button>
        </div>
    </div>

    <!-- Hashtag Stats -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; padding: 20px; background-color: var(--stone-50); border-radius: 12px;">
        <div style="text-align: center;">
            <div style="font-size: 24px; font-weight: 700; color: var(--stone-800); margin-bottom: 5px;">
                {{ $hashtagModel->posts_count }}
            </div>
            <div style="color: var(--stone-600); font-size: 14px;">Total Posts</div>
        </div>
        <div style="text-align: center;">
            <div style="font-size: 24px; font-weight: 700; color: var(--stone-800); margin-bottom: 5px;">
                {{ $posts->where('published_at', '>=', now()->subDays(7))->count() }}
            </div>
            <div style="color: var(--stone-600); font-size: 14px;">This Week</div>
        </div>
        <div style="text-align: center;">
            <div style="font-size: 24px; font-weight: 700; color: var(--stone-800); margin-bottom: 5px;">
                {{ $posts->where('published_at', '>=', now()->subDay())->count() }}
            </div>
            <div style="color: var(--stone-600); font-size: 14px;">Today</div>
        </div>
        <div style="text-align: center;">
            <div style="font-size: 24px; font-weight: 700; color: var(--stone-800); margin-bottom: 5px;">
                {{ $hashtagModel->created_at->format('M Y') }}
            </div>
            <div style="color: var(--stone-600); font-size: 14px;">First Used</div>
        </div>
    </div>

    <!-- Create Post with Hashtag -->
    <div style="background-color: var(--stone-50); padding: 20px; border-radius: 12px; margin-bottom: 30px; text-align: center;">
        <h3 style="color: var(--stone-700); margin-bottom: 10px;">Join the Conversation</h3>
        <p style="color: var(--stone-600); margin-bottom: 20px;">
            Share your thoughts and stories using #{{ $hashtagModel->name }}
        </p>
        <a href="{{ route('posts.create') }}?hashtag={{ $hashtagModel->name }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create Post with #{{ $hashtagModel->name }}
        </a>
    </div>
</div>

<!-- Posts using this hashtag -->
@if($posts->count() > 0)
    <div class="card">
        <h3 style="margin-bottom: 20px; color: var(--stone-700);">Latest Posts</h3>
        
        @foreach($posts as $post)
            @include('components.post-card', ['post' => $post, 'showAuthor' => true])
        @endforeach

        <!-- Pagination -->
        @if($posts->hasPages())
            <div style="display: flex; justify-content: center; margin-top: 30px;">
                {{ $posts->links() }}
            </div>
        @endif
    </div>
@else
    <div class="card">
        <div style="text-align: center; padding: 60px 20px;">
            <i class="fas fa-hashtag" style="font-size: 64px; color: var(--stone-300); margin-bottom: 20px;"></i>
            <h3 style="color: var(--stone-600); margin-bottom: 15px;">No posts yet</h3>
            <p class="text-muted" style="margin-bottom: 20px;">
                Be the first to create a post using #{{ $hashtagModel->name }}!
            </p>
            <a href="{{ route('posts.create') }}?hashtag={{ $hashtagModel->name }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create First Post
            </a>
        </div>
    </div>
@endif

<!-- Related Hashtags -->
@if($relatedHashtags->count() > 0)
    <div class="card">
        <h3 style="margin-bottom: 20px; color: var(--stone-700); display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-tags" style="color: var(--stone-500);"></i>
            Related Hashtags
        </h3>
        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
            @foreach($relatedHashtags as $related)
                <a href="{{ route('search.hashtag', $related->name) }}" 
                   class="hashtag-tag"
                   style="background-color: var(--stone-100); color: var(--stone-700); padding: 8px 15px; border-radius: 20px; text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.2s;">
                    #{{ $related->name }}
                    <span style="color: var(--stone-500); margin-left: 5px;">({{ $related->posts_count }})</span>
                </a>
            @endforeach
        </div>
    </div>
@endif

<!-- Trending in Category -->
<div class="card">
    <h3 style="margin-bottom: 20px; color: var(--stone-700); display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-fire" style="color: #f59e0b;"></i>
        Trending Now
    </h3>
    <p style="color: var(--stone-600); margin-bottom: 20px;">
        Discover more popular hashtags and join trending conversations.
    </p>
    <div style="display: flex; gap: 15px; flex-wrap: wrap;">
        <a href="{{ route('search') }}" class="btn btn-secondary">
            <i class="fas fa-search"></i> Explore All
        </a>
        <a href="{{ route('posts.index') }}" class="btn btn-secondary">
            <i class="fas fa-newspaper"></i> Latest Posts
        </a>
        <a href="{{ route('users.discover') }}" class="btn btn-secondary">
            <i class="fas fa-users"></i> Find People
        </a>
    </div>
</div>

@push('styles')
<style>
.hashtag-tag:hover {
    background-color: var(--stone-200);
    transform: translateY(-1px);
}

.hashtag-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 20px;
    background-color: var(--stone-50);
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 30px;
}

.stat-item {
    text-align: center;
}

.stat-number {
    font-size: 28px;
    font-weight: 700;
    color: var(--stone-800);
    margin-bottom: 5px;
}

.stat-label {
    color: var(--stone-600);
    font-size: 14px;
}

.activity-chart {
    height: 200px;
    background-color: var(--stone-50);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--stone-500);
    font-style: italic;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle follow hashtag (placeholder functionality)
    const followBtn = document.getElementById('follow-hashtag-btn');
    if (followBtn) {
        followBtn.addEventListener('click', function() {
            const hashtag = this.dataset.hashtag;
            const icon = this.querySelector('i');
            
            // Toggle follow state (this would connect to backend in full implementation)
            if (this.textContent.trim().includes('Follow')) {
                this.innerHTML = '<i class="fas fa-bell-slash"></i> Unfollow Hashtag';
                this.className = 'btn btn-secondary';
                
                // Show success message
                const message = document.createElement('div');
                message.style.cssText = 'position: fixed; top: 20px; right: 20px; background: #10b981; color: white; padding: 10px 20px; border-radius: 8px; z-index: 1000;';
                message.textContent = `Now following #${hashtag}`;
                document.body.appendChild(message);
                
                setTimeout(() => message.remove(), 3000);
            } else {
                this.innerHTML = '<i class="fas fa-bell"></i> Follow Hashtag';
                this.className = 'btn btn-primary';
            }
        });
    }

    // Social interaction handlers
    const likeButtons = document.querySelectorAll('.like-btn');
    const followButtons = document.querySelectorAll('.follow-btn');

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