@extends('layouts.app')

@section('content')
<div class="card">
    <h2 style="margin-bottom: 30px; color: var(--stone-800);">Profile Settings</h2>
    
    @if(session('success'))
        <div style="background-color: #dcfce7; border: 1px solid #bbf7d0; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
            <p class="success-message" style="margin-bottom: 0;">{{ session('success') }}</p>
        </div>
    @endif
    
    @if ($errors->any())
        <div style="background-color: #fee2e2; border: 1px solid #fecaca; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
            @foreach ($errors->all() as $error)
                <p class="error-message" style="margin-bottom: 0;">{{ $error }}</p>
            @endforeach
        </div>
    @endif
    
    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        
        <div class="form-group">
            <label for="avatar" class="form-label">Profile Picture</label>
            <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 15px;">
                <img src="{{ $user->avatar_url }}" alt="Current Avatar" class="user-avatar large">
                <div>
                    <input type="file" id="avatar" name="avatar" accept="image/*" class="form-control" style="width: auto;">
                    <p style="font-size: 14px; color: var(--stone-500); margin-top: 5px;">
                        JPG, PNG, GIF up to 2MB
                    </p>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label for="name" class="form-label">Full Name</label>
            <input id="name" type="text" class="form-control @error('name') error @enderror" 
                   name="name" value="{{ old('name', $user->name) }}" required>
            @error('name')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="username" class="form-label">Username</label>
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="color: var(--stone-500);">@</span>
                <input id="username" type="text" class="form-control" 
                       value="{{ $user->username }}" disabled style="background-color: var(--stone-100);">
            </div>
            <p style="font-size: 14px; color: var(--stone-500); margin-top: 5px;">
                Username cannot be changed after account creation
            </p>
        </div>
        
        <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <input id="email" type="email" class="form-control" 
                   value="{{ $user->email }}" disabled style="background-color: var(--stone-100);">
            <p style="font-size: 14px; color: var(--stone-500); margin-top: 5px;">
                Email cannot be changed. Contact support if needed.
            </p>
        </div>
        
        <div class="form-group">
            <label for="bio" class="form-label">Bio</label>
            <textarea id="bio" name="bio" class="form-control @error('bio') error @enderror" 
                      rows="4" maxlength="500" placeholder="Tell us about yourself...">{{ old('bio', $user->bio) }}</textarea>
            @error('bio')
                <div class="error-message">{{ $message }}</div>
            @enderror
            <p style="font-size: 14px; color: var(--stone-500); margin-top: 5px;">
                Maximum 500 characters
            </p>
        </div>
        
        <div style="display: flex; gap: 15px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Changes
            </button>
            <a href="{{ route('profile') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>

<div class="card">
    <h3 style="margin-bottom: 20px; color: var(--stone-700);">Account Information</h3>
    <div style="color: var(--stone-600);">
        <p><strong>Account Created:</strong> {{ $user->created_at->format('F j, Y') }}</p>
        <p><strong>Last Updated:</strong> {{ $user->updated_at->format('F j, Y g:i A') }}</p>
        @if($user->google_id)
            <p><strong>Connected Services:</strong> Google Account</p>
        @endif
    </div>
</div>
@endsection