{{-- resources/views/components/user-card.blade.php --}}
@props(['user', 'showFollowButton' => true])

<div class="user-card" style="display: flex; align-items: center; gap: 15px; padding: 20px; border: 1px solid var(--stone-200); border-radius: 12px; margin-bottom: 15px;">
    <img src="{{ $user->avatar_url }}" alt="{{ $user->display_name }}" 
         style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
    
    <div style="flex: 1;">
        <h4 style="margin-bottom: 5px; font-size: 16px;">
            <a href="{{ route('user.profile', $user->username) }}" 
               style="text-decoration: none; color: var(--stone-800);">
                {{ $user->display_name }}
            </a>
        </h4>
        <p style="color: var(--stone-500); font-size: 14px; margin-bottom: 5px;">
            {{ '@' . $user->username }}
        </p>
        @if($user->bio)
            <p style="color: var(--stone-600); font-size: 13px; line-height: 1.4; margin-bottom: 8px;">
                {{ Str::limit($user->bio, 100) }}
            </p>
        @endif
        <div style="display: flex; gap: 15px; color: var(--stone-500); font-size: 12px;">
            <span><strong>{{ $user->posts_count ?? 0 }}</strong> posts</span>
            <span><strong>{{ $user->followers_count ?? 0 }}</strong> followers</span>
            <span><strong>{{ $user->following_count ?? 0 }}</strong> following</span>
        </div>
    </div>
    
    @if($showFollowButton && auth()->id() !== $user->id)
        <button class="follow-btn btn {{ auth()->user()->isFollowing($user) ? 'btn-secondary' : 'btn-primary' }}" 
                style="padding: 8px 16px; font-size: 14px;"
                data-user-id="{{ $user->id }}">
            <i class="fas {{ auth()->user()->isFollowing($user) ? 'fa-user-check' : 'fa-user-plus' }}"></i>
            <span class="follow-text">{{ auth()->user()->isFollowing($user) ? 'Following' : 'Follow' }}</span>
        </button>
    @endif
</div>