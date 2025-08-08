@extends('layouts.app')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="margin: 0; color: var(--stone-800);">Create New Post</h2>
        <a href="{{ route('posts.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Posts
        </a>
    </div>

    @if($hashtag)
        <div style="background-color: #dbeafe; border: 1px solid #93c5fd; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
            <p style="margin: 0; color: #1e40af;">
                <i class="fas fa-hashtag"></i>
                Creating a post with hashtag: <strong>#{{ $hashtag }}</strong>
            </p>
        </div>
    @endif

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
                   name="image" accept="image/*" onchange="previewImage(this)">
            @error('image')
                <div class="error-message">{{ $message }}</div>
            @enderror
            <p style="font-size: 14px; color: var(--stone-500); margin-top: 5px;">
                Recommended: High-quality images (JPG, PNG, GIF) up to 2MB for best results
            </p>
            <!-- Image Preview -->
            <div id="image-preview" style="margin-top: 10px; display: none;">
                <img id="preview-img" style="max-width: 300px; height: auto; border-radius: 8px; border: 1px solid var(--stone-200);">
            </div>
        </div>

        <div class="form-group">
            <label for="content" class="form-label">Content</label>
            <textarea id="content" name="content" class="form-control @error('content') error @enderror" 
                      rows="12" required placeholder="Write your news article or story here...{{ $hashtag ? ' Don\'t forget to include #' . $hashtag . ' in your content!' : '' }}">{{ old('content', $hashtag ? '#' . $hashtag . ' ' : '') }}</textarea>
            @error('content')
                <div class="error-message">{{ $message }}</div>
            @enderror
            <p style="font-size: 14px; color: var(--stone-500); margin-top: 5px;">
                <i class="fas fa-hashtag"></i> Use hashtags like #technology #news #lifestyle to help people discover your post
            </p>
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
        <i class="fas fa-hashtag" style="color: var(--stone-500);"></i>
        Hashtag Tips
    </h3>
    <div style="color: var(--stone-600); line-height: 1.6;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
            <div>
                <h4 style="color: var(--stone-700); margin-bottom: 10px; font-size: 16px;">Popular Hashtags</h4>
                <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                    <span class="hashtag-suggestion">#technology</span>
                    <span class="hashtag-suggestion">#news</span>
                    <span class="hashtag-suggestion">#lifestyle</span>
                    <span class="hashtag-suggestion">#business</span>
                    <span class="hashtag-suggestion">#health</span>
                    <span class="hashtag-suggestion">#education</span>
                </div>
            </div>
            <div>
                <h4 style="color: var(--stone-700); margin-bottom: 10px; font-size: 16px;">Best Practices</h4>
                <ul style="margin: 0; padding-left: 16px; font-size: 14px;">
                    <li style="margin-bottom: 5px;">Use 2-5 relevant hashtags per post</li>
                    <li style="margin-bottom: 5px;">Mix popular and niche hashtags</li>
                    <li style="margin-bottom: 5px;">Create unique hashtags for campaigns</li>
                    <li>Keep hashtags simple and memorable</li>
                </ul>
            </div>
        </div>
    </div>
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
            <li style="margin-bottom: 8px;">Add relevant hashtags to increase discoverability</li>
            <li>Include a high-quality image to make your post more engaging</li>
        </ul>
    </div>
</div>

@push('styles')
<style>
.hashtag-suggestion {
    background-color: var(--stone-100);
    color: var(--stone-700);
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s;
    font-weight: 500;
}

.hashtag-suggestion:hover {
    background-color: var(--stone-200);
    transform: translateY(-1px);
}

#image-preview {
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
@endpush

@push('scripts')
<script>
// Image preview functionality
function previewImage(input) {
    const preview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        };
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const contentTextarea = document.getElementById('content');
    const hashtagSuggestions = document.querySelectorAll('.hashtag-suggestion');
    
    // Handle hashtag suggestion clicks
    hashtagSuggestions.forEach(suggestion => {
        suggestion.addEventListener('click', function() {
            const hashtag = this.textContent;
            const currentContent = contentTextarea.value;
            
            // Add hashtag if not already present
            if (!currentContent.includes(hashtag)) {
                const newContent = currentContent + (currentContent.endsWith(' ') || currentContent === '' ? '' : ' ') + hashtag + ' ';
                contentTextarea.value = newContent;
                contentTextarea.focus();
                
                // Visual feedback
                this.style.backgroundColor = 'var(--stone-800)';
                this.style.color = 'white';
                
                setTimeout(() => {
                    this.style.backgroundColor = '';
                    this.style.color = '';
                }, 1000);
            }
        });
    });
    
    // Character count for content (optional feature)
    const contentCounter = document.createElement('div');
    contentCounter.style.cssText = 'font-size: 12px; color: var(--stone-500); text-align: right; margin-top: 5px;';
    contentTextarea.parentNode.appendChild(contentCounter);
    
    function updateCharacterCount() {
        const length = contentTextarea.value.length;
        contentCounter.textContent = `${length} characters`;
        
        if (length > 5000) {
            contentCounter.style.color = '#ef4444';
        } else if (length > 4000) {
            contentCounter.style.color = '#f59e0b';
        } else {
            contentCounter.style.color = 'var(--stone-500)';
        }
    }
    
    contentTextarea.addEventListener('input', updateCharacterCount);
    updateCharacterCount(); // Initial count
    
    // Auto-resize textarea
    contentTextarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 400) + 'px';
    });
    
    // Hashtag highlighting in real-time (optional)
    let hashtagTimeout;
    contentTextarea.addEventListener('input', function() {
        clearTimeout(hashtagTimeout);
        hashtagTimeout = setTimeout(() => {
            highlightHashtags();
        }, 500);
    });
    
    function highlightHashtags() {
        const content = contentTextarea.value;
        const hashtagMatches = content.match(/#\w+/g);
        
        if (hashtagMatches && hashtagMatches.length > 0) {
            const uniqueHashtags = [...new Set(hashtagMatches)];
            console.log('Hashtags detected:', uniqueHashtags);
            // You could show detected hashtags in UI here
        }
    }
    
    // Form submission handling
    document.querySelector('form').addEventListener('submit', function(e) {
        const submitBtn = document.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Publishing...';
        
        // Re-enable if there's an error (though form will likely redirect)
        setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }, 10000);
    });
});
</script>
@endpush
@endsection