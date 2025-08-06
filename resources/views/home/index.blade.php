@extends('layouts.app')

@section('content')
<div class="welcome-card card">
    <h2>Welcome to Social News</h2>
    <p>Hello, {{ $user->display_name }}! Your personalized news feed awaits. Share your thoughts, discover new perspectives, and connect with fellow readers.</p>
    
    <div class="action-buttons">
        <a href="#" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create Your First Post
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
                <p style="color: var(--stone-600); margin-bottom: 0; font-size: 16px; line-height: 1.6;">{{ $user->bio }}</p>
            @else
                <p style="color: var(--stone-400); margin-bottom: 0; font-style: italic; font-size: 16px;">Add your bio in settings to tell others about yourself</p>
            @endif
        </div>
    </div>
</div>

<div class="card">
    <h3 style="margin-bottom: 20px; color: var(--stone-700); display: flex; align-items: center; gap: 12px;">
        <i class="fas fa-newspaper" style="color: var(--stone-500);"></i>
        Recent Activity
    </h3>
    <div style="text-align: center; padding: 40px 20px;">
        <i class="fas fa-file-alt" style="font-size: 48px; color: var(--stone-300); margin-bottom: 16px;"></i>
        <p class="text-muted" style="font-size: 18px; margin-bottom: 24px;">No posts yet. Be the first to share something interesting!</p>
        <a href="#" class="btn btn-primary">
            <i class="fas fa-pen"></i> Write Your First Post
        </a>
    </div>
</div>
@endsection