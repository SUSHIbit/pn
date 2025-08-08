@extends('layouts.app')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="margin: 0; color: var(--stone-800);">
            @if($query)
                Search Results
            @else
                Explore & Discover
            @endif
        </h2>
        <a href="{{ route('search.advanced') }}" class="btn btn-secondary">
            <i class="fas fa-search-plus"></i> Advanced Search
        </a>
    </div>

    <!-- Search Form -->
    <form method="GET" action="{{ route('search') }}" class="search-form" style="margin-bottom: 30px;">
        <div style="display: flex; gap: 15px; align-items: center;">
            <div style="flex: 1; position: relative;">
                <input type="text" name="q" value="{{ $query }}" 
                       placeholder="Search posts, users, hashtags..." 
                       class="form-control"
                       id="search-input"
                       style="padding-right: 50px;">
                <button type="submit" 
                        style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--stone-500); cursor: pointer;">
                    <i class="fas fa-search"></i>
                </button>
                
                <!-- Search Suggestions Dropdown -->
                <div id="search-suggestions" 
                     style="position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid var(--stone-300); border-radius: 8px; margin-top: 4px; z-index: 1000; display: none; max-height: 300px; overflow-y: auto;">
                </div>
            </div>
            
            <select name="type" class="form-control" style="width: 150px;">
                <option value="all" {{ $type === 'all' ? 'selected' : '' }}>All</option>
                <option value="posts" {{ $type === 'posts' ? 'selected' : '' }}>Posts</option>
                <option value="users" {{ $type === 'users' ? 'selected' : '' }}>Users</option>
                <option value="hashtags" {{ $type === 'hashtags' ? 'selected' : '' }}>Hashtags</option>
            </select>
        </div>
    </form>

    @if($query)
        <!-- Search Results -->
        <div style="margin-bottom: 20px;">
            <p style="color: var(--stone-600);">
                Found {{ $totalResults }} result{{ $totalResults !== 1 ? 's' : '' }} for "<strong>{{ $query }}</strong>"
            </p>
        </div>

        <!-- Filter Tabs -->
        <div style="display: flex; gap: 20px; border-bottom: 1px solid var(--stone-200); margin-bottom: 30px;">
            <a href="{{ route('search', ['q' => $query, 'type' => 'all']) }}" 
               class="search-tab {{ $type === 'all' ? 'active' : '' }}">
                All ({{ $totalResults }})
            </a>
            <a href="{{ route('search', ['q' => $query, 'type' => 'posts']) }}" 
               class="search-tab {{ $type === 'posts' ? 'active' : '' }}">
                Posts ({{ $results['posts']->count() }})
            </a>
            <a href="{{ route('search', ['q' => $query, 'type' => 'users']) }}" 
               class="search-tab {{ $type === 'users' ? 'active' : '' }}">
                Users ({{ $results['users']->count() }})
            </a>
            <a href="{{ route('search', ['q' => $query, 'type' => 'hashtags']) }}" 
               class="search-tab {{ $type === 'hashtags' ? 'active' : '' }}">
                Hashtags ({{ $results['hashtags']->count() }})
            </a>
        </div>

        <!-- Search Results Content -->
        @if($type === 'all' || $type === 'posts')
            @if($results['posts']->count() > 0)
                <div style="margin-bottom: 40px;">
                    @if($type === 'all')<h3 style="margin-bottom: 20px; color: var(--stone-700);">Posts</h3>@endif
                    @foreach($results['posts'] as $post)
                        @include('components.post-card', ['post' => $post, 'showAuthor' => true])
                    @endforeach
                    @if($type === 'all' && $results['posts']->count() >= 5)
                        <div style="text-align: center; margin-top: 20px;">
                            <a href="{{ route('search', ['q' => $query, 'type' => 'posts']) }}" class="btn btn-secondary">
                                View All Posts
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        @endif

        @if($type === 'all' || $type === 'users')
            @if($results['users']->count() > 0)
                <div style="margin-bottom: 40px;">
                    @if($type === 'all')<h3 style="margin-bottom: 20px; color: var(--stone-700);">Users</h3>@endif
                    @foreach($results['users'] as $user)
                        @include('components.user-card', ['user' => $user, 'showFollowButton' => true])
                    @endforeach
                    @if($type === 'all' && $results['users']->count() >= 5)
                        <div style="text-align: center; margin-top: 20px;">
                            <a href="{{ route('search', ['q' => $query, 'type' => 'users']) }}" class="btn btn-secondary">
                                View All Users
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        @endif

        @if($type === 'all' || $type === 'hashtags')
            @if($results['hashtags']->count() > 0)
                <div style="margin-bottom: 40px;">
                    @if($type === 'all')<h3 style="margin-bottom: 20px; color: var(--stone-700);">Hashtags</h3>@endif
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px;">
                        @foreach($results['hashtags'] as $hashtag)
                            <div class="hashtag-card" style="border: 1px solid var(--stone-200); border-radius: 8px; padding: 15px;">
                                <h4 style="margin-bottom: 8px;">
                                    <a href="{{ route('search.hashtag', $hashtag->name) }}" 
                                       style="text-decoration: none; color: var(--stone-700); font-weight: 600;">
                                        #{{ $hashtag->name }}
                                    </a>
                                </h4>
                                <p style="color: var(--stone-500); font-size: 14px;">
                                    {{ $hashtag->posts_count }} post{{ $hashtag->posts_count !== 1 ? 's' : '' }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                    @if($type === 'all' && $results['hashtags']->count() >= 5)
                        <div style="text-align: center; margin-top: 20px;">
                            <a href="{{ route('search', ['q' => $query, 'type' => 'hashtags']) }}" class="btn btn-secondary">
                                View All Hashtags
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        @endif

        @if($totalResults === 0)
            <div style="text-align: center; padding: 60px 20px;">
                <i class="fas fa-search" style="font-size: 64px; color: var(--stone-300); margin-bottom: 20px;"></i>
                <h3 style="color: var(--stone-600); margin-bottom: 15px;">No results found</h3>
                <p class="text-muted" style="margin-bottom: 20px;">
                    We couldn't find anything for "{{ $query }}". Try different keywords or check your spelling.
                </p>
                <div style="display: flex; gap: 15px; justify-content: center;">
                    <a href="{{ route('posts.index') }}" class="btn btn-secondary">
                        <i class="fas fa-newspaper"></i> Browse All Posts
                    </a>
                    <a href="{{ route('users.discover') }}" class="btn btn-secondary">
                        <i class="fas fa-users"></i> Discover People
                    </a>
                </div>
            </div>
        @endif
    @else
        <!-- Trending Content (when no search query) -->
        @if(isset($trending['hashtags']) && $trending['hashtags']->count() > 0)
            <div class="trending-section" style="margin-bottom: 40px;">
                <h3 style="margin-bottom: 20px; color: var(--stone-700); display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-fire" style="color: #f59e0b;"></i>
                    Trending Hashtags
                </h3>
                <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                    @foreach($trending['hashtags'] as $hashtag)
                        <a href="{{ route('search.hashtag', $hashtag->name) }}" 
                           class="hashtag-tag"
                           style="background-color: var(--stone-100); color: var(--stone-700); padding: 8px 15px; border-radius: 20px; text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.2s;">
                            #{{ $hashtag->name }}
                            <span style="color: var(--stone-500); margin-left: 5px;">({{ $hashtag->posts_count }})</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        @if(isset($trending['posts']) && $trending['posts']->count() > 0)
            <div class="popular-posts" style="margin-bottom: 40px;">
                <h3 style="margin-bottom: 20px; color: var(--stone-700); display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-thumbs-up" style="color: #10b981;"></i>
                    Popular This Week
                </h3>
                @foreach($trending['posts'] as $post)
                    @include('components.post-card', ['post' => $post, 'showAuthor' => true])
                @endforeach
            </div>
        @endif

        @if(isset($trending['users']) && $trending['users']->count() > 0)
            <div class="active-users" style="margin-bottom: 40px;">
                <h3 style="margin-bottom: 20px; color: var(--stone-700); display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-user-friends" style="color: #6366f1;"></i>
                    Active Writers
                </h3>
                @foreach($trending['users'] as $user)
                    @include('components.user-card', ['user' => $user, 'showFollowButton' => true])
                @endforeach
            </div>
        @endif

        <!-- Quick Start Section -->
        <div style="background-color: var(--stone-50); padding: 30px; border-radius: 12px; text-align: center;">
            <h3 style="color: var(--stone-700); margin-bottom: 15px;">Discover Amazing Content</h3>
            <p style="color: var(--stone-600); margin-bottom: 25px;">
                Use the search box above to find posts, users, and hashtags that interest you.
            </p>
            <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                <a href="{{ route('posts.index') }}" class="btn btn-secondary">
                    <i class="fas fa-newspaper"></i> Browse All Posts
                </a>
                <a href="{{ route('users.discover') }}" class="btn btn-secondary">
                    <i class="fas fa-users"></i> Find People
                </a>
                <a href="{{ route('posts.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Post
                </a>
            </div>
        </div>
    @endif
</div>

@push('styles')
<style>
.search-tab {
    padding: 12px 0;
    color: var(--stone-500);
    text-decoration: none;
    border-bottom: 2px solid transparent;
    font-weight: 500;
    transition: all 0.2s;
}

.search-tab:hover {
    color: var(--stone-700);
}

.search-tab.active {
    color: var(--stone-800);
    border-bottom-color: var(--stone-800);
}

.hashtag-tag:hover {
    background-color: var(--stone-200);
    transform: translateY(-1px);
}

.hashtag-card {
    transition: all 0.2s;
}

.hashtag-card:hover {
    border-color: var(--stone-400);
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.search-suggestion {
    padding: 12px 15px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
    border-bottom: 1px solid var(--stone-100);
}

.search-suggestion:hover {
    background-color: var(--stone-50);
}

.search-suggestion:last-child {
    border-bottom: none;
}

.suggestion-avatar {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    object-fit: cover;
}

.suggestion-type {
    background-color: var(--stone-200);
    color: var(--stone-600);
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 10px;
    text-transform: uppercase;
    font-weight: 600;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const suggestionsContainer = document.getElementById('search-suggestions');
    let searchTimeout;

    // Search suggestions
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            suggestionsContainer.style.display = 'none';
            return;
        }
        
        searchTimeout = setTimeout(() => {
            fetch(`{{ route('search.suggestions') }}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(suggestions => {
                    if (suggestions.length > 0) {
                        let html = '';
                        suggestions.forEach(suggestion => {
                            if (suggestion.type === 'user') {
                                html += `
                                    <div class="search-suggestion" onclick="window.location.href='${suggestion.url}'">
                                        <img src="${suggestion.avatar}" alt="${suggestion.name}" class="suggestion-avatar">
                                        <div style="flex: 1;">
                                            <div style="font-weight: 500; color: var(--stone-800);">${suggestion.name}</div>
                                            <div style="font-size: 12px; color: var(--stone-500);">@${suggestion.username}</div>
                                        </div>
                                        <span class="suggestion-type">User</span>
                                    </div>
                                `;
                            } else if (suggestion.type === 'hashtag') {
                                html += `
                                    <div class="search-suggestion" onclick="window.location.href='${suggestion.url}'">
                                        <div style="width: 30px; height: 30px; background-color: var(--stone-200); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--stone-600);">
                                            <i class="fas fa-hashtag"></i>
                                        </div>
                                        <div style="flex: 1;">
                                            <div style="font-weight: 500; color: var(--stone-800);">${suggestion.name}</div>
                                            <div style="font-size: 12px; color: var(--stone-500);">${suggestion.count} posts</div>
                                        </div>
                                        <span class="suggestion-type">Tag</span>
                                    </div>
                                `;
                            }
                        });
                        suggestionsContainer.innerHTML = html;
                        suggestionsContainer.style.display = 'block';
                    } else {
                        suggestionsContainer.style.display = 'none';
                    }
                })
                .catch(() => {
                    suggestionsContainer.style.display = 'none';
                });
        }, 300);
    });

    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !suggestionsContainer.contains(e.target)) {
            suggestionsContainer.style.display = 'none';
        }
    });

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