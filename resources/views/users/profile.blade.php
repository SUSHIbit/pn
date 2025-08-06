@extends('layouts.app')

@section('content')
<div class="card">
    <div style="display: flex; align-items: center; gap: 30px; margin-bottom: 30px;">
        <img src="{{ $user->avatar_url }}" alt="Profile" 
             style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid var(--stone-200);">
        
        <div style="flex: 1;">
            <h1 style="margin-bottom: 10px; color: var(--stone-800); font-size: 32px;">{{ $user->display_name }}</h1>
            <p style="margin-bottom: 15px; color: var(--stone-500); font-size: 18px;">{{ '@' . $user->username }}</p>
            
            @if($user->bio)
                <p style="color: var(--stone-600); font-size: 16px; line-height: 1.6; margin-bottom: 20px;">{{ $user->bio }}</p>
            @else
                <p style="color: var(--stone-400); font-style: italic; margin-bottom: 20px;">No bio added yet</p>
            @endif
            
            <div style="display: flex; gap: 20px; color: var(--stone-500);">
                <span><strong style="color: var(--stone-700);">0</strong> Posts</span>
                <span><strong style="color: var(--stone-700);">0</strong> Following</span>
                <span><strong style="color: var(--stone-700);">0</strong> Followers</span>
            </div>
        </div>
        
        <div>
            @if(auth()->user()->id === $user->id)
                <a href="{{ route('settings') }}" class="btn btn-secondary">
                    <i class="fas fa-edit"></i> Edit Profile
                </a>
            @else
                <button class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Follow
                </button>
            @endif
        </div>
    </div>
    
    <div style="border-top: 1px solid var(--stone-200); padding-top: 20px;">
        <h3 style="margin-bottom: 20px; color: var(--stone-700);">Posts</h3>
        <p class="text-muted text-center" style="padding: 40px 0;">No posts yet. {{ $user->id === auth()->user()->id ? 'Create your first post!' : $user->display_name . ' hasn\'t posted anything yet.' }}</p>
    </div>
</div>

@if($user->id === auth()->user()->id)
<div class="card">
    <h3 style="margin-bottom: 20px; color: var(--stone-700);">Quick Actions</h3>
    <div style="display: flex; gap: 15px; flex-wrap: wrap;">
        <a href="#" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create Post
        </a>
        <a href="{{ route('settings') }}" class="btn btn-secondary">
            <i class="fas fa-cog"></i> Settings
        </a>
        <a href="#" class="btn btn-secondary">
            <i class="fas fa-users"></i> Find People
        </a>
    </div>
</div>
@endif
@endsection