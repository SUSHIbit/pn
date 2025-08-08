@extends('layouts.app')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="margin: 0; color: var(--stone-800);">Advanced Search Results</h2>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('search.advanced') }}" class="btn btn-secondary">
                <i class="fas fa-edit"></i> Modify Search
            </a>
            <a href="{{ route('search') }}" class="btn btn-secondary">
                <i class="fas fa-search"></i> Basic Search
            </a>
        </div>
    </div>

    <!-- Search Summary -->
    <div style="background-color: var(--stone-50); padding: 20px; border-radius: 12px; margin-bottom: 30px;">
        <h3 style="color: var(--stone-700); margin-bottom: 15px;">Search Criteria</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            @if($request->filled('keywords'))
                <div>
                    <strong style="color: var(--stone-800);">Keywords:</strong>
                    <span style="color: var(--stone-600);">{{ $request->input('keywords') }}</span>
                </div>
            @endif
            
            @if($request->filled('author'))
                <div>
                    <strong style="color: var(--stone-800);">Author:</strong>
                    <span style="color: var(--stone-600);">{{ $request->input('author') }}</span>
                </div>
            @endif
            
            @if($request->filled('hashtags'))
                <div>
                    <strong style="color: var(--stone-800);">Hashtags:</strong>
                    <span style="color: var(--stone-600);">#{{ str_replace(',', ', #', $request->input('hashtags')) }}</span>
                </div>
            @endif
            
            @if($request->filled('date_from') || $request->filled('date_to'))
                <div>
                    <strong style="color: var(--stone-800);">Date Range:</strong>
                    <span style="color: var(--stone-600);">
                        @if($request->filled('date_from') && $request->filled('date_to'))
                            {{ \Carbon\Carbon::parse($request->input('date_from'))->format('M j, Y') }} - {{ \Carbon\Carbon::parse($request->input('date_to'))->format('M j, Y') }}
                        @elseif($request->filled('date_from'))
                            Since {{ \Carbon\Carbon::parse($request->input('date_from'))->format('M j, Y') }}
                        @else
                            Until {{ \Carbon\Carbon::parse($request->input('date_to'))->format('M j, Y') }}
                        @endif
                    </span>
                </div>
            @endif
            
            @if($request->filled('sort_by'))
                <div>
                    <strong style="color: var(--stone-800);">Sort:</strong>
                    <span style="color: var(--stone-600);">
                        @switch($request->input('sort_by'))
                            @case('newest') Newest First @break
                            @case('oldest') Oldest First @break
                            @case('most_liked') Most Liked @break
                            @case('most_commented') Most Commented @break
                            @default Newest First
                        @endswitch
                    </span>
                </div>
            @endif
        </div>
        
        @if($request->has(['has_image', 'has_comments', 'popular_only']))
            <div style="margin-top: 15px;">
                <strong style="color: var(--stone-800);">Filters:</strong>
                <div style="display: flex; gap: 10px; margin-top: 8px; flex-wrap: wrap;">
                    @if($request->has('has_image'))
                        <span style="background-color: var(--stone-200); color: var(--stone-700); padding: 4px 8px; border-radius: 6px; font-size: 12px;">
                            Has Images
                        </span>
                    @endif
                    @if($request->has('has_comments'))
                        <span style="background-color: var(--stone-200); color: var(--stone-700); padding: 4px 8px; border-radius: 6px; font-size: 12px;">
                            Has Comments
                        </span>
                    @endif
                    @if($request->has('popular_only'))
                        <span style="background-color: var(--stone-200); color: var(--stone-700); padding: 4px 8px; border-radius: 6px; font-size: 12px;">
                            Popular Posts
                        </span>
                    @endif
                </div>
            </div>
        @endif
        
        <div style="margin-top: 15px;">
            <strong style="color: var(--stone-800);">Results:</strong>
            <span style="color: var(--stone-600);">{{ $posts->total() }} post{{ $posts->total() !== 1 ? 's' : '' }} found</span>
        </div>
    </div>

    <!-- Results -->
    @if($posts->count() > 0)
        @foreach($posts as $post)
            @include('components.post-card', ['post' => $post, 'showAuthor' => true])
        @endforeach

        <!-- Pagination -->
        @if($posts->hasPages())
            <div style="display: flex; justify-content: center; margin-top: 30px;">
                {{ $posts->appends(request()->query())->links() }}
            </div>
        @endif
    @else
        <div style="text-align: center; padding: 60px 20px;">
            <i class="fas fa-search" style="font-size: 64px; color: var(--stone-300); margin-bottom: 20px;"></i>
            <h3 style="color: var(--stone-600); margin-bottom: 15px;">No results found</h3>
            <p class="text-muted" style="margin-bottom: 20px;">
                Try adjusting your search criteria or use broader terms.
            </p>
            <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                <a href="{{ route('search.advanced') }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Modify Search
                </a>
                <a href="{{ route('search') }}" class="btn btn-secondary">
                    <i class="fas fa-search"></i> Basic Search
                </a>
                <a href="{{ route('posts.index') }}" class="btn btn-secondary">
                    <i class="fas fa-newspaper"></i> Browse All Posts
                </a>
            </div>
        </div>
    @endif
