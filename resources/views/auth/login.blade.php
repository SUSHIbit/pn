@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="auth-header">
    <h1>Welcome Back</h1>
    <p>Sign in to your account</p>
</div>

@if ($errors->any())
    <div style="background-color: #fee2e2; border: 1px solid #fecaca; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
        @foreach ($errors->all() as $error)
            <p class="error-message" style="margin-bottom: 0;">{{ $error }}</p>
        @endforeach
    </div>
@endif

@if (session('error'))
    <div style="background-color: #fee2e2; border: 1px solid #fecaca; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
        <p class="error-message" style="margin-bottom: 0;">{{ session('error') }}</p>
    </div>
@endif

<form method="POST" action="{{ route('login') }}">
    @csrf
    
    <div class="form-group">
        <label for="email" class="form-label">Email Address</label>
        <input id="email" type="email" class="form-control @error('email') error @enderror" 
               name="email" value="{{ old('email') }}" required autofocus>
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
        <label style="display: flex; align-items: center; gap: 8px; font-weight: normal;">
            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
            <span>Remember me</span>
        </label>
    </div>
    
    <button type="submit" class="btn btn-primary btn-full">
        Sign In
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
    <p class="text-muted">Don't have an account? 
        <a href="{{ route('register') }}" style="color: var(--stone-700); font-weight: 600;">Sign up</a>
    </p>
</div>
@endsection