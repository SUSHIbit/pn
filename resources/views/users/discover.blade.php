@extends('layouts.app')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="margin: 0; color: var(--stone-800);">Discover People</h2>
        <a href="{{ route('posts.feed') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Feed
        </a>
    </div>

    <div style="background-color: var(--stone-50); padding: 20px; border-radius: 12px; margin-bottom: 30px;">
        <h3 style="color: var(--stone-700); margin-bottom: 10px;">Find interesting people to follow</h3>
        <p style="color: var(--stone-600); margin-bottom: 0;">
            Connect with writers and creators who share content that interests you. Following them will add their posts to your personalized feed.
        </p>
    </div>

    @if($suggestions->count() > 0)
        @foreach($suggestions as $user)
            @include('components.user-card', ['user' => $user])
        @endforeach

        <!-- Pagination -->
        @if($suggestions->hasPages())
            <div style="display: flex; justify-content: center; margin-top: 30px;">
                {{ $suggestions->links() }}
            </div>
        @endif
    @else
        <div style="text-align: center; padding: 60px 20px;">
            <i class="fas fa-users" style="font-size: 64px; color: var(--stone-300); margin-bottom: 20px;"></i>
            <h3 style="color: var(--stone-600); margin-bottom: 15px;">No suggestions available</h3>
            <p class="text-muted">We couldn't find any new people for you to follow right now.</p>
            <a href="{{ route('posts.index') }}" class="btn btn-primary" style="margin-top: 20px;">
                <i class="fas fa-newspaper"></i> Browse All Posts
            </a>
        </div>
    @endif
</div>

@if(auth()->user()->following_count > 0)
<div class="card">
    <h3 style="margin-bottom: 20px; color: var(--stone-700);">People You Follow</h3>
    <p style="color: var(--stone-600); margin-bottom: 15px;">
        You're following {{ auth()->user()->following_count }} people.
    </p>
    <div style="display: flex; gap: 15px;">
        <a href="{{ route('users.following') }}" class="btn btn-secondary">
            <i class="fas fa-eye"></i> View Following
        </a>
        <a href="{{ route('users.followers') }}" class="btn btn-secondary">
            <i class="fas fa-users"></i> View Followers
        </a>
    </div>
</div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const followButtons = document.querySelectorAll('.follow-btn');

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

                // Show success message
                const message = document.createElement('div');
                message.style.cssText = 'position: fixed; top: 20px; right: 20px; background: #10b981; color: white; padding: 10px 20px; border-radius: 8px; z-index: 1000;';
                message.textContent = data.message;
                document.body.appendChild(message);
                
                setTimeout(() => {
                    message.remove();
                }, 3000);
            })
            .catch(error => console.error('Error:', error));
        });
    });
});
</script>
@endpush
@endsection