</div>

<!-- Search Suggestions -->
@if($posts->count() === 0)
    <div class="card">
        <h3 style="margin-bottom: 20px; color: var(--stone-700); display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-lightbulb" style="color: var(--stone-500);"></i>
            Search Suggestions
        </h3>
        <div style="color: var(--stone-600); line-height: 1.6;">
            <ul style="margin: 0; padding-left: 20px;">
                <li style="margin-bottom: 8px;">Try using fewer keywords or broader terms</li>
                <li style="margin-bottom: 8px;">Check your spelling and try alternative keywords</li>
                <li style="margin-bottom: 8px;">Remove some filters to see more results</li>
                <li style="margin-bottom: 8px;">Try searching for popular hashtags like #news, #technology, or #lifestyle</li>
                <li>Browse categories or use the basic search for general exploration</li>
            </ul>
        </div>
    </div>
@endif

<!-- Related Searches -->
@if($posts->count() > 0)
    <div class="card">
        <h3 style="margin-bottom: 20px; color: var(--stone-700); display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-link" style="color: var(--stone-500);"></i>
            Related Searches
        </h3>
        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
            @if($request->filled('keywords'))
                @php
                    $keywords = explode(' ', $request->input('keywords'));
                    $relatedTerms = ['latest', 'trending', 'popular', 'recent', 'top'];
                @endphp
                @foreach(array_slice($relatedTerms, 0, 3) as $term)
                    <a href="{{ route('search', ['q' => $term . ' ' . $request->input('keywords')]) }}" 
                       class="related-search">
                        {{ ucfirst($term) }} {{ $request->input('keywords') }}
                    </a>
                @endforeach
            @endif
            
            @if($request->filled('hashtags'))
                @php
                    $hashtags = explode(',', $request->input('hashtags'));
                @endphp
                @foreach(array_slice($hashtags, 0, 2) as $hashtag)
                    <a href="{{ route('search.hashtag', trim($hashtag)) }}" 
                       class="related-search">
                        #{{ trim($hashtag) }}
                    </a>
                @endforeach
            @endif
            
            <a href="{{ route('search', ['type' => 'users']) }}" class="related-search">
                Find Users
            </a>
            <a href="{{ route('search', ['type' => 'hashtags']) }}" class="related-search">
                Browse Hashtags
            </a>
        </div>
    </div>
@endif

@push('styles')
<style>
.related-search {
    background-color: var(--stone-100);
    color: var(--stone-700);
    padding: 6px 12px;
    border-radius: 16px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s;
}

.related-search:hover {
    background-color: var(--stone-200);
    transform: translateY(-1px);
}

.search-criteria {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    background-color: var(--stone-50);
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 30px;
}

.criteria-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.criteria-label {
    font-weight: 600;
    color: var(--stone-800);
    font-size: 14px;
}

.criteria-value {
    color: var(--stone-600);
    font-size: 14px;
}

.filter-tag {
    background-color: var(--stone-200);
    color: var(--stone-700);
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Social interaction handlers
    const likeButtons = document.querySelectorAll('.like-btn');
    const followButtons = document.querySelectorAll('.follow-btn');

    likeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.dataset.postId;
            const likeText = this.querySelector('.like-text');
            const likeCount = this.querySelector('.like-count');

            fetch(`/posts/${postId}/like`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                likeText.textContent = data.liked ? 'Liked' : 'Like';
                likeCount.textContent = data.likes_count;
                
                if (data.liked) {
                    this.style.backgroundColor = '#dc2626';
                    this.style.color = 'white';
                } else {
                    this.style.backgroundColor = '';
                    this.style.color = '';
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });

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
            })
            .catch(error => console.error('Error:', error));
        });
    });
});
</script>
@endpush
@endsection