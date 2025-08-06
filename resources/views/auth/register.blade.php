@extends('layouts.auth')

@section('title', 'Register')

@section('content')
<div class="auth-header">
    <h1>Create Account</h1>
    <p>Join our community today</p>
</div>

@if ($errors->any())
    <div style="background-color: #fee2e2; border: 1px solid #fecaca; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
        @foreach ($errors->all() as $error)
            <p class="error-message" style="margin-bottom: 0;">{{ $error }}</p>
        @endforeach
    </div>
@endif

<form method="POST" action="{{ route('register') }}">
    @csrf
    
    <div class="form-group">
        <label for="name" class="form-label">Full Name</label>
        <input id="name" type="text" class="form-control @error('name') error @enderror" 
               name="name" value="{{ old('name') }}" required autofocus>
        @error('name')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="form-group">
        <label for="email" class="form-label">Email Address</label>
        <input id="email" type="email" class="form-control @error('email') error @enderror" 
               name="email" value="{{ old('email') }}" required>
        @error('email')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="form-group">
        <label for="password" class="form-label">Password</label>
        <input id="password" type="password" class="form-control @error('password') error @enderror" 
               name="password" required>
        @error('password')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="form-group">
        <label for="password_confirmation" class="form-label">Confirm Password</label>
        <input id="password_confirmation" type="password" class="form-control" 
               name="password_confirmation" required>
    </div>
    
    <button type="submit" class="btn btn-primary btn-full">
        Create Account
    </button>
</form>

<div class="auth-divider">
    <span>or</span>
</div>

<a href="{{ route('auth.google') }}" class="btn btn-google btn-full">
    <i class="fab fa-google"></i>
    Continue with Google
</a>

<div style="text-align: center; margin-top: 20px;">
    <p class="text-muted">Already have an account? 
        <a href="{{ route('login') }}" style="color: var(--stone-700); font-weight: 600;">Sign in</a>
    </p>
</div>
@endsection