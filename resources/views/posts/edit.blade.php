@extends('layouts.app')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="margin: 0; color: var(--stone-800);">Edit Post</h2>
        <a href="{{ route('posts.show', $post) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Post
        </a>
    </div>

    @if ($errors->any())
        <div style="background-color: #fee2e2; border: 1px solid #fecaca; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
            @foreach ($errors->all() as $error)
                <p class="error-message" style="margin-bottom: 0;">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('posts.update', $post) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="title" class="form-label">Post Title</label>
            <input id="title" type="text" class="form-control @error('title') error @enderror" 
                   name="title" value="{{ old('title', $post->title) }}" required autofocus
                   placeholder="Enter an engaging title for your post">
            @error('title')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="image" class="form-label">Featured Image</label>
            
            @if($post->image)
                <div style="margin-bottom: 15px;">
                    <p style="font-size: 14px; color: var(--stone-600); margin-bottom: 10px;">Current image:</p>
                    <img src="{{ $post->image_url }}" alt="Current image" 
                         style="max-width: 300px; height: auto; border-radius: 8px; border: 1px solid var(--stone-200);">
                </div>
            @endif
            
            <input id="image" type="file" class="form-control @error('image') error @enderror" 
                   name="image" accept="image/*">
            @error('image')
                <div class="error-message">{{ $message }}</div>
            @enderror
            <p style="font-size: 14px; color: var(--stone-500); margin-top: 5px;">
                Leave empty to keep current image. Upload a new image to replace it.
            </p>
        </div>

        <div class="form-group">
            <label for="content" class="form-label">Content</label>
            <textarea id="content" name="content" class="form-control @error('content') error @enderror" 
                      rows="12" required placeholder="Write your news article or story here...">{{ old('content', $post->content) }}</textarea>
            @error('content')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 8px; font-weight: normal;">
                <input type="checkbox" name="is_published" value="1" {{ old('is_published', $post->is_published) ? 'checked' : '' }}>
                <span>Published</span>
            </label>
            <p style="font-size: 14px; color: var(--stone-500); margin-top: 5px;">
                Uncheck to save as draft
            </p>
        </div>

        <div style="display: flex; gap: 15px; border-top: 1px solid var(--stone-200); padding-top: 20px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Post
            </button>
            <a href="{{ route('posts.show', $post) }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>
@endsection