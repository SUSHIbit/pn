@extends('layouts.app')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="margin: 0; color: var(--stone-800);">Create New Post</h2>
        <a href="{{ route('posts.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Posts
        </a>
    </div>

    @if ($errors->any())
        <div style="background-color: #fee2e2; border: 1px solid #fecaca; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
            @foreach ($errors->all() as $error)
                <p class="error-message" style="margin-bottom: 0;">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data">
        @csrf
        
        <div class="form-group">
            <label for="title" class="form-label">Post Title</label>
            <input id="title" type="text" class="form-control @error('title') error @enderror" 
                   name="title" value="{{ old('title') }}" required autofocus
                   placeholder="Enter an engaging title for your post">
            @error('title')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="image" class="form-label">Featured Image (Optional)</label>
            <input id="image" type="file" class="form-control @error('image') error @enderror" 
                   name="image" accept="image/*">
            @error('image')
                <div class="error-message">{{ $message }}</div>
            @enderror
            <p style="font-size: 14px; color: var(--stone-500); margin-top: 5px;">
                Recommended: High-quality images (JPG, PNG, GIF) up to 2MB for best results
            </p>
        </div>

        <div class="form-group">
            <label for="content" class="form-label">Content</label>
            <textarea id="content" name="content" class="form-control @error('content') error @enderror" 
                      rows="12" required placeholder="Write your news article or story here...">{{ old('content') }}</textarea>
            @error('content')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 8px; font-weight: normal;">
                <input type="checkbox" name="is_published" value="1" {{ old('is_published', true) ? 'checked' : '' }}>
                <span>Publish immediately</span>
            </label>
            <p style="font-size: 14px; color: var(--stone-500); margin-top: 5px;">
                Uncheck to save as draft
            </p>
        </div>

        <div style="display: flex; gap: 15px; border-top: 1px solid var(--stone-200); padding-top: 20px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane"></i> Publish Post
            </button>
            <a href="{{ route('posts.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>

<div class="card">
    <h3 style="margin-bottom: 20px; color: var(--stone-700); display: flex; align-items: center; gap: 12px;">
        <i class="fas fa-lightbulb" style="color: var(--stone-500);"></i>
        Writing Tips
    </h3>
    <div style="color: var(--stone-600); line-height: 1.6;">
        <ul style="margin: 0; padding-left: 20px;">
            <li style="margin-bottom: 8px;">Start with a compelling headline that grabs attention</li>
            <li style="margin-bottom: 8px;">Include relevant details: who, what, when, where, why</li>
            <li style="margin-bottom: 8px;">Write in a clear, concise manner that's easy to read</li>
            <li style="margin-bottom: 8px;">Use short paragraphs for better readability</li>
            <li>Add a high-quality image to make your post more engaging</li>
        </ul>
    </div>
</div>
@endsection