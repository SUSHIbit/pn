@extends('layouts.app')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="margin: 0; color: var(--stone-800);">
            {{ auth()->user()->following_count > 0 ? 'Your Feed' : 'Discover Posts' }}
        </h2>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('posts.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create Post
            </a>
            <a href="{{ route('users.discover') }}" class="btn btn-secondary">
                <i class="fas fa-user-plus"></i> Find People
            </a>
        </div>
    </div>

    @if(auth()->user()->following_count === 0)
        <div style="text-align: center; padding: 40px 20px; background-color: var(--stone-50); border-radius: 12px; margin-bottom: 30px;">
            <i class="fas fa-users" style="font-size: 48px; color: var(--stone-400); margin-bottom: 16px;"></i>
            <h3 style="color: var(--stone-700); margin-bottom: 12px;">Build Your Network</h3>
            <p style="color: var(--stone-600); margin-bottom: 20px;">
                You're not following anyone yet. Follow other users to see their posts in your personalized feed.
            </p>
            <a href="{{ route('users.discover') }}" class="btn btn-primary">
                <i class="fas fa-compass"></i> Discover People to Follow
            </a>
        </div>
    @endif

    @if($posts->count() > 0)
        @foreach($posts as $post)
            @include('components.post-card', ['post' => $post, 'showAuthor' => true])
        @endforeach

        <!-- Pagination -->
        <div style="display: flex; justify-content: center; margin-top: 30px;">
            {{ $posts->links() }}
        </div>
    @else
        <div style="text-align: center; padding: 60px 20px;">
            <i class="fas fa-newspaper" style="font-size: 64px; color: var(--stone-300); margin-bottom: 20px;"></i>
            <h3 style="color: var(--stone-600); margin-bottom: 15px;">No posts in your feed</h3>
            <p class="text-muted">
                {{ auth()->user()->following_count > 0 ? 'The people you follow haven\'t posted anything yet.' : 'Start following people to see their posts here.' }}
            </p>
            <div style="display: flex; gap: 15px; justify-content: center; margin-top: 20px;">
                <a href="{{ route('posts.index') }}" class="btn btn-secondary">
                    <i class="fas fa-globe"></i> Browse All Posts
                </a>
                <a href="{{ route('users.discover') }}" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Find People to Follow
                </a>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
